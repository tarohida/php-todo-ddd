<?php
declare(strict_types=1);


namespace App\Application\Validation\Exception;


use App\Application\Validation\ViolateParam\ViolateParamIteratorInterface;
use App\Exception\TodoAppException;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class ValidateBaseException
 * @package App\Application\Validation\Excetion
 */
class ValidateException extends TodoAppException
{
    /**
     * ValidateException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    #[Pure] public function __construct(
        protected ViolateParamIteratorInterface $violate_params,
        $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getViolateParams(): ViolateParamIteratorInterface
    {
        return $this->violate_params;
    }
}