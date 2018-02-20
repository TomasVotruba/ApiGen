<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitPropertyReflection;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Trait_\TraitPropertyReflection\Source\TraitPropertyTrait;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class TraitPropertyReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var TraitPropertyReflectionInterface
     */
    private $traitPropertyReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);
        $traitReflections = $this->reflectionStorage->getTraitReflections();
        $traitReflection = $traitReflections[TraitPropertyTrait::class];
        $this->traitPropertyReflection = $traitReflection->getProperty('memberCount');
    }

    public function testGetDeclaringTrait(): void
    {
        $this->assertInstanceOf(TraitReflectionInterface::class, $this->traitPropertyReflection->getDeclaringTrait());
        $this->assertSame(TraitPropertyTrait::class, $this->traitPropertyReflection->getDeclaringTraitName());
    }
}
