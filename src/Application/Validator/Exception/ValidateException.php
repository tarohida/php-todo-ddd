<?php
declare(strict_types=1);


namespace App\Application\Validator\Exception;


use App\Application\Validator\ViolateParam\ViolateParamsIterator\ViolateParamsIterator;
use App\Application\Validator\ViolateParam\ViolateParamsIterator\ViolateParamsIteratorInterface;
use App\Exception\TodoAppException;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class ValidateBaseException
 * @package App\Application\Validator\Excetion
 */
class ValidateException extends TodoAppException
{
    #[Pure] public function getViolateParams(): ViolateParamsIteratorInterface
    {
        return $this->violateParams();
    }

    /**
     * ValidateException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    #[Pure] public function __construct(
        protected ViolateParamsIteratorInterface $violate_params,
        $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function violateParams(): array
    {
        return $this->violate_params;
    }
}