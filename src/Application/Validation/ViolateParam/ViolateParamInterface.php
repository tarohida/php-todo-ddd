<?php
declare(strict_types=1);


namespace App\Application\Validation\ViolateParam;


/**
 * Interface ViolateParamInterface
 * @package App\Application\Validation\ViolateParam
 */
interface ViolateParamInterface
{
    public function getName(): string;
    public function getReason(): string;
}