<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Partial;

use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;
use ApiGen\Reflection\Tests\Reflection\Partial\Source\SomeClassWithAnnotations;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class StartAndEndLineTest extends AbstractParserAwareTestCase
{
    /**
     * @var StartAndEndLineInterface
     */
    private $startAndEndLine;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);
        $this->startAndEndLine = $this->reflectionStorage->getClassReflections()[SomeClassWithAnnotations::class];
    }

    public function test(): void
    {
        $this->assertSame(12, $this->startAndEndLine->getStartLine());
        $this->assertSame(24, $this->startAndEndLine->getEndLine());
    }
}
