<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

#[Fillable(['queue_number', 'prefix', 'number', 'counter_id', 'status', 'customer_name', 'called_at', 'completed_at'])]
class Queue extends Model
{
    public const STATUS_WAITING = 'waiting';

    public const STATUS_CALLED = 'called';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_SKIPPED = 'skipped';

    protected function casts(): array
    {
        return [
            'called_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING)->orderBy('id');
    }

    /**
     * Nomor antrean berurutan per kategori, mulai dari 1 setiap hari.
     */
    public static function issue(string $prefix, ?string $customerName = null): self
    {
        return DB::transaction(function () use ($prefix, $customerName) {
            $latest = static::today()
                ->where('prefix', $prefix)
                ->lockForUpdate()
                ->orderByDesc('number')
                ->first();

            $number = $latest ? ((int) $latest->number + 1) : 1;

            return static::create([
                'queue_number' => $prefix.'-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'prefix' => $prefix,
                'number' => $number,
                'customer_name' => $customerName,
            ]);
        }, 3);
    }

    public function waitingAhead(): int
    {
        return static::today()
            ->waiting()
            ->where('prefix', $this->prefix)
            ->where('id', '<', $this->id)
            ->count();
    }

    public function estimatedMinutes(): int
    {
        return $this->waitingAhead() * (int) config('antrianq.avg_service_minutes', 6);
    }

    /**
     * Payload tampilan TV. Dipakai event broadcast dan fallback polling.
     *
     * @return array<string, mixed>
     */
    public static function displayState(): array
    {
        $called = static::today()
            ->whereNotNull('called_at')
            ->with('counter')
            ->orderByDesc('called_at')
            ->take(3)
            ->get();

        return [
            'current' => $called->first() ? static::displayItem($called->first()) : null,
            'previous' => $called->skip(1)->take(2)->map(fn (self $queue) => static::displayItem($queue))->values()->all(),
            'waiting' => static::today()->waiting()->take(6)->pluck('queue_number')->all(),
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private static function displayItem(self $queue): array
    {
        return [
            'key' => (string) ($queue->called_at?->getTimestamp() ?? $queue->id),
            'number' => $queue->queue_number,
            'counter' => $queue->counter?->name,
        ];
    }
}
