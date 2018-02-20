<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Method\MethodReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source\ParameterMethodClass;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class MethodParameterReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    /**
     * @var MethodParameterReflectionInterface
     */
    private $methodParameterReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $this->classReflection = $this->reflectionStorage->getClassReflections()[ParameterMethodClass::class];

        $methodReflection = $this->classReflection->getMethod('methodWithArgs');
        $this->methodParameterReflection = $methodReflection->getParameters()['url'];
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(MethodParameterReflectionInterface::class, $this->methodParameterReflection);
    }

    public function testGetTypeHint(): void
    {
        $this->assertSame('int|string', $this->methodParameterReflection->getTypeHint());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('the URL of the API endpoint', $this->methodParameterReflection->getDescription());
    }

    public function testType(): void
    {
        $this->assertFalse($this->methodParameterReflection->isArray());
        $this->assertFalse($this->methodParameterReflection->isVariadic());
    }

    public function testGetDeclaringFunction(): void
    {
        $this->assertInstanceOf(
            ClassMethodReflectionInterface::class,
            $this->methodParameterReflection->getDeclaringMethod()
        );
    }

    public function testGetDeclaringFunctionName(): void
    {
        $this->assertSame('methodWithArgs', $this->methodParameterReflection->getDeclaringMethodName());
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(
            ClassReflectionInterface::class,
            $this->methodParameterReflection->getDeclaringClass()
        );

        $this->assertSame(
            ParameterMethodClass::class,
            $this->methodParameterReflection->getDeclaringClassName()
        );
    }

    public function testIsPassedByReference(): void
    {
        $this->assertFalse($this->methodParameterReflection->isPassedByReference());
    }
}
