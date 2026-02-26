<?php
declare(strict_types=1);

namespace Tests;

use App\Bootstrap;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;

final class TodoTest extends TestCase
{
    protected function setUp(): void
    {
        $dbFile = __DIR__ . '/../storage/database.sqlite';
        if (file_exists($dbFile)) {
            unlink($dbFile);
        }
    }

    public function testTodoCrud(): void
    {
        $app = (new Bootstrap())();

        // Create
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/todos')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(['title' => 'Test Todo']);
        $response = $app->handle($request);
        $this->assertSame(201, $response->getStatusCode());
        $data = json_decode((string)$response->getBody(), true);
        $this->assertSame('Test Todo', $data['title']);
        $this->assertFalse($data['done']);
        $id = $data['id'];

        // List
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/todos');
        $response = $app->handle($request);
        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode((string)$response->getBody(), true);
        $this->assertCount(1, $data);
        $this->assertSame($id, $data[0]['id']);

        // Show
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/todos/' . $id);
        $response = $app->handle($request);
        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode((string)$response->getBody(), true);
        $this->assertSame('Test Todo', $data['title']);

        // Update
        $request = (new ServerRequestFactory())->createServerRequest('PUT', '/todos/' . $id)
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(['title' => 'Updated Todo', 'done' => true]);
        $response = $app->handle($request);
        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode((string)$response->getBody(), true);
        $this->assertSame('Updated Todo', $data['title']);
        $this->assertTrue($data['done']);

        // Delete
        $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/todos/' . $id);
        $response = $app->handle($request);
        $this->assertSame(204, $response->getStatusCode());

        // Show (404)
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/todos/' . $id);
        $response = $app->handle($request);
        $this->assertSame(404, $response->getStatusCode());
    }
}
