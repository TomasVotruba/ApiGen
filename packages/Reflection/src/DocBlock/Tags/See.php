<?php declare(strict_types=1);

namespace ApiGen\Reflection\DocBlock\Tags;

use Nette\Utils\Validators;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Webmozart\Assert\Assert;

final class See extends BaseTag implements StaticMethod
{
    /**
     * @var string
     */
    protected $name = 'see';

    /**
     * @var Fqsen
     */
    protected $fqsen;

    /**
     * @var string
     */
    private $link;

    public function __construct(?Fqsen $fqsen = null, ?string $link = null, ?Description $description = null)
    {
        $this->fqsen = $fqsen;
        $this->link = $link;
        $this->description = $description;
    }

    public function __toString(): string
    {
        return ($this->fqsen ?: $this->link) . ($this->description ? ' ' . $this->description->render() : '');
    }

    /**
     * @param string $body
     */
    public static function create(
        $body,
        ?FqsenResolver $fqsenResolver = null,
        ?DescriptionFactory $descriptionFactory = null,
        ?TypeContext $typeContext = null
    ): self {
        Assert::string($body);
        Assert::allNotNull([$fqsenResolver, $descriptionFactory]);

        $parts = preg_split('/\s+/Su', $body, 2);

        if (! Validators::isUrl($parts[0])) {
            $fqsen = $fqsenResolver->resolve($parts[0], $typeContext);
            $link = null;
        } else {
            $link = $parts[0];
            $fqsen = null;
        }

        $description = isset($parts[1]) ? $descriptionFactory->create($parts[1], $typeContext) : null;

        return new static($fqsen, $link, $description);
    }

    public function getReference(): ?Fqsen
    {
        return $this->fqsen;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }
}
