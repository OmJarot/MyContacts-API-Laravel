<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where("username", "=", "test")->first();
        Contact::query()->create([
            "first_name" => "test",
            "last_name" => "test",
            "email" => "test@test.com",
            "phone" => "1234",
            "user_id" => $user->id
        ]);
    }
}
