<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

class AuthService
{
    const KEY = "8abd443a8870ef5f2b36b995a325d219";

    public function verifyPassword($provided, $real) : bool{
        return Hash::check($provided, $real);
    }

    public function generateToken($user): string
    {
        return $user->createToken(self::KEY)->plainTextToken;
    }
}
