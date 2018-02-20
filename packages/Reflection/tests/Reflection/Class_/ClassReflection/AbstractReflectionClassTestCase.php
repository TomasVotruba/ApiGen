<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\AccessLevels;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\ParentClass;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\RichInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

abstract class AbstractReflectionClassTestCase extends AbstractContainerAwareTestCase
{
    /**
     * @var ClassReflectionInterface
     */
    protected $classReflection;

    /**
     * @var ClassReflectionInterface
     */
    protected $classReflection;

    /**
     * @var InterfaceReflectionInterface
     */
    protected $interfaceReflection;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        /** @var ReflectionStorage $reflectionStorage */
        $reflectionStorage = $this->container->get(ReflectionStorage::class);

        $classReflections = $reflectionStorage->getClassReflections();
        $this->classReflection = $classReflections[AccessLevels::class];
        $this->classReflection = $classReflections[ParentClass::class];

        $interfaceReflections = $reflectionStorage->getInterfaceReflections();
        $this->interfaceReflection = $interfaceReflections[RichInterface::class];
    }
}
