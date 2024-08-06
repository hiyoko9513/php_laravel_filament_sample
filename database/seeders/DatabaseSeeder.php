<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create(['personal_id' => '123456']);
        User::factory()->create(['personal_id' => '123']);
        User::factory()->create(['personal_id' => '001']);
    }
}
