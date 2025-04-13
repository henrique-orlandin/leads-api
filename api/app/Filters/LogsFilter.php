<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\LogsModel;

class LogsFilter implements FilterInterface
{
    private $logsId;

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
        $requestData = service('request');

        // Add the request data to the logs
        $logsModel = new LogsModel();
        $logData = [
            'ip' => $request->getIPAddress(),
            'uri' => $requestData->getUri()->getRoutePath(),
            'method' => $request->getMethod(),
            'headers' => json_encode($requestData->headers()),
            'body' => json_encode($requestData->getJSON()),
        ];

        try {
            if ($logsModel->insert($logData)) {
                $this->logsId = $logsModel->insertID();
            }
        } catch (\Exception $e) {
            $error = json_encode([
                "status" => 500,
                "error" => 500,
                "messages" => [
                    "error" => "An unexpected error happened! Please try again or contact us."
                ]
            ]);
            $response = service('response');
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody($error);
            $response->setStatusCode(500);

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
        $responseData = service('response');

        // Add the response data to the logs
        $logsModel = new LogsModel();
        $logData = [
            'response' => json_encode($responseData->getBody()),
            'status' => $responseData->getStatusCode(),
        ];

        try {
            $logsModel->update($this->logsId, $logData);
        } catch (\Throwable $th) {
            $error = json_encode([
                "status" => 500,
                "error" => 500,
                "messages" => [
                    "error" => "An unexpected error happened! Please try again or contact us."
                ]
            ]);
            $response = service('response');
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody($error);
            $response->setStatusCode(500);

            return $response;
        }
    }
}
