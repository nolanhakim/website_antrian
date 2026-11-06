<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number', 8);
            $table->string('prefix', 2);
            $table->unsignedInteger('number');
            $table->foreignId('counter_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('waiting')->index();
            $table->string('customer_name')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['prefix', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
