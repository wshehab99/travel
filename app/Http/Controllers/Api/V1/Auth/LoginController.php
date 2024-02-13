<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $user = User::where("email", $request->email)->first();
        if (!$user ||! Hash::check($request->password,$user->password)) 
        {
            return response()->json([
                'message' => "The provided credentials are incorrect",
                "errors"=> ["The provided credentials are incorrect",]
            ],422);
        }
        $device = substr($request->userAgent()??'',0,255);
        return [
            'user' => $user,
            'token' => $user->createToken($device)->plainTextToken,
        ];
    }
}
