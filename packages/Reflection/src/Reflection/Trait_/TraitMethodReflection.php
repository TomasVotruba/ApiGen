<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\AbstractParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class TraitMethodReflection implements TraitMethodReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var string
     */
    private const EMPTY_LINE = PHP_EOL . PHP_EOL;

    /**
     * @var ReflectionMethod
     */
    private $reflectionMethod;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollector
     */
    private $transformerCollector;

    public function __construct(ReflectionMethod $reflectionMethod, DocBlock $docBlock)
    {
        $this->reflectionMethod = $reflectionMethod;
        $this->docBlock = $docBlock;
    }

    public function getName(): string
    {
        return $this->reflectionMethod->getName();
    }

    public function getShortName(): string
    {
        return $this->reflectionMethod->getShortName();
    }

    public function getDeclaringTrait(): TraitReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->reflectionMethod->getDeclaringClass()
        );
    }

    public function getDeclaringTraitName(): string
    {
        return $this->getDeclaringTrait()
            ->getName();
    }

    public function isAbstract(): bool
    {
        return $this->reflectionMethod->isAbstract();
    }

    public function isFinal(): bool
    {
        return $this->reflectionMethod->isFinal();
    }

    public function isStatic(): bool
    {
        return $this->reflectionMethod->isStatic();
    }

    public function returnsReference(): bool
    {
        return $this->reflectionMethod->returnsReference();
    }

    /**
     * @return AbstractParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        return $this->transformerCollector->transformGroup($this->reflectionMethod->getParameters());
    }

    public function isDeprecated(): bool
    {
        if ($this->reflectionMethod->isDeprecated()) {
            return true;
        }

        return $this->getDeclaringTrait()
            ->isDeprecated();
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . self::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    public function getOverriddenMethod(): ?TraitMethodReflectionInterface
    {
        return null;
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

    public function getStartLine(): int
    {
        return $this->reflectionMethod->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflectionMethod->getEndLine();
    }

    public function isPublic(): bool
    {
        return $this->reflectionMethod->isPublic();
    }

    public function isProtected(): bool
    {
        return $this->reflectionMethod->isProtected();
    }

    public function isPrivate(): bool
    {
        return $this->reflectionMethod->isPrivate();
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
