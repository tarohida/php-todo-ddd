<?php
declare(strict_types=1);


namespace App\Domain\Task\Exception;


use App\Exception\TodoAppException;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class TaskValidateFailedWithIdException
 * @package App\Domain\Task\Exception
 */
class TaskValidateFailedWithIdException extends TodoAppException
{
    public const REASON_NOT_SET = 0;
    public const REASON_MUST_BE_POSITIVE_NUMBER = 1;

    public function getReason(): int
    {
        return $this->reason;
    }

    /**
     * 特殊な場合を除き、基本的に$reasonを指定してください。
     * 適切な理由が存在しない場合は、新規に作成してください。
     *
     * TaskValidateFailedWithIdException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param int $reason
     */
    #[Pure] public function __construct(
        $message = '',
        $code = 0,
        Throwable $previous = null,
        private int $reason = self::REASON_NOT_SET
    ) {
        parent::__construct($message, $code, $previous);
    }
}