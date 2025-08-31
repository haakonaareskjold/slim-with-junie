<?php
declare(strict_types=1);

use App\Bootstrap;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Environment;
use Slim\Psr7\Factory\ServerRequestFactory;

final class HealthTest extends TestCase
{
    public function testHealthEndpointReturnsOk(): void
    {
        $app = (new Bootstrap())();
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/health');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $data = json_decode((string)$response->getBody(), true);
        $this->assertSame('ok', $data['status'] ?? null);
    }
}
