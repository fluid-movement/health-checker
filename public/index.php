<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Symfony\Component\ErrorHandler\Debug;
use App\Controller\HealthCheckController;

require __DIR__ . '/../vendor/autoload.php';

// Enable error handling
Debug::enable();

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dotenv->required(['APP_ENV', 'APP_DEBUG', 'APP_URL']);

try {
    $container = (new ContainerBuilder())
        ->addDefinitions(__DIR__ . '/../config/services.php')
        ->build();
} catch (Exception $e) {
    // Handle the exception
    echo 'Error building container: ' . $e->getMessage();
    exit(1);
}

$app = Bridge::create($container);

$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

// Define route
$app->get('/', [HealthCheckController::class, 'index'])->setName('health.index');
$app->get('/health/{name}', [HealthCheckController::class, 'show'])->setName('health.show');

// Run the app
$app->run();