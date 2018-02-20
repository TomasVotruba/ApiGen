<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Interface_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\AbstractParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class InterfaceMethodReflection implements InterfaceMethodReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionMethod
     */
    private $reflectionMethod;

    /**
     * @var TransformerCollector
     */
    private $transformerCollector;

    /**
     * @var DocBlock
     */
    private $docBlock;

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

    /**
     * @return AbstractParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        return $this->transformerCollector->transformGroup($this->reflectionMethod->getParameters());
    }

    public function getDeclaringInterface(): InterfaceReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->reflectionMethod->getDeclaringClass()
        );
    }

    public function getDeclaringInterfaceName(): string
    {
        return $this->getDeclaringInterface()
            ->getName();
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }

    public function isDeprecated(): bool
    {
        if ($this->reflectionMethod->isDeprecated()) {
            return true;
        }

        return $this->getDeclaringInterface()
            ->isDeprecated();
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
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
}
