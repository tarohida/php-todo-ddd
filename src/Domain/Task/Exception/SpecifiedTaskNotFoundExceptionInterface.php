<?php
declare(strict_types=1);


namespace App\Domain\Task\Exception;


use App\Exception\TodoAppExceptionInterface;

interface SpecifiedTaskNotFoundExceptionInterface extends TodoAppExceptionInterface
{
    public function getSpecifiedId(): int;
}