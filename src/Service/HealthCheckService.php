<?php

namespace App\Service;

use App\Enum\HttpStatus;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class HealthCheckService
{

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function checkEndpoints(array $endpoints): array
    {
        $results = [];
        $responses = [];

        // Initiate all requests concurrently
        foreach ($endpoints as $name => $url) {
            try {
                $responses[$name] = $this->httpClient->request('GET', $url, [
                    'timeout' => 10, // Timeout per request in seconds
                ]);
            } catch (TransportExceptionInterface $e) {
                return $this->returnError($e);
            }
        }

        // Process responses asynchronously
        foreach ($responses as $name => $response) {
            try {
                $results[$name] = $this->buildResponse($response);
            } catch (Exception $e) {
                return $this->returnError($e);
            }
        }

        return $results;
    }

    public function checkEndpoint(string $url): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 10, // Timeout per request in seconds
            ]);

            return $this->buildResponse($response);
        } catch (Exception | TransportExceptionInterface $e) {
            return $this->returnError($e);
        }
    }

    private function buildResponse(ResponseInterface $response): array
    {
        try {
            $content = $response->toArray();

            if (isset($content['status'])) {
                $health = $content['status'] !== 'error' ? 'OK' : $content['message'] ?? 'FAIL';
            } else {
                $health = 'FAIL';
            }

            $status = HttpStatus::tryFrom($response->getStatusCode()) ?? HttpStatus::INTERNAL_SERVER_ERROR;
            return [
                'status' => $status,
                'health' => $health,
                'endpoint' => $response->getInfo('url'),
                'response' => json_encode($content , JSON_PRETTY_PRINT),
            ];
        } catch (\Throwable $e) {
            return $this->returnError($e);
        }
    }

    private function returnError(Exception $exception): array
    {
        return [
            'status' => HttpStatus::INTERNAL_SERVER_ERROR,
            'response' => $exception->getMessage(),
        ];
    }
}
