<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess(): void {
        $this->post("/api/users",[
            "username" => "piter",
            "password" => "rahasia",
            "name" => "Piter Pangaribuan"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "piter",
                    "name" => "Piter Pangaribuan"
                ]
            ]);
    }

    public function testRegisterFailed(): void {
        $this->post("/api/users",[
            "username" => "",
            "password" => "",
            "name" => ""
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExist(): void {
        User::query()->create([
            "username" => "piter",
            "password" => "rahasia",
            "name" => "Piter Pangaribuan"
        ]);

        $this->post("/api/users",[
            "username" => "piter",
            "password" => "rahasia",
            "name" => "Piter Pangaribuan"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "username already registered"
                    ],
                ]
            ]);
    }


}
