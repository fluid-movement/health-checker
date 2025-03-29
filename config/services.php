<?php

use App\Service\ConfigService;
use Slim\Views\Twig;
use Symfony\Component\HttpClient\HttpClient;

return [
    Twig::class => fn() => Twig::create(__DIR__ . '/../src/View/templates', ['cache' => false]),
    Symfony\Contracts\HttpClient\HttpClientInterface::class => fn() => HttpClient::create(),
    ConfigService::class => fn() => new ConfigService(require __DIR__ . '/config.php'),
];