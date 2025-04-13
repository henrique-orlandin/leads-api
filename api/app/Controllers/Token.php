<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use \Firebase\JWT\JWT;

class Token extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $key = getenv('api.jwt_secret');
        $iat = time();
        $exp = $iat + 60 * 60 * 24; // 1 day expiration
 
        $payload = array(
            "iat" => $iat,
            "exp" => $exp,
        );
        
        try {
            $token = JWT::encode($payload, $key, 'HS256');
     
            $response = [
                'token' => $token
            ];
        } catch (\Exception $e) {
            return $this->fail('Failed to generate token', 500);
        }

        return $this->respond($response, 200);
    }
}
