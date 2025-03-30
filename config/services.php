<?php

use App\Service\ConfigService;
use Slim\Views\Twig;
use Symfony\Component\HttpClient\HttpClient;
use App\Extensions\ViteExtension;
use Twig\Extension\DebugExtension;

return [
    Twig::class => function () {
        $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';

        $cache = $isDev ? false : __DIR__ . '/../var/cache/twig';
        $twig = Twig::create(__DIR__ . '/../src/View/templates', ['debug' => $isDev, 'cache' => $cache]);

        $devServerUrl = $_ENV['APP_URL'].':5173';
        $manifestPath = __DIR__ . '/../public/dist/.vite/manifest.json';
        $twig->addExtension(new ViteExtension($isDev, $devServerUrl, $manifestPath));
        if ($isDev) {
            $twig->addExtension(new DebugExtension());
        }
        return $twig;
    },
    Symfony\Contracts\HttpClient\HttpClientInterface::class => fn() => HttpClient::create(),
    ConfigService::class => fn() => new ConfigService(require __DIR__ . '/config.php'),
];
