<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;

final class ClassConstantReflection implements ClassConstantReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionClassConstant
     */
    private $reflectionClassConstant;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollector
     */
    private $transformerCollector;

    public function __construct(ReflectionClassConstant $reflectionClassConstant, DocBlock $docBlock)
    {
        $this->reflectionClassConstant = $reflectionClassConstant;
        $this->docBlock = $docBlock;
    }

    public function isPublic(): bool
    {
        return $this->reflectionClassConstant->isPublic();
    }

    public function isProtected(): bool
    {
        return $this->reflectionClassConstant->isProtected();
    }

    public function isPrivate(): bool
    {
        return $this->reflectionClassConstant->isPrivate();
    }

    public function getName(): string
    {
        return $this->reflectionClassConstant->getName();
    }

    public function getTypeHint(): string
    {
        $valueType = gettype($this->reflectionClassConstant->getValue());
        if ($valueType === 'integer') {
            return 'int';
        }

        return $valueType;
    }

    public function getDeclaringClass(): ClassReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->reflectionClassConstant->getDeclaringClass()
        );
    }

    public function getDeclaringClassName(): string
    {
        return $this->reflectionClassConstant->getDeclaringClass()->getName();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->reflectionClassConstant->getValue();
    }

    public function getValueDefinition(): string
    {
        return 'TODO'; // $this->constant->getValueAsString(); FIXME
    }

    public function isDeprecated(): bool
    {
        if ($this->getDeclaringClass()->isDeprecated()) {
            return true;
        }

        return $this->hasAnnotation(AnnotationList::DEPRECATED);
    }

    public function getStartLine(): int
    {
        return $this->reflectionClassConstant->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflectionClassConstant->getEndLine();
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    public function hasAnnotation(string $name): bool
    {
        return $this->docBlock->hasTag($name);
    }

    /**
     * @return Tag[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->docBlock->getTagsByName($name);
    }

    /**
     * @return Tag[]|Tag[][]
     */
    public function getAnnotations(): array
    {
        return $this->docBlock->getTags();
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
