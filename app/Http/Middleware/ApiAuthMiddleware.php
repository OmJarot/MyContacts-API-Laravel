<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("Authorization");

        $authenticate = true;

        if (!$token){
            $authenticate = false;
        }
        $user = User::query()->where("token", "=", $token)->first();
        if (!$user){
            $authenticate = false;
        }else{
            Auth::login($user);//simpan data usernya
        }
        if ($authenticate){
            return $next($request);
        }else{
            Log::info("false");
            return \response()->json([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
