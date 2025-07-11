<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
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

    public function testLoginSuccess(): void {
        $this->seed([UserSeeder::class]);

        $this->post("/api/users/login",[
            "username" => "test",
            "password" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test",
                ]
            ]);
        $user = User::query()->where("username", "=", "test")->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound(): void {
        $this->post("/api/users/login",[
            "username" => "test",
            "password" => "test"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password is wrong"
                    ]
                ]
            ]);
    }
    public function testLoginFailedPasswordWrong(): void {
        $this->seed([UserSeeder::class]);
        $this->post("/api/users/login",[
            "username" => "test",
            "password" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password is wrong"
                    ]
                ]
            ]);
    }

    public function testGetSuccess(): void {
        $this->seed([UserSeeder::class]);
        $this->get("/api/users/current", ["Authorization" => "test"])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);
    }

    public function testGetUnauthorized(): void {
        $this->seed([UserSeeder::class]);
        $this->get("/api/users/current")
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testInvalidToken(): void {
        $this->seed([UserSeeder::class]);
        $this->get("/api/users/current", ["Authorization" => "salah"])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testUpdatePasswordSuccess(): void {
        $this->seed([UserSeeder::class]);
        $oldUser = User::query()->where("username", "test")->first();

        $this->patch("/api/users/current",
            [
                "password" => "baru"
            ],
            ["Authorization" => "test"]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);
        $newUser = User::query()->where("username", "test")->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess(): void {
        $this->seed([UserSeeder::class]);
        $oldUser = User::query()->where("username", "test")->first();

        $this->patch("/api/users/current",
            [
                "name" => "baru"
            ],
            ["Authorization" => "test"]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "baru"
                ]
            ]);
        $newUser = User::query()->where("username", "test")->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed(): void {
        $this->seed([UserSeeder::class]);

        $this->patch("/api/users/current",
            [
                "name" => "barubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubarubaru"
            ],
            ["Authorization" => "test"]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess(): void {
        $this->seed([UserSeeder::class]);

        $this->delete("/api/users/logout", headers: ["Authorization" => "test"]
        )->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $user = User::where("username", "=", "test")->first();
        self::assertNull($user->token);
    }

    public function testLogoutFailed(): void {
        $this->seed([UserSeeder::class]);
        $this->delete("/api/users/logout", headers: [
            "Authorization" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }


}
