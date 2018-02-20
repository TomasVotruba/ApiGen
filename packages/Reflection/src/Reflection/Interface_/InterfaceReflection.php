<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Interface_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Element\Tree\ImplementersResolver;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use Exception;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class InterfaceReflection implements InterfaceReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollector
     */
    private $transformerCollector;

    /**
     * @var ImplementersResolver
     */
    private $implementersResolver;

    public function __construct(
        ReflectionClass $reflectionClass,
        DocBlock $docBlock,
        ImplementersResolver $implementersResolver
    ) {
        $this->reflectionClass = $reflectionClass;
        $this->docBlock = $docBlock;
        $this->implementersResolver = $implementersResolver;
    }

    public function getName(): string
    {
        return $this->reflectionClass->getName();
    }

    public function getShortName(): string
    {
        return $this->reflectionClass->getShortName();
    }

    public function getNamespaceName(): string
    {
        return $this->reflectionClass->getNamespaceName();
    }

    public function getStartLine(): int
    {
        return $this->reflectionClass->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflectionClass->getEndLine();
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getImplementers(): array
    {
        return $this->implementersResolver->getImplementers($this);
    }

    public function getFileName(): ?string
    {
        return $this->reflectionClass->getFileName();
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaces(): array
    {
        return $this->transformerCollector->transformGroup($this->reflectionClass->getInterfaces());
    }

    /**
     * @return InterfaceMethodReflectionInterface[]
     */
    public function getMethods(): array
    {
        return $this->transformerCollector->transformGroup($this->reflectionClass->getMethods());
    }

    /**
     * @return InterfaceMethodReflectionInterface[]
     */
    public function getOwnMethods(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->reflectionClass->getImmediateMethods()
        );
    }

    public function getMethod(string $name): InterfaceMethodReflectionInterface
    {
        return $this->getMethods()[$name];
    }

    /**
     * @return InterfaceConstantReflectionInterface[]
     */
    public function getOwnConstants(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->reflectionClass->getImmediateReflectionConstants()
        );
    }

    /**
     * @return InterfaceConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array
    {
        $inheritedConstants = [];
        foreach ($this->getInterfaces() as $interfaceReflection) {
            $inheritedConstants += $interfaceReflection->getOwnConstants();
        }

        return $inheritedConstants;
    }

    public function hasConstant(string $name): bool
    {
        foreach ($this->getOwnConstants() as $interfaceConstantReflection) {
            if ($name === $interfaceConstantReflection->getName()) {
                return true;
            }
        }

        foreach ($this->getInheritedConstants() as $interfaceConstantReflection) {
            if ($name === $interfaceConstantReflection->getName()) {
                return true;
            }
        }

        return false;
    }

    public function getConstant(string $name): InterfaceConstantReflectionInterface
    {
        foreach ($this->getOwnConstants() as $interfaceConstantReflection) {
            if ($interfaceConstantReflection->getName() === $name) {
                return $interfaceConstantReflection;
            }
        }

        foreach ($this->getInheritedConstants() as $interfaceConstantReflection) {
            if ($interfaceConstantReflection->getName() === $name) {
                return $interfaceConstantReflection;
            }
        }

        throw new Exception(
            sprintf('missing cosntant %s', $name)
        );
    }

    public function getOwnConstant(string $name): InterfaceConstantReflectionInterface
    {
        foreach ($this->getOwnConstants() as $interfaceConstantReflection) {
            if ($name === $interfaceConstantReflection->getName()) {
                return $interfaceConstantReflection;
            }
        }

        throw new Exception(
            sprintf('missing cosntant %s', $name)
        );
    }

    /**
     * Actually "extends interface", but naming goes wrong on more places.
     * So decided to keep it here.
     */
    public function implementsInterface(string $interface): bool
    {
        return $this->reflectionClass->implementsInterface($interface);
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        return $this->docBlock->getTags();
    }

    public function hasAnnotation(string $name): bool
    {
        return $this->docBlock->hasTag($name);
    }

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->docBlock->getTagsByName($name);
    }

    public function isDeprecated(): bool
    {
        return $this->hasAnnotation(AnnotationList::DEPRECATED);
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getOwnInterfaces(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->reflectionClass->getImmediateInterfaces()
        );
    }
}
