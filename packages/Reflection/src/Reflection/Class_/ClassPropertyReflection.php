<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Types\Object_;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class ClassPropertyReflection implements ClassPropertyReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionProperty
     */
    private $reflectionProperty;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollector
     */
    private $transformerCollector;

    public function __construct(ReflectionProperty $reflectionProperty, DocBlock $docBlock)
    {
        $this->reflectionProperty = $reflectionProperty;
        $this->docBlock = $docBlock;
    }

    public function getNamespaceName(): string
    {
        return $this->reflectionProperty->getDeclaringClass()
            ->getNamespaceName();
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    public function getStartLine(): int
    {
        return $this->reflectionProperty->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflectionProperty->getEndLine();
    }

    public function getName(): string
    {
        return $this->reflectionProperty->getName();
    }

    public function isDefault(): bool
    {
        return $this->reflectionProperty->isDefault();
    }

    public function isStatic(): bool
    {
        return $this->reflectionProperty->isStatic();
    }

    /**
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return $this->reflectionProperty->getDefaultValue();
    }

    public function getTypeHint(): string
    {
        $typeHints = $this->reflectionProperty->getDocBlockTypes();
        if (! count($typeHints)) {
            return '';
        }

        $typeHint = $typeHints[0];
        if ($typeHint instanceof Object_) {
            $classOrInterfaceName = (string) $typeHint->getFqsen();

            return ltrim($classOrInterfaceName, '\\');
        }

        return implode('|', $this->reflectionProperty->getDocBlockTypeStrings());
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

    public function isPrivate(): bool
    {
        return $this->reflectionProperty->isPrivate();
    }

    public function isProtected(): bool
    {
        return $this->reflectionProperty->isProtected();
    }

    public function isPublic(): bool
    {
        return $this->reflectionProperty->isPublic();
    }

    public function getDeclaringClass(): ClassReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->reflectionProperty->getDeclaringClass()
        );
    }

    public function getDeclaringClassName(): string
    {
        return $this->reflectionProperty->getDeclaringClass()
            ->getName();
    }

    public function isDeprecated(): bool
    {
        if ($this->getDeclaringClass()->isDeprecated()) {
            return true;
        }

        return $this->hasAnnotation(AnnotationList::DEPRECATED);
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
