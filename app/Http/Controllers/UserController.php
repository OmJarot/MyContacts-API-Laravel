<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    function register(UserRegisterRequest $request): JsonResponse {
        $data = $request->validated();

        if (User::query()->where("username", "=", $data["username"])->count() == 1){
            throw new HttpResponseException(response([
                "errors" => [
                    "username" => [
                        "username already registered"
                    ]
                ]
            ],400));
        }
        $user = new User($data);
        $user->password = Hash::make($data["password"]);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    function login(UserLoginRequest $request): UserResource {
        $data = $request->validated();

        $user = User::query()->where("username", "=", $data["username"])->first();
        if (!$user || !Hash::check($data["password"], $user->password)){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "username or password is wrong"
                    ]
                ]
            ],401));
        }
        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    function get(Request $request): UserResource {
        $user = Auth::user();
        return new UserResource($user);
    }

    function update(UserUpdateRequest $request): UserResource {
        $data = $request->validated();
        $user = Auth::user();

        if (isset($data["name"])){
            $user->name = $data["name"];
        }
        if (isset($data["password"])){
            $user->password = $data["password"];
        }

        $user->save();
        return new UserResource($user);
    }

    function logout(Request $request): JsonResponse {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return \response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
