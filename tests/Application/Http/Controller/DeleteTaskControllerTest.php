<?php
declare(strict_types=1);

namespace Tests\Application\Http\Controller;

use App\Application\Http\Controller\DeleteTaskController;
use App\Domain\Task\TaskRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Slim\Exception\HttpBadRequestException;
use PHPUnit\Framework\Attributes\DataProvider;

class DeleteTaskControllerTest extends TestCase
{
    public function test_returns_no_content_after_deleting_task(): void
    {
        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects(self::once())->method('delete');
        $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/tasks/1');

        $response = (new DeleteTaskController($repository))($request, new Response(), ['id' => '1']);

        self::assertSame(204, $response->getStatusCode());
        self::assertSame('', (string) $response->getBody());
        self::assertSame('', $response->getHeaderLine('Content-Type'));
    }

    #[DataProvider('invalidRouteIds')]
    public function test_rejects_non_canonical_positive_decimal_route_id(string $id): void
    {
        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects(self::never())->method('delete');
        $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/tasks/' . $id);

        $this->expectException(HttpBadRequestException::class);
        (new DeleteTaskController($repository))($request, new Response(), ['id' => $id]);
    }

    public static function invalidRouteIds(): array
    {
        return [['1.5'], ['1e0'], [' 1'], ['1 '], ['0'], ['01'], ['-1'], [(string) PHP_INT_MAX . '0']];
    }
}
