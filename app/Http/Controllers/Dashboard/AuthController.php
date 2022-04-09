<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        if (!$user) {
            return ResponseHelper::notAuthenticated();
        }

        if (Hash::check($request->get('password'), $user->password)) {
            $token = $user->createToken('auth');
            $user = [
                'name'  => $user->name,
                'email' => $user->email,
                'token' => $token->plainTextToken,
            ];
            return ResponseHelper::make($user, 'Login Successfully', true, 202);
        } else {
            return ResponseHelper::notAuthenticated();
        }
    }

    public function notAuth()
    {
        return ResponseHelper::notAuthenticated();
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return ResponseHelper::make(null, 'Logout Successfully', true, 202);
    }
}
