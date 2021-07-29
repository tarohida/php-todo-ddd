<?php
declare(strict_types=1);


namespace App\Application\Validation\ViolateParam;


use Iterator;

/**
 * Interface ViolateParamsIteratorInterface
 * @package App\Application\Validation\ViolateParam\ViolateParamsIterator
 */
interface ViolateParamIteratorInterface extends Iterator
{
    public function current(): ViolateParamInterface;
}