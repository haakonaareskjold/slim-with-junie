<?php
declare(strict_types=1);

use Slim\App;
use App\Controllers\HealthController;
use App\Controllers\TodoController;

return static function (App $app): void {
    // Health
    $app->get('/health', HealthController::class);

    // Todos
    $app->get('/todos', [TodoController::class, 'index']);
    $app->get('/todos/{id}', [TodoController::class, 'show']);
    $app->post('/todos', [TodoController::class, 'store']);
    $app->put('/todos/{id}', [TodoController::class, 'update']);
    $app->delete('/todos/{id}', [TodoController::class, 'destroy']);
};
