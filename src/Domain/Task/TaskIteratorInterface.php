<?php
declare(strict_types=1);


namespace App\Domain\Task;


use Iterator;

/**
 * 中に入っているのはTaskInterfaceを実装したオブジェクトであることが保証されたIterator
 *
 * Class TaskIteratorInterface
 * @package App\Domain\Task
 */
interface TaskIteratorInterface extends Iterator
{
    public function getArray(): array;
}