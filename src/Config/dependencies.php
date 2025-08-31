<?php
declare(strict_types=1);

use App\Services\TodoService;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

return static function (ContainerBuilder $builder): void {
    $builder->addDefinitions([
        LoggerInterface::class => function (): LoggerInterface {
            $logger = new Logger('api');
            $logger->pushHandler(new StreamHandler(__DIR__ . '/../../storage/logs/app.log', Level::Debug));
            return $logger;
        },
        PDO::class => function (): PDO {
            $dbDir = __DIR__ . '/../../storage';
            if (!is_dir($dbDir) && !mkdir($dbDir, 0777, true) && !is_dir($dbDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dbDir));
            }
            $path = $dbDir . '/database.sqlite';
            $pdo = new PDO('sqlite:' . $path);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        },
        TodoService::class => DI\create(TodoService::class)->constructor(DI\get(PDO::class)),
    ]);
};
