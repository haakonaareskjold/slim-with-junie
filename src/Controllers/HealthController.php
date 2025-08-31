<?php
declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HealthController
{
    public function __invoke(Request $request, Response $response): Response
    {
        $payload = ['status' => 'ok', 'time' => (new \DateTimeImmutable())->format(DATE_ATOM)];
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
