<?php
declare(strict_types=1);

namespace App;

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;

final class Bootstrap
{
    public function __invoke(): App
    {
        $containerBuilder = new ContainerBuilder();

        $dependencies = require __DIR__ . '/Config/dependencies.php';
        if (is_callable($dependencies)) {
            $dependencies($containerBuilder);
        }

        $container = $containerBuilder->build();
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        $this->registerMiddleware($app);
        $this->loadRoutes($app);

        return $app;
    }

    private function registerMiddleware(App $app): void
    {
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        $displayErrorDetails = (bool)($_ENV['APP_DEBUG'] ?? true);
        $errorMiddleware = new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            $displayErrorDetails,
            true,
            true
        );
        $app->add($errorMiddleware);
    }

    private function loadRoutes(App $app): void
    {
        $routes = require __DIR__ . '/Routes/routes.php';
        if (is_callable($routes)) {
            $routes($app);
        }
    }
}
