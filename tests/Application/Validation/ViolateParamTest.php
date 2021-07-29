<?php
declare(strict_types=1);

namespace Tests\Application\Validation;

use App\Application\Validation\ViolateParam;
use App\Exception\LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Class ViolateParamTest
 * @package Tests\Application\Validation
 */
class ViolateParamTest extends TestCase
{
    private string $param_name;
    private string $reason;
    private ViolateParam $violate_param;

    protected function setUp(): void
    {
        parent::setUp();
        $this->param_name = 'param_name';
        $this->reason = 'reason';
        $this->violate_param = new ViolateParam($this->param_name, $this->reason);
    }

    public function test_implements_ViolateParamInterface()
    {
        $this->assertInstanceOf(ViolateParam\ViolateParamInterface::class, $this->violate_param);
    }

    public function test_method_getName()
    {
        $this->assertSame($this->param_name, $this->violate_param->getName());
    }

    public function test_method_getReason()
    {
        $this->assertSame($this->reason, $this->violate_param->getReason());
    }

    public function test_validation_param_name()
    {
        $this->expectException(LogicException::class);
        new ViolateParam('', $this->reason);
    }

    public function test_validation_reason()
    {
        $this->expectException(LogicException::class);
        new ViolateParam($this->param_name, '');
    }
}
