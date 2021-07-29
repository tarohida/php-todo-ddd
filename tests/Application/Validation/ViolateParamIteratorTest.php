<?php
declare(strict_types=1);

namespace Tests\Application\Validation;

use App\Application\Validation\ViolateParam\ViolateParamInterface;
use App\Application\Validation\ViolateParam\ViolateParamIteratorInterface;
use App\Application\Validation\ViolateParamIterator;
use PHPUnit\Framework\TestCase;

/**
 * Class ViolateParamIteratorTest
 * @package Tests\Application\Validation
 */
class ViolateParamIteratorTest extends TestCase
{
    private ViolateParamIterator $violate_param_iterator;

    public function setUp(): void
    {
        parent::setUp();
        $violate_params = [
            $this->createStub(ViolateParamInterface::class),
            $this->createStub(ViolateParamInterface::class),
            $this->createStub(ViolateParamInterface::class)
        ];
        $this->violate_param_iterator = new ViolateParamIterator($violate_params);
    }

    public function test_implements_ViolateParamIterator()
    {
        $this->assertInstanceOf(ViolateParamIteratorInterface::class, $this->violate_param_iterator);
    }

    public function test_foreach()
    {
        foreach ($this->violate_param_iterator as $violate_param) {
            $this->assertInstanceOf(ViolateParamInterface::class, $violate_param);
        }
    }
}
