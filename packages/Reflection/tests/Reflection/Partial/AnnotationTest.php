<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Partial;

use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\DocBlock\Tags\See;
use ApiGen\Reflection\Tests\Reflection\Partial\Source\SomeClassWithAnnotations;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;

final class AnnotationTest extends AbstractParserAwareTestCase
{
    /**
     * @var AnnotationsInterface
     */
    private $annotations;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);
        $this->annotations = $this->reflectionStorage->getClassReflections()[SomeClassWithAnnotations::class];
    }

    public function testIsDeprecated(): void
    {
        $this->assertFalse($this->annotations->isDeprecated());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('This is some description.', $this->annotations->getDescription());
    }

    public function testGetAnnotations(): void
    {
        $annotations = $this->annotations->getAnnotations();

        $this->assertCount(3, $annotations);

        $this->assertInstanceOf(See::class, $annotations[0]);
        $this->assertInstanceOf(Author::class, $annotations[1]);
        $this->assertInstanceOf(Generic::class, $annotations[2]);
    }

    public function testGetAnnotation(): void
    {
        /** @var Author[] $authorAnnotations */
        $authorAnnotations = $this->annotations->getAnnotation('author');
        $authorAnnotation = $authorAnnotations[0];
        $this->assertSame('Everyone.', $authorAnnotation->getAuthorName());
    }

    public function testHasAnnotation(): void
    {
        $this->assertTrue($this->annotations->hasAnnotation('see'));
        $this->assertFalse($this->annotations->hasAnnotation('nope'));
    }
}
