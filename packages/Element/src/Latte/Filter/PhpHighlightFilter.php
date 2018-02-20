<?php declare(strict_types=1);

namespace ApiGen\Element\Latte\Filter;

use ApiGen\Contract\Templating\FilterProviderInterface;
use ApiGen\SourceCodeHighlighter\SourceCodeHighlighter;

final class PhpHighlightFilter implements FilterProviderInterface
{
    /**
     * @var SourceCodeHighlighter
     */
    private $sourceCodeHighlighter;

    public function __construct(SourceCodeHighlighter $sourceCodeHighlighter)
    {
        $this->sourceCodeHighlighter = $sourceCodeHighlighter;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            // use in .latte: {$method|phpHighlight}
            'phpHighlight' => function ($code) {
                return $this->sourceCodeHighlighter->highlight($code);
            },
        ];
    }
}
