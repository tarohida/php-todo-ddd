<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Task;

use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\Exception\TaskAlreadyExistsException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskTitle;
use App\Infrastructure\Task\TaskRepository;
use PDO;
use Tests\Contract\TaskRepositoryContract;

final class PostgresTaskRepositoryContractTest extends TaskRepositoryContract
{
    private const TEST_DATABASE_MARKER = 'php-todo-ddd-integration-test';

    private ?PDO $pdo = null;

    protected function createRepository(): TaskRepositoryInterface
    {
        $database = $_ENV['TEST_DB_NAME'] ?? '';
        $developmentDatabase = $_ENV['DEV_DB_NAME'] ?? ($_ENV['DB_NAME'] ?? '');
        if (!str_ends_with($database, '_test') || $database === $developmentDatabase) {
            self::fail('Refusing PostgreSQL contract tests: TEST_DB_NAME must end in _test and differ from DB_NAME.');
        }
        $this->pdo = new PDO(
            sprintf('pgsql:host=%s;port=5432;dbname=%s', $_ENV['DB_HOST'], $database),
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
        $connectedDatabase = $this->pdo->query('select current_database()')->fetchColumn();
        if ($connectedDatabase !== $database) {
            self::fail('Refusing destructive reset outside the explicitly configured test database.');
        }
        return new TaskRepository($this->pdo);
    }

    protected function resetRepository(): void
    {
        $this->assertDedicatedTestDatabase();
        $this->pdo->exec('TRUNCATE TABLE tasks RESTART IDENTITY');
    }

    public function test_concurrent_allocations_are_unique_and_monotonic(): void
    {
        $processes = [];
        for ($index = 0; $index < 8; ++$index) {
            $pipes = [];
            $process = proc_open(
                [PHP_BINARY, dirname(__DIR__, 2) . '/Support/allocate-task-id.php'],
                [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
                $pipes,
            );
            self::assertIsResource($process);
            $processes[] = [$process, $pipes];
        }

        $ids = [];
        foreach ($processes as [$process, $pipes]) {
            $ids[] = (int) stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            self::assertSame(0, proc_close($process), $error);
        }

        sort($ids);
        self::assertSame(range($ids[0], $ids[0] + 7), $ids);
    }

    public function test_duplicate_save_does_not_abort_a_caller_owned_transaction(): void
    {
        $repository = new TaskRepository($this->pdo);
        $firstId = $repository->createTaskId();
        $first = new Task($firstId, new TaskTitle('first'));

        $this->pdo->beginTransaction();
        $repository->save($first);
        try {
            $repository->save($first);
            self::fail('Duplicate task ID must be reported.');
        } catch (TaskAlreadyExistsException) {
            self::assertTrue($this->pdo->inTransaction());
        }

        $secondId = $repository->createTaskId();
        $repository->save(new Task($secondId, new TaskTitle('second')));
        self::assertTrue($this->pdo->commit());
        self::assertSame(2, $this->pdo->query('select count(*) from tasks')->fetchColumn());
    }

    protected function tearDown(): void
    {
        if ($this->pdo !== null) {
            $this->resetRepository();
            self::assertSame(0, $this->pdo->query('select count(*) from tasks')->fetchColumn());
            self::assertSame(
                ['last_value' => 1, 'is_called' => false],
                $this->pdo->query('select last_value, is_called from tasks_id_seq')->fetch(PDO::FETCH_ASSOC),
            );
        }
        parent::tearDown();
    }

    private function assertDedicatedTestDatabase(): void
    {
        if ($this->pdo === null) {
            self::fail('Test database connection is not initialized.');
        }
        $marker = $this->pdo->prepare('select marker from test_database_marker where marker = :marker');
        try {
            $marker->execute(['marker' => self::TEST_DATABASE_MARKER]);
        } catch (\PDOException) {
            self::fail('Refusing destructive reset: dedicated test database marker is missing.');
        }
        if ($marker->fetchColumn() !== self::TEST_DATABASE_MARKER) {
            self::fail('Refusing destructive reset: dedicated test database marker is missing.');
        }
    }
}
