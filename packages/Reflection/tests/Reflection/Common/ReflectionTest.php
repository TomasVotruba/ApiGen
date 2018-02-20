<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Common;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Common\Source\CommonReflection;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);
        $this->classReflection = $this->reflectionStorage->getClassReflections()[CommonReflection::class];
    }

    public function testGetName(): void
    {
        $this->assertSame(CommonReflection::class, $this->classReflection->getName());
    }

    public function testGetFileName(): void
    {
        $this->assertSame(__DIR__ . '/Source/CommonReflection.php', $this->classReflection->getFileName());
    }

    public function testClassConstant(): void
    {
        $methodReflection = $this->classReflection->getMethods()['getClass'];
        $this->assertFalse($methodReflection->returnsReference());
    }

    public function testBetterReflectionsConstantsParsing(): void
    {
        $constants = $this->classReflection->getConstants();
        $this->assertCount(2, $constants);

        $thisDirectoryConstant = $constants['THIS_DIRECTORY'];
        $this->assertSame(__DIR__ . '/Source', $thisDirectoryConstant->getValue());

        $thisClassConstant = $constants['THIS_CLASS_METHOD'];
        $this->assertSame(CommonReflection::class . '::methodWithArgs', $thisClassConstant->getValue());
    }
}
