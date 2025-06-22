<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
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

    public function testGetSuccess(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();

        $this->get("/api/contacts/$contact->id",
            headers: ["Authorization" => "test"]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "test",
                    "last_name" => "test",
                    "email" => "test@test.com",
                    "phone" => "1234",
                ]
            ]);
    }

    public function testGetNotFound(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();

        $this->get("/api/contacts/".($contact->id + 1),
            headers: ["Authorization" => "test"]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }

    public function testGetOtherUserContact(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();

        $this->get("/api/contacts/".$contact->id,
            headers: ["Authorization" => "test2"]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();

        $this->put("/api/contacts/$contact->id",
            [
                "first_name" => "test2",
                "last_name" => "test2",
                "email" => "test2@test.com",
                "phone" => "12342",
            ],
            headers: ["Authorization" => "test"]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "test2",
                    "last_name" => "test2",
                    "email" => "test2@test.com",
                    "phone" => "12342"
                ]
            ]);

    }

    public function testUpdateValidationError(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();

        $this->put("/api/contacts/$contact->id",
            [
                "first_name" => "",
                "last_name" => "test2",
                "email" => "test2@test.com",
                "phone" => "12342",
            ],
            headers: ["Authorization" => "test"]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();

        $this->delete("/api/contacts/$contact->id",
            headers: ["Authorization" => "test"]
        )->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testDeleteNotFound(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();

        $this->delete("/api/contacts/".($contact->id+1),
            headers: ["Authorization" => "test"]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }


}
