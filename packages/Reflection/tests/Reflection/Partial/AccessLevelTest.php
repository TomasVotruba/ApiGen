<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Partial;

use ApiGen\Reflection\Contract\Reflection\Partial\AccessLevelInterface;
use ApiGen\Reflection\Tests\Reflection\Partial\Source\SomeClassWithAnnotations;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class AccessLevelTest extends AbstractParserAwareTestCase
{
    /**
     * @var AccessLevelInterface
     */
    private $accessLevel;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);
        $classReflection = $this->reflectionStorage->getClassReflections()[SomeClassWithAnnotations::class];
        $this->accessLevel = $classReflection->getMethod('methodWithArgs');
    }

    public function test(): void
    {
        $this->assertTrue($this->accessLevel->isPublic());
        $this->assertFalse($this->accessLevel->isProtected());
        $this->assertFalse($this->accessLevel->isPrivate());
    }
}
