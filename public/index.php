<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Symfony\Component\ErrorHandler\Debug;
use App\Controller\HealthCheckController;

require __DIR__ . '/../vendor/autoload.php';

// Enable error handling
Debug::enable();

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

// Define route
$app->get('/', [HealthCheckController::class, 'index']);

// Run the app
$app->run();