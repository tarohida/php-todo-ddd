<?php
declare(strict_types=1);


namespace App\Application\Validation;


use App\Application\Validation\ViolateParam\ViolateParamInterface;
use App\Exception\LogicException;

/**
 * Class ViolateParam
 * @package App\Application\Validation
 */
class ViolateParam  implements ViolateParamInterface
{
    private string $param_name;
    private string $reason;

    /**
     * ViolateParam constructor.
     * @param string $param_name
     * @param string $reason
     */
    public function __construct(string $param_name, string $reason)
    {
        if (strlen($param_name) <= 0) {
            throw new LogicException(self::class . '::$param_name 1文字以上の文字列を設定してください');
        }
        if (strlen($reason) <= 0) {
            throw new LogicException(self::class . '::$reason: 1文字以上の文字列を設定してください');
        }
        $this->param_name = $param_name;
        $this->reason = $reason;
    }

    public function getName(): string
    {
        return $this->param_name;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}