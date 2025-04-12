<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use \Firebase\JWT\JWT;

class Token extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $key = JWT_SECRET;
        $iat = time();
        $exp = $iat + 3600;
 
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
            $response = [
                'error' => 'Failed to generate token',
                'message' => $e->getMessage()
            ];
            return $this->fail($response, 500);
        }

        return $this->respond($response, 200);
    }
}
