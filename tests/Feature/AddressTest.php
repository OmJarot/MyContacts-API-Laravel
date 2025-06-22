<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
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

    public function testGetSuccess(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->first();

        $this->get("/api/contacts/$address->contact_id/addresses/$address->id",
            headers: [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "test",
                    "city" => "test",
                    "province" => "test",
                    "country" => "test",
                    "postal_code" => "12343"
                ]
            ]);
    }

    public function testGetNotFound(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->first();

        $this->get("/api/contacts/$address->contact_id/addresses/".($address->id+1),
            headers: [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->first();

        $this->put("/api/contacts/$address->contact_id/addresses/$address->id",
            [
                "street" => "update",
                "city" => "update",
                "province" => "update",
                "country" => "update",
                "postal_code" => "22222",
            ],
            headers: [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "update",
                    "city" => "update",
                    "province" => "update",
                    "country" => "update",
                    "postal_code" => "22222"
                ]
            ]);
    }

    public function testUpdateFailed(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->first();

        $this->put("/api/contacts/$address->contact_id/addresses/$address->id",
            [
                "street" => "update",
                "city" => "update",
                "province" => "update",
                "country" => "",
                "postal_code" => "22222",
            ],
            headers: [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => [
                        "The country field is required."
                    ]
                ]
            ]);
    }

    public function testUpdateNotFound(): void {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->first();

        $this->put("/api/contacts/$address->contact_id/addresses/".($address->id +1),
            [
                "street" => "update",
                "city" => "update",
                "province" => "update",
                "country" => "update",
                "postal_code" => "22222",
            ],
            headers: [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }


}
