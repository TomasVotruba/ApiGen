<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ExceptionGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ExceptionGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ExceptionGenerator
     */
    private $exceptionGenerator;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $this->exceptionGenerator = $this->container->get(ExceptionGenerator::class);
    }

    public function testGenerate(): void
    {
        $this->exceptionGenerator->generate();

        $this->assertFileExists(
            TEMP_DIR . '/exception-ApiGen.Tests.Generator.Source.SomeException.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/source-exception-ApiGen.Tests.Generator.Source.SomeException.html'
        );
    }
}
