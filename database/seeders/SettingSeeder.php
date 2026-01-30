<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('settings')->insert([
            'user_id' => 1,
            'key' => 'gold_stock',
            'value' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
