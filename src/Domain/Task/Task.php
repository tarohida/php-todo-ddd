<?php
declare(strict_types=1);


namespace App\Domain\Task;


use App\Application\Validation\ViolateParam;
use App\Application\Validation\ViolateParamIterator;
use App\Domain\Task\Exception\TaskValidateException;

/**
 * Class Task
 * @package App\Domain\Task
 */
class Task implements TaskInterface
{
    private int $id;
    private string $title;

    /**
     * Task constructor.
     * @param int $id
     * @param string $title
     * @throws TaskValidateException
     */
    public function __construct(int $id, string $title) {
        $violate_params = [];
        if ($id <= 0) {
            $reason = 'id must be positive number';
            $violate_params[] = new ViolateParam('id', $reason);
        }
        if (empty($title)) {
            $reason = 'title must not be blank';
            $violate_params[] = new ViolateParam('title', $reason);
        }
        if (!empty($violate_params)) {
            $iterator = new ViolateParamIterator($violate_params);
            throw new TaskValidateException($iterator);
        }
        $this->id = $id;
        $this->title = $title;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }
}