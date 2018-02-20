<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Function_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionFunction;

final class FunctionReflection implements FunctionReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionFunction
     */
    private $reflectionFunction;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollector
     */
    private $transformerCollector;

    public function __construct(ReflectionFunction $reflectionFunction, DocBlock $docBlock)
    {
        $this->reflectionFunction = $reflectionFunction;
        $this->docBlock = $docBlock;
    }

    public function getName(): string
    {
        return $this->reflectionFunction->getName();
    }

    public function getShortName(): string
    {
        return $this->reflectionFunction->getShortName();
    }

    public function getStartLine(): int
    {
        return $this->reflectionFunction->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflectionFunction->getEndLine();
    }

    public function returnsReference(): bool
    {
        return $this->reflectionFunction->returnsReference();
    }

    public function isDeprecated(): bool
    {
        return $this->reflectionFunction->isDeprecated();
    }

    public function getNamespaceName(): string
    {
        return $this->reflectionFunction->getNamespaceName();
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

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    /**
     * @return FunctionParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        return $this->transformerCollector->transformGroup($this->reflectionFunction->getParameters());
    }

    public function getFileName(): ?string
    {
        return $this->reflectionFunction->getFileName();
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
