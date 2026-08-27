<?php
declare(strict_types=1);

namespace Tests\Application\Http\Controller;

use App\Application\Http\Controller\DeleteTaskController;
use App\Application\Task\DeleteTask;
use App\Domain\Task\Exception\TaskNotFoundException;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Slim\Exception\HttpBadRequestException;
use PHPUnit\Framework\Attributes\DataProvider;

class DeleteTaskControllerTest extends TestCase
{
    public function test_returns_no_content_after_deleting_task(): void
    {
        $repository = $this->createMock(DeleteTask::class);
        $repository->expects(self::once())->method('execute');
        $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/tasks/1');

        $response = (new DeleteTaskController($repository))($request, new Response(), ['id' => '1']);

        self::assertSame(204, $response->getStatusCode());
        self::assertSame('', (string) $response->getBody());
        self::assertSame('', $response->getHeaderLine('Content-Type'));
    }

    public function test_accepts_php_int_max_as_a_route_id(): void
    {
        $repository = $this->createMock(DeleteTask::class);
        $repository->expects(self::once())
            ->method('execute')
            ->with(self::callback(static fn ($id): bool => $id->id() === PHP_INT_MAX));
        $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/tasks/' . PHP_INT_MAX);

        $response = (new DeleteTaskController($repository))(
            $request,
            new Response(),
            ['id' => (string) PHP_INT_MAX],
        );

        self::assertSame(204, $response->getStatusCode());
    }

    public function test_maps_missing_task_to_not_found(): void
    {
        $useCase = $this->createMock(DeleteTask::class);
        $useCase->expects(self::once())
            ->method('execute')
            ->with(self::callback(static fn ($id): bool => $id->id() === 42))
            ->willThrowException(new TaskNotFoundException());
        $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/tasks/42');

        $this->expectException(\Slim\Exception\HttpNotFoundException::class);
        (new DeleteTaskController($useCase))($request, new Response(), ['id' => '42']);
    }

    public function test_rejects_the_decimal_route_id_immediately_above_php_int_max(): void
    {
        $repository = $this->createMock(DeleteTask::class);
        $repository->expects(self::never())->method('execute');
        $id = self::decimalStringAbovePhpIntMax();
        $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/tasks/' . $id);

        $this->expectException(HttpBadRequestException::class);
        (new DeleteTaskController($repository))($request, new Response(), ['id' => $id]);
    }

    #[DataProvider('invalidRouteIds')]
    public function test_rejects_non_canonical_positive_decimal_route_id(string $id): void
    {
        $repository = $this->createMock(DeleteTask::class);
        $repository->expects(self::never())->method('execute');
        $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/tasks/' . $id);

        $this->expectException(HttpBadRequestException::class);
        (new DeleteTaskController($repository))($request, new Response(), ['id' => $id]);
    }

    public static function invalidRouteIds(): array
    {
        return [['1.5'], ['1e0'], [' 1'], ['1 '], ['0'], ['01'], ['-1']];
    }

    private static function decimalStringAbovePhpIntMax(): string
    {
        $digits = str_split((string) PHP_INT_MAX);
        for ($index = count($digits) - 1; $index >= 0; --$index) {
            if ($digits[$index] !== '9') {
                $digits[$index] = (string) ((int) $digits[$index] + 1);
                return implode('', $digits);
            }
            $digits[$index] = '0';
        }

        return '1' . implode('', $digits);
    }
}
