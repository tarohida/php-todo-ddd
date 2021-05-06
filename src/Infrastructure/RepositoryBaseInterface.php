<?php
declare(strict_types=1);


namespace App\Infrastructure;


/**
 * Repository系の処理が共通して利用するTransaction周りの機能を提供することを目的とする。。
 *
 * Interface RepositoryBaseInterface
 * @package App\Infrastructure
 */
interface RepositoryBaseInterface
{
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
}