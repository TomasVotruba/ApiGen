<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassMethodReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassMethodReflection\Source\ClassMethod;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ClassMethodReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassMethodReflectionInterface
     */
    private $classMethodReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $classReflection = $classReflections[ClassMethod::class];
        $this->classMethodReflection = $classReflection->getMethod('methodWithArgs');
    }

    public function testName(): void
    {
        $this->assertSame('methodWithArgs', $this->classMethodReflection->getName());
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(ClassMethodReflectionInterface::class, $this->classMethodReflection);
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->classMethodReflection->getDeclaringClass());
        $this->assertSame(ClassMethod::class, $this->classMethodReflection->getDeclaringClassName());
    }

    public function testModificators(): void
    {
        $this->assertFalse($this->classMethodReflection->isAbstract());
        $this->assertFalse($this->classMethodReflection->isFinal());
        $this->assertFalse($this->classMethodReflection->isPrivate());
        $this->assertFalse($this->classMethodReflection->isProtected());
        $this->assertTrue($this->classMethodReflection->isPublic());
        $this->assertFalse($this->classMethodReflection->isStatic());
    }

    public function testGetParameters(): void
    {
        $parameters = $this->classMethodReflection->getParameters();
        $this->assertCount(3, $parameters);
        $this->assertInstanceOf(MethodParameterReflectionInterface::class, $parameters['url']);
        $this->assertSame(['url', 'data', 'headers'], array_keys($parameters));
    }

    public function testReturnReference(): void
    {
        $this->assertFalse($this->classMethodReflection->returnsReference());
    }
}
