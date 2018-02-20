<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Element\Tree\ParentClassElementsResolver;
use ApiGen\Element\Tree\SubClassesResolver;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use InvalidArgumentException;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class ClassReflection implements ClassReflectionInterface, TransformerCollectorAwareInterface
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
     * @var ParentClassElementsResolver
     */
    private $parentClassElementsResolver;

    /**
     * @var SubClassesResolver
     */
    private $subClassesResolver;

    public function __construct(
        ReflectionClass $reflectionClass,
        DocBlock $docBlock,
        ParentClassElementsResolver $parentClassElementsResolver,
        SubClassesResolver $subClassesResolver
    ) {
        $this->reflectionClass = $reflectionClass;
        $this->docBlock = $docBlock;
        $this->parentClassElementsResolver = $parentClassElementsResolver;
        $this->subClassesResolver = $subClassesResolver;
    }

    public function getName(): string
    {
        return $this->reflectionClass->getName();
    }

    public function getShortName(): string
    {
        return $this->reflectionClass->getShortName();
    }

    public function getStartLine(): int
    {
        return $this->reflectionClass->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflectionClass->getEndLine();
    }

    public function getNamespaceName(): string
    {
        return $this->reflectionClass->getNamespaceName();
    }

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->docBlock->getTagsByName($name);
    }

    public function getDescription(): string
    {
        if ($this->getInheritedDescription()) {
            return $this->getInheritedDescription();
        }

        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    public function getInheritedDescription(): ?string
    {
        if ($this->docBlock->hasTag('inheritdoc')) {
            if ($this->getParentClasses()) {
                foreach ($this->getParentClasses() as $parentClassReflection) {
                    if ($parentClassReflection->getDescription()) {
                        return $parentClassReflection->getDescription();
                    }
                }
            }

            if ($this->getInterfaces()) {
                foreach ($this->getInterfaces() as $interfaceReflection) {
                    if ($interfaceReflection->getDescription()) {
                        return $interfaceReflection->getDescription();
                    }
                }
            }
        }

        return null;
    }

    public function getParentClass(): ?ClassReflectionInterface
    {
        $parentClassName = $this->getParentClassName();
        if ($parentClassName === '') {
            return null;
        }

        return $this->transformerCollector->transformSingle(
            $this->reflectionClass->getParentClass()
        );
    }

    public function getParentClassName(): string
    {
        if ($this->reflectionClass->getParentClass()) {
            return $this->reflectionClass->getParentClass()
                ->getName();
        }

        return '';
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParentClasses(): array
    {
        $parentClasses = [];

        $currentClass = $this->getParentClass();
        while ($currentClass) {
            $parentClasses[$currentClass->getName()] = $currentClass;
            $currentClass = $currentClass->getParentClass();
        }

        return $parentClasses;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getSubClasses(): array
    {
        return $this->subClassesResolver->getSubClasses($this);
    }

    public function implementsInterface(string $interface): bool
    {
        return $this->reflectionClass->implementsInterface($interface);
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

    public function getMethod(string $name): ClassMethodReflectionInterface
    {
        if (! isset($this->getMethods()[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Method "%s" does not exist in "%s" class.',
                $name,
                $this->getName()
            ));
        }

        return $this->getMethods()[$name];
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getInheritedMethods(): array
    {
        return $this->parentClassElementsResolver->getInheritedMethods($this);
    }

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getConstants(): array
    {
        return $this->getOwnConstants() + $this->getInheritedConstants();
    }

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getOwnConstants(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->reflectionClass->getImmediateReflectionConstants()
        );
    }

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array
    {
        $parentClassesConstants = [];
        foreach ($this->getParentClasses() as $classReflection) {
            $parentClassesConstants += $classReflection->getOwnConstants();
        }

        $interfaceConstants = [];
        foreach ($this->getInterfaces() as $interfaceReflection) {
            $interfaceConstants += $interfaceReflection->getOwnConstants();
        }

        return $parentClassesConstants + $interfaceConstants;
    }

    public function hasConstant(string $name): bool
    {
        return isset($this->getConstants()[$name]);
    }

    public function getConstant(string $name): ClassConstantReflectionInterface
    {
        if (isset($this->getConstants()[$name])) {
            return $this->getConstants()[$name];
        }

        throw new InvalidArgumentException(sprintf(
            'Constant %s does not exist in class %s',
            $name,
            $this->getName()
        ));
    }

    public function getOwnConstant(string $name): ClassConstantReflectionInterface
    {
        if (! isset($this->getOwnConstants()[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Constant %s does not exist in class %s',
                $name,
                $this->getName()
            ));
        }

        return $this->getOwnConstants()[$name];
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraits(): array
    {
        return $this->transformerCollector->transformGroup($this->reflectionClass->getTraits());
    }

    /**
     * @return string[]
     */
    public function getTraitAliases(): array
    {
        return $this->reflectionClass->getTraitAliases();
    }

    /**
     * @return ClassPropertyReflectionInterface[]
     */
    public function getProperties(): array
    {
        return $this->transformerCollector->transformGroup($this->reflectionClass->getProperties());
    }

    /**
     * @return ClassPropertyReflectionInterface[]
     */
    public function getOwnProperties(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->reflectionClass->getImmediateProperties()
        );
    }

    /**
     * @return ClassPropertyReflectionInterface[][]
     */
    public function getInheritedProperties(): array
    {
        return $this->parentClassElementsResolver->getInheritedProperties($this);
    }

    public function getProperty(string $name): ClassPropertyReflectionInterface
    {
        if (! isset($this->getProperties()[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Property %s does not exist in class %s',
                $name,
                $this->getName()
            ));
        }

        return $this->getProperties()[$name];
    }

    public function isAbstract(): bool
    {
        return $this->reflectionClass->isAbstract();
    }

    public function isFinal(): bool
    {
        return $this->reflectionClass->isFinal();
    }

    public function isSubclassOf(string $class): bool
    {
        return $this->reflectionClass->isSubclassOf($class);
    }

    public function isDeprecated(): bool
    {
        return $this->hasAnnotation(AnnotationList::DEPRECATED);
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

    public function getFileName(): ?string
    {
        return $this->reflectionClass->getFileName();
    }

    /**
     * @return ClassMethodReflectionInterface[]|TraitMethodReflectionInterface[]
     */
    public function getMethods(): array
    {
        $allMethods = [];
        $allMethods += $this->getOwnMethods();

        foreach ($this->getTraits() as $traitReflection) {
            // @todo check override from the left
            // keep already existing
            $allMethods += $traitReflection->getMethods();
        }

        if ($this->getParentClass()) {
            // @todo check override from the left
            // keep already existing
            $allMethods += $this->getParentClass()->getMethods();
        }

        return $allMethods;
    }

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getOwnMethods(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->reflectionClass->getImmediateMethods()
        );
    }

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getTraitMethods(): array
    {
        $traitMethods = [];
        foreach ($this->getTraits() as $traitReflection) {
            $traitMethods = array_merge($traitMethods, $traitReflection->getMethods());
        }

        return $traitMethods;
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaces(): array
    {
        return $this->transformerCollector->transformGroup($this->reflectionClass->getInterfaces());
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
