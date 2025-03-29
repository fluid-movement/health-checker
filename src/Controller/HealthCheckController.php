<?php


namespace App\Controller;

use App\Service\ConfigService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response;
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
            return $this->view->render($response, 'health_check.twig', [
                'results' => $results
            ]);
        } catch (LoaderError $e) {

        } catch (RuntimeError $e) {

        } catch (SyntaxError $e) {

        }
    }
}