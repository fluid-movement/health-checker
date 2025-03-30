<?php


namespace App\Controller;

use App\Service\ConfigService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use App\Service\HealthCheckService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class HealthCheckController
{
    public function __construct(
        private Twig               $view,
        private HealthCheckService $healthCheckService,
        private ConfigService      $configService
    )
    {
    }

    public function index(Request $request, Response $response): Response
    {
        $results = $this->healthCheckService->checkEndpoints($this->configService->get('endpoints'));

        try {
            return $this->view->render($response, 'health/list.twig', [
                'results' => $results
            ]);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            return $this->view->render(
                $response->withStatus(500)->withHeader('Content-Type', 'text/html'),
                'error.twig',
                ['error' => $e->getMessage()]
            );
        }
    }

    public function show(Request $request, Response $response): Response
    {
        $args = $request->getAttributes();
        $name = $args['name'];
        $endpoints = $this->configService->get('endpoints');
        if (!array_key_exists($name, $endpoints)) {
            return $this->view->render(
                $response->withStatus(500)->withHeader('Content-Type', 'text/html'),
                'error.twig',
                ['error' => "Endpoint '$name' not found"]
            );
        }
        $url = $endpoints[$name];
        $result = $this->healthCheckService->checkEndpoint($url);

        try {
            return $this->view->render($response, 'health/show.twig', [
                'name' => $name,
                'result' => $result
            ]);
        } catch (LoaderError|SyntaxError|RuntimeError $e) {
            return $this->view->render(
                $response->withStatus(500)->withHeader('Content-Type', 'text/html'),
                'error.twig',
                ['error' => $e->getMessage()]
            );
        }
    }
}