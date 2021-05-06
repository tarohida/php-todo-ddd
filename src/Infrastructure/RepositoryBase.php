<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
declare(strict_types=1);


namespace App\Infrastructure;


use PDO;

/**
 * Class RepositoryBase
 * @package App\Infrastructure
 */
abstract class RepositoryBase implements RepositoryBaseInterface
{
    private PDO $pdo;

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }


}