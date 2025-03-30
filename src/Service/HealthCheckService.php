<?php

namespace App\Service;

use Exception;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
                $results[$name] = [
                    'status' => 'ERROR',
                    'response' => $e->getMessage(),
                ];
            }
        }

        // Process responses asynchronously
        foreach ($responses as $name => $response) {
            try {
                $statusCode = $response->getStatusCode();
                $content = $response->getContent(); // generic exception catches all errors

                $results[$name] = [
                    'status' => $statusCode === 200 ? 'OK' : 'FAIL',
                    'response' => $content,
                ];
            } catch (Exception | TransportExceptionInterface $e) {
                $results[$name] = [
                    'status' => 'ERROR',
                    'response' => $e->getMessage(),
                ];
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

            $statusCode = $response->getStatusCode();
            $content = $response->getContent();

            return [
                'status' => $statusCode === 200 ? 'OK' : 'FAIL',
                'response' => $content,
            ];
        } catch (Exception | TransportExceptionInterface $e) {
            return [
                'status' => 'ERROR',
                'response' => $e->getMessage(),
            ];
        }
    }
}
