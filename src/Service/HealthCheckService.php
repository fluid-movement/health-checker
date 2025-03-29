<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HealthCheckService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function checkEndpoints(array $endpoints): array
    {
        $results = [];

        foreach ($endpoints as $name => $url) {
            try {
                $response = $this->httpClient->request('GET', $url);
                $statusCode = $response->getStatusCode();

                $results[$name] = [
                    'status' => $statusCode === 200 ? 'OK' : 'FAIL',
                    'response' => $response->getContent(),
                ];
            } catch (\Exception $e) {
                $results[$name] = [
                    'status' => 'ERROR',
                    'response' => $e->getMessage(),
                ];
            } catch (TransportExceptionInterface $e) {
                $results[$name] = [
                    'status' => 'ERROR',
                    'response' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}