<?php

namespace App\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;



class JwtService
{
    private static $secret = "Cette_Phrase_Est_Tres_Longue_Et_Securisee_Pour_Mon_SAE_2026!";    

    public static function generate(array $payload): string
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + 3600;

        return JWT::encode($payload, self::$secret, 'HS256');
    }

    public static function decode(string $token)
    {
        return JWT::decode($token, new Key(self::$secret, 'HS256'));
    }
}