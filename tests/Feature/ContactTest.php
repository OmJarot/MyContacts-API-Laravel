<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess(): void {
        $this->seed([UserSeeder::class]);
        $this->post("/api/contacts", [
            "first_name" => "Piter",
            "last_name" => "Pangaribuan",
            "email" => "piter@test.com",
            "phone" => "123"
        ],
            ["Authorization" => "test"])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "first_name" => "Piter",
                    "last_name" => "Pangaribuan",
                    "email" => "piter@test.com",
                    "phone" => "123"
                ]
            ]);
    }

    public function testCreateFailed(): void {
        $this->seed([UserSeeder::class]);
        $this->post("/api/contacts", [
            "first_name" => "",
            "last_name" => "Pangaribuan",
            "email" => "piter",
            "phone" => "123"
        ],
            ["Authorization" => "test"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ],
                    "email" => [
                        "The email field must be a valid email address."
                    ]
                ]
            ]);
    }

    public function testCreateUnauthorized(): void {
        $this->seed([UserSeeder::class]);
        $this->post("/api/contacts", [
            "first_name" => "",
            "last_name" => "Pangaribuan",
            "email" => "piter",
            "phone" => "123"
        ],
            ["Authorization" => "salah"])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }


}
