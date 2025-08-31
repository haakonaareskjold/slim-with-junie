<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\TodoService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class TodoController
{
    public function __construct(private TodoService $service) {}

    public function index(Request $request, Response $response): Response
    {
        $response->getBody()->write(json_encode($this->service->all()));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $todo = $this->service->find($id);
        if ($todo === null) {
            $response->getBody()->write(json_encode(['error' => 'Not Found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode($todo));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();
        $title = trim((string)($data['title'] ?? ''));
        if ($title === '') {
            $response->getBody()->write(json_encode(['error' => 'title is required']));
            return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
        }
        $done = (bool)($data['done'] ?? false);
        $todo = $this->service->create($title, $done);
        $response->getBody()->write(json_encode($todo));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $data = (array)$request->getParsedBody();
        $title = array_key_exists('title', $data) ? (string)$data['title'] : null;
        $done = array_key_exists('done', $data) ? (bool)$data['done'] : null;
        $todo = $this->service->update($id, $title, $done);
        if ($todo === null) {
            $response->getBody()->write(json_encode(['error' => 'Not Found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode($todo));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $deleted = $this->service->delete($id);
        if (!$deleted) {
            $response->getBody()->write(json_encode(['error' => 'Not Found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        return $response->withStatus(204);
    }
}
