<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassPropertyReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassPropertyReflection\Source\ReflectionProperty;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ClassPropertyReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassPropertyReflectionInterface
     */
    private $classPropertyReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $classReflection = $classReflections[ReflectionProperty::class];
        $this->classPropertyReflection = $classReflection->getProperty('memberCount');
    }

    public function testName(): void
    {
        $this->assertSame('memberCount', $this->classPropertyReflection->getName());
        $this->assertSame(
            'ApiGen\Reflection\Tests\Reflection\Class_\ClassPropertyReflection\Source',
            $this->classPropertyReflection->getNamespaceName()
        );
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(ClassPropertyReflectionInterface::class, $this->classPropertyReflection);
    }

    public function testGetTypeHint(): void
    {
        $this->assertSame('int', $this->classPropertyReflection->getTypeHint());
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->classPropertyReflection->getDeclaringClass());
        $this->assertSame(ReflectionProperty::class, $this->classPropertyReflection->getDeclaringClassName());
    }

    public function testDefaults(): void
    {
        $this->assertTrue($this->classPropertyReflection->isDefault());
        $this->assertSame(52, $this->classPropertyReflection->getDefaultValue());
    }

    public function testIsStatic(): void
    {
        $this->assertFalse($this->classPropertyReflection->isStatic());
    }

    public function testLines(): void
    {
        $this->assertSame(10, $this->classPropertyReflection->getStartLine());
        $this->assertSame(10, $this->classPropertyReflection->getEndLine());
    }
}
