<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Trait_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Element\Tree\TraitUsersResolver;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use InvalidArgumentException;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class TraitReflection implements TraitReflectionInterface, TransformerCollectorAwareInterface
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
     * @var TraitUsersResolver
     */
    private $traitUsersResolver;

    public function __construct(
        ReflectionClass $reflectionClass,
        DocBlock $docBlock,
        TraitUsersResolver $traitUsersResolver
    ) {
        $this->reflectionClass = $reflectionClass;
        $this->docBlock = $docBlock;
        $this->traitUsersResolver = $traitUsersResolver;
    }

    public function getName(): string
    {
        return $this->reflectionClass->getName();
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
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    /**
     * @return ClassReflectionInterface[]|TraitReflectionInterface[]
     */
    public function getUsers(): array
    {
        return $this->traitUsersResolver->getUsers($this);
    }

    public function getShortName(): string
    {
        return $this->reflectionClass->getShortName();
    }

    public function isDeprecated(): bool
    {
        return $this->hasAnnotation(AnnotationList::DEPRECATED);
    }

    public function getNamespaceName(): string
    {
        return $this->reflectionClass->getNamespaceName();
    }

    public function getFileName(): ?string
    {
        return $this->reflectionClass->getFileName();
    }

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getMethods(): array
    {
        return $this->transformerCollector->transformGroup($this->reflectionClass->getMethods());
    }

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getOwnMethods(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->reflectionClass->getImmediateMethods()
        );
    }

    public function getMethod(string $name): TraitMethodReflectionInterface
    {
        if (! isset($this->getMethods()[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Method "%s" does not exist in trait "%s".',
                $name,
                $this->getName()
            ));
        }

        return $this->getMethods()[$name];
    }

    /**
     * Fails for now, see:
     * https://github.com/nikic/PHP-Parser/issues/73#issuecomment-24533846
     * $this->betterTraitReflection->getTraits();
     * and PR with test
     * https://github.com/Roave/BetterReflection/pull/274.
     *
     * @return TraitReflectionInterface[]
     */
    public function getTraits(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getTraitAliases(): array
    {
        return $this->reflectionClass->getTraitAliases();
    }

    /**
     * @return TraitPropertyReflectionInterface[]
     */
    public function getProperties(): array
    {
        return $this->transformerCollector->transformGroup($this->reflectionClass->getProperties());
    }

    /**
     * @return TraitPropertyReflectionInterface[]
     */
    public function getOwnProperties(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->reflectionClass->getImmediateProperties()
        );
    }

    public function getProperty(string $name): TraitPropertyReflectionInterface
    {
        if (! isset($this->getProperties()[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Property "%s" does not exist in trait "%s".',
                $name,
                $this->getName()
            ));
        }

        return $this->getProperties()[$name];
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

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
