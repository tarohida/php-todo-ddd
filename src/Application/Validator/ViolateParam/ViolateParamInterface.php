<?php
declare(strict_types=1);


namespace App\Application\Validator\ViolateParam;


/**
 * Interface ViolateParamInterface
 * @package App\Application\Validator\ViolateParam
 */
interface ViolateParamInterface
{
    public function getName(): string;
    public function getReason(): string;
}