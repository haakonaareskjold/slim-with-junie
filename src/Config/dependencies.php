<?php
declare(strict_types=1);

use App\Services\TodoService;
use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
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
        EntityManagerInterface::class => function (): EntityManagerInterface {
            $config = ORMSetup::createAttributeMetadataConfiguration(
                paths: [__DIR__ . '/../Models'],
                isDevMode: true,
            );

            $dbDir = __DIR__ . '/../../storage';
            if (!is_dir($dbDir) && !mkdir($dbDir, 0777, true) && !is_dir($dbDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dbDir));
            }

            $connection = DriverManager::getConnection([
                'driver' => 'pdo_sqlite',
                'path' => $dbDir . '/database.sqlite',
            ], $config);

            return new EntityManager($connection, $config);
        },
        TodoService::class => DI\create(TodoService::class)->constructor(DI\get(EntityManagerInterface::class)),
    ]);
};
