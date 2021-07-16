<?php
declare(strict_types=1);


namespace App\Domain\Task\Exception;


use App\Exception\TodoAppException;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class TaskValidateByTitleException
 * @package App\Domain\Task\Exception
 */
class TaskValidateFailedWithTitleException extends TodoAppException
{
    public const REASON_NOT_SET = 0;
    public const REASON_MUST_NOT_BE_EMPTY = 1;

    public function getReason(): int
    {
        return $this->reason;
    }
    /**
     * 特殊な場合を除き、基本的に$reasonを指定してください。
     * 適切な理由が存在しない場合は、新規に作成してください。
     *
     * TaskValidateFailedWithTitleException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param int $reason
     */
    #[Pure] public function __construct(
        $message = '',
        $code = 0,
        Throwable $previous = null,
        private int $reason=self::REASON_NOT_SET
    ) {
        parent::__construct($message, $code, $previous);
    }
}