<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreate(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();
        $this->post("/api/contacts/$contact->id/addresses",
            [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "postal_code" => "12334",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(201)
            ->assertJson(
                [
                    "data" => [
                        "street" => "test",
                        "city" => "test",
                        "province" => "test",
                        "country" => "test",
                        "postal_code" => "12334",
                    ]
                ]
            );

    }

    public function testCreateFailed(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();
        $this->post("/api/contacts/$contact->id/addresses",
            [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "",
                "postal_code" => "12334",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400)
            ->assertJson(
                [
                    "errors" => [
                        "country" => [
                            "The country field is required."
                        ]
                    ]
                ]
            );
    }

    public function testCreateContactNotFound(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->first();
        $this->post("/api/contacts/".($contact->id + 1)."/addresses",
            [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "postal_code" => "12334",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson(
                [
                    "errors" => [
                        "message" => [
                            "Not found"
                        ]
                    ]
                ]
            );
    }


}
