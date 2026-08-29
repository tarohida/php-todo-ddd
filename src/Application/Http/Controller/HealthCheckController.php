<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use Closure;
use PDO;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Throwable;

final class HealthCheckController implements SlimHttpControllerInterface
{
    /** @param Closure(): PDO $connectionFactory */
    public function __construct(private Closure $connectionFactory)
    {
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $status = 200;
        $payload = ['status' => 'ok', 'database' => 'ok'];

        try {
            $statement = ($this->connectionFactory)()->prepare('SELECT 1');
            if (!$statement->execute()) {
                throw new \RuntimeException('Database health check failed');
            }
        } catch (Throwable) {
            $status = 503;
            $payload = ['status' => 'unavailable', 'database' => 'unavailable'];
        }

        $response->getBody()->write(json_encode($payload, JSON_THROW_ON_ERROR));

        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }
}
