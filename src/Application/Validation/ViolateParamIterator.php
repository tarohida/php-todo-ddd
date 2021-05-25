<?php
declare(strict_types=1);


namespace App\Application\Validation;


use App\Application\Validation\ViolateParam\ViolateParamInterface;
use App\Application\Validation\ViolateParam\ViolateParamIteratorInterface;

/**
 * Class ViolateParamIterator
 * @package App\Application\Validation
 */
class ViolateParamIterator implements ViolateParamIteratorInterface
{
    private array $array;
    private int $position;

    /**
     * ViolateParamIterator constructor.
     * @param array $violate_params
     */
    public function __construct(array $violate_params)
    {
        $this->array = [];
        $this->position = 0;
        foreach ($violate_params as $violate_param) {
            $this->add($violate_param);
        }
    }

    /**
     * @param ViolateParamInterface $param
     */
    private function add(ViolateParamInterface $param)
    {
        $this->array[] = $param;
    }

    public function next()
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current(): ViolateParamInterface
    {
        return $this->array[$this->position];
    }
}