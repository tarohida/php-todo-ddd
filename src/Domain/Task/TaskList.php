<?php
declare(strict_types=1);

namespace App\Domain\Task;

class TaskList implements \Iterator
{
    public function current()
    {
        throw new \LogicException();
    }

    public function next()
    {
        throw new \LogicException();
    }

    public function key()
    {
        throw new \LogicException();
    }

    public function valid()
    {
        throw new \LogicException();
    }

    public function rewind()
    {
        throw new \LogicException();
    }
}