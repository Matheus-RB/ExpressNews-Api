<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    //use HttpResponses;

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = $request->user();
            $userData = [
                'id' => $user->id,
                'nome' => $user->name,
                'avatar' => $user->avatar,
                'role' => $user->role
            ];

            return response([
                'token' => $user->createToken('invoice')->plainTextToken,
                'user' => $userData
            ]);
        }

        return response('Not Authorized', 403);
    }


    public function logout(Request $request)
    {
      $request->user()->currentAccessToken()->delete();
      return response('Token Revoked', 200);
    }
}
