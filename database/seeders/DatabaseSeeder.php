<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $counter1 = Counter::create(['name' => 'Kasir', 'prefix' => 'A']);
        Counter::create(['name' => 'Konsultasi', 'prefix' => 'B']);

        User::create([
            'name' => 'Pemilik Toko',
            'email' => 'admin@antrianq.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Operator Kasir',
            'email' => 'operator@antrianq.test',
            'password' => 'password',
            'role' => 'operator',
            'counter_id' => $counter1->id,
        ]);
    }
}
