<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Method\MethodReflection;

use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source\ParameterMethodClass;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ParameterWithConstantDefalutValueTest extends AbstractParserAwareTestCase
{
    /**
     * @var MethodParameterReflectionInterface
     */
    private $methodParameterReflection;

    /**
     * @var MethodParameterReflectionInterface
     */
    private $methodParameterReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $classReflection = $this->reflectionStorage->getClassReflections()[ParameterMethodClass::class];

        $methodReflection = $classReflection->getMethod('methodWithConstantDefaultValue');
        $this->methodParameterReflection = $methodReflection->getParameters()['where'];
        $this->methodParameterReflection = $methodReflection->getParameters()['when'];
    }

    public function testGetTypeHint(): void
    {
        $this->assertSame('string', $this->methodParameterReflection->getTypeHint());
        $this->assertSame('string', $this->methodParameterReflection->getTypeHint());
    }

    // @todo - fix after constant dump is fixed
    //public function testType(): void
    //{
    //    $this->assertSame('HERE', $this->localConstantParameterReflection->getDefaultValueDefinition());
    //    $this->assertSame('TODAY', $this->classConstantParameterReflection->getDefaultValueDefinition());
    //}
}
