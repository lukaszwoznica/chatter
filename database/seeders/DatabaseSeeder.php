<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->createAdminAndTestUser();
        User::factory()->count(20)->create();
        Message::factory()->count(10000)->create();
    }

    private function createAdminAndTestUser()
    {
        User::factory()->admin()->create([
            'email' => 'admin@chatter.xyz'
        ]);
        User::factory()->create([
            'email' => 'user@chatter.xyz'
        ]);
    }
}
