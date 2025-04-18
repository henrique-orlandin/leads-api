<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('api.jwt_secret');
        $header = $request->getHeaderLine("Authorization");
        $token = null;
  
        // extract the token from the header
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }
  
        $error = json_encode([
            "status" => 401,
            "error" => 401,
            "messages" => [
                "error" => "Unauthorized access! Please provide a valid token."
            ]
        ]);
        $errorCode = 401;

        // check if token is null or empty
        if (is_null($token) || empty($token)) {
            $response = service('response');
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody($error);
            $response->setStatusCode($errorCode);

            return $response;
        }
  
        try {
            JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $ex) {
            $response = service('response');
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody($error);
            $response->setStatusCode($errorCode);

            return $response;
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}
