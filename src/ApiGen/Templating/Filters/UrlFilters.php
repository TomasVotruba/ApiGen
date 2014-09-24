<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Templating\Filters\Helpers\Strings;
use ApiGen\Templating\Filters\Helpers\TextFormatter;
use Nette;
use ApiGen\Configuration\Configuration;
use ApiGen\Generator\Markups\Markup;
use ApiGen\Generator\SourceCodeHighlighter;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionProperty;
use Nette\Utils\Validators;


class UrlFilters extends Filters
{

	/**
	 * @var Configuration|\stdClass
	 */
	private $configuration;

	/**
	 * @var SourceCodeHighlighter
	 */
	private $highlighter;

	/**
	 * @var Markup
	 */
	private $markup;

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;

	/**
	 * @var TextFormatter
	 */
	private $textFormatter;


	public function __construct(Configuration $configuration, SourceCodeHighlighter $highlighter, Markup $markup,
	                            ElementResolver $elementResolver, TextFormatter $textFormatter)
	{
		$this->configuration = $configuration;
		$this->highlighter = $highlighter;
		$this->markup = $markup;
		$this->elementResolver = $elementResolver;
		$this->textFormatter = $textFormatter;
	}


	/**
	 * Returns a link to element summary file.
	 *
	 * @return string
	 */
	public function elementUrl(ReflectionElement $element)
	{
		if ($element instanceof ReflectionClass) {
			return $this->classUrl($element);

		} elseif ($element instanceof ReflectionMethod) {
			return $this->methodUrl($element);

		} elseif ($element instanceof ReflectionProperty) {
			return $this->propertyUrl($element);

		} elseif ($element instanceof ReflectionConstant) {
			return $this->constantUrl($element);

		} elseif ($element instanceof ReflectionFunction) {
			return $this->functionUrl($element);
		}

		return NULL;
	}


	/**
	 * Returns links for package/namespace and its parent packages.
	 *
	 * @param string $package
	 * @param boolean $last
	 * @return string
	 */
	public function packageLinks($package, $last = TRUE)
	{
		if (empty($this->packages)) {
			return $package;
		}

		$links = array();

		$parent = '';
		foreach (explode('\\', $package) as $part) {
			$parent = ltrim($parent . '\\' . $part, '\\');
			$links[] = $last || $parent !== $package
				? $this->link($this->packageUrl($parent), $part)
				: $this->escapeHtml($part);
		}

		return implode('\\', $links);
	}

	/**
	 * @param string $groupName
	 * @return string
	 */
	public function subgroupName($groupName)
	{
		if ($pos = strrpos($groupName, '\\')) {
			return substr($groupName, $pos + 1);
		}
		return $groupName;
	}


	/**
	 * Returns links for namespace and its parent namespaces.
	 *
	 * @param string $namespace
	 * @param boolean $last
	 * @return string
	 */
	public function namespaceLinks($namespace, $last = TRUE)
	{
		if (empty($this->namespaces)) {
			return $namespace;
		}

		$links = array();

		$parent = '';
		foreach (explode('\\', $namespace) as $part) {
			$parent = ltrim($parent . '\\' . $part, '\\');
			$links[] = $last || $parent !== $namespace
				? $this->link($this->namespaceUrl($parent), $part)
				: $this->escapeHtml($part);
		}

		return implode('\\', $links);
	}


	/**
	 * Returns a link to a package summary file.
	 *
	 * @param string $packageName Package name
	 * @return string
	 */
	public function packageUrl($packageName)
	{
		return sprintf(
			$this->configuration->template['templates']['main']['package']['filename'],
			$this->urlize($packageName)
		);
	}


	/**
	 * Returns a link to a group summary file.
	 *
	 * @param string $groupName Group name
	 * @return string
	 */
	public function groupUrl($groupName)
	{
		if ( ! empty($this->packages)) {
			return $this->packageUrl($groupName);
		}

		return $this->namespaceUrl($groupName);
	}


	/**
	 * Returns a link to a namespace summary file.
	 *
	 * @param string $namespaceName Namespace name
	 * @return string
	 */
	public function namespaceUrl($namespaceName)
	{
		return sprintf(
			$this->configuration->template['templates']['main']['namespace']['filename'],
			$this->urlize($namespaceName)
		);
	}


	/**
	 * Returns a link to class summary file.
	 *
	 * @param string|ReflectionClass $class Class reflection or name
	 * @return string
	 */
	public function classUrl($class)
	{
		$className = $class instanceof ReflectionClass ? $class->getName() : $class;
		return sprintf(
			$this->configuration->template['templates']['main']['class']['filename'],
			$this->urlize($className)
		);
	}


	/**
	 * Returns a link to method in class summary file.
	 *
	 * @return string
	 */
	public function methodUrl(ReflectionMethod $method, ReflectionClass $class = NULL)
	{
		$className = $class !== NULL ? $class->getName() : $method->getDeclaringClassName();
		return $this->classUrl($className) . '#' . ($method->isMagic() ? 'm' : '') . '_'
			. ($method->getOriginalName() ?: $method->getName());
	}


	/**
	 * Returns a link to property in class summary file.
	 *
	 * @return string
	 */
	public function propertyUrl(ReflectionProperty $property, ReflectionClass $class = NULL)
	{
		$className = $class !== NULL ? $class->getName() : $property->getDeclaringClassName();
		return $this->classUrl($className) . '#' . ($property->isMagic() ? 'm' : '') . '$' . $property->getName();
	}


	/**
	 * Returns a link to constant in class summary file or to constant summary file.
	 *
	 * @return string
	 */
	public function constantUrl(ReflectionConstant $constant)
	{
		// Class constant
		if ($className = $constant->getDeclaringClassName()) {
			return $this->classUrl($className) . '#' . $constant->getName();
		}
		// Constant in namespace or global space
		return sprintf(
			$this->configuration->template['templates']['main']['constant']['filename'],
			$this->urlize($constant->getName())
		);
	}


	/**
	 * Returns a link to function summary file.
	 *
	 * @return string
	 */
	public function functionUrl(ReflectionFunction $function)
	{
		return sprintf(
			$this->configuration->template['templates']['main']['function']['filename'],
			$this->urlize($function->getName())
		);
	}


	/**
	 * Tries to parse a definition of a class/method/property/constant/function
	 * and returns the appropriate link if successful.
	 *
	 * @param string $definition
	 * @param ReflectionElement $context
	 * @return string|NULL
	 */
	public function resolveLink($definition, ReflectionElement $context)
	{
		if (empty($definition)) {
			return NULL;
		}

		$suffix = '';
		if (substr($definition, -2) === '[]') {
			$definition = substr($definition, 0, -2);
			$suffix = '[]';
		}

		$element = $this->elementResolver->resolveElement($definition, $context, $expectedName);
		if ($element === NULL) {
			return $expectedName;
		}

		$classes = array();
		if ($element->isDeprecated()) {
			$classes[] = 'deprecated';
		}

		/** @var ReflectionClass|ReflectionConstant $element */
		if ( ! $element->isValid()) {
			$classes[] = 'invalid';
		}

		if ($element instanceof ReflectionClass) {
			$link = $this->link($this->classUrl($element), $element->getName(), TRUE, $classes);

		} elseif ($element instanceof ReflectionConstant && $element->getDeclaringClassName() === NULL) {
			$text = $element->inNamespace()
				? $this->escapeHtml($element->getNamespaceName()) . '\\<b>' . $this->escapeHtml($element->getShortName()) . '</b>'
				: '<b>' . $this->escapeHtml($element->getName()) . '</b>';
			$link = $this->link($this->constantUrl($element), $text, FALSE, $classes);

		} elseif ($element instanceof ReflectionFunction) {
			$link = $this->link($this->functionUrl($element), $element->getName() . '()', TRUE, $classes);

		} else {
			$url = '';
			$text = $this->escapeHtml($element->getDeclaringClassName());
			if ($element instanceof ReflectionProperty) {
				$url = $this->propertyUrl($element);
				$text .= '::<var>$' . $this->escapeHtml($element->getName()) . '</var>';

			} elseif ($element instanceof ReflectionMethod) {
				$url = $this->methodUrl($element);
				$text .= '::' . $this->escapeHtml($element->getName()) . '()';

			} elseif ($element instanceof ReflectionConstant) {
				$url = $this->constantUrl($element);
				$text .= '::<b>' . $this->escapeHtml($element->getName()) . '</b>';
			}

			$link = $this->link($url, $text, FALSE, $classes);
		}

		return sprintf('<code>%s</code>', $link . $suffix);
	}

	/**
	 * Returns links for package/namespace and its parent packages.
	 *
	 * @param string $package
	 * @param boolean $last
	 * @return string
	 */
	public function getPackageLinks($package, $last = TRUE)
	{
		if (empty($this->packages)) {
			return $package;
		}

		$links = array();

		$parent = '';
		foreach (explode('\\', $package) as $part) {
			$parent = ltrim($parent . '\\' . $part, '\\');
			$links[] = $last || $parent !== $package
				? $this->link($this->packageUrl($parent), $part)
				: $this->escapeHtml($part);
		}

		return implode('\\', $links);
	}


	/**
	 * Individual annotations processing
	 *
	 * @param string $value
	 * @param string $name
	 * @param ReflectionElement $context
	 * @return string
	 */
	public function annotation($value, $name, ReflectionElement $context)
	{
		switch ($name) {
			case 'return':

			case 'throws':
				$description = trim(strpbrk($value, "\n\r\t $")) ?: $value;
				$description = $this->textFormatter->doc($description, $context);
				return sprintf('<code>%s</code>%s', $this->typeLinks($value, $context), $description ? '<br>' . $description : '');

			case 'license':
				list($url, $description) = Strings::split($value);
				return Strings::link($url, $description ?: $url);

			case 'link':
				list($url, $description) = Strings::split($value);
				if (Validators::isUri($url)) {
					return Strings::link($url, $description ?: $url);
				}
				break;

			case 'see':
				$doc = array();
				foreach (preg_split('~\\s*,\\s*~', $value) as $link) {
					if ($this->elementResolver->resolveElement($link, $context) !== NULL) {
						$doc[] = sprintf('<code>%s</code>', $this->typeLinks($link, $context));

					} else {
						$doc[] = $this->textFormatter->doc($link, $context);
					}
				}
				return implode(', ', $doc);

			case 'uses':
			case 'usedby':
				list($link, $description) = Strings::split($value);
				$separator = $context instanceof ReflectionClass || ! $description ? ' ' : '<br>';
				if ($this->elementResolver->resolveElement($link, $context) !== NULL) {
					return sprintf('<code>%s</code>%s%s', $this->typeLinks($link, $context), $separator, $description);
				}
				break;

			default:
				break;
		}

		// Default
		return $this->textFormatter->doc($value, $context);
	}


	/**
	 * Returns links for types.
	 *
	 * @param string $annotation
	 * @param ReflectionElement $context
	 * @return string
	 */
	public function typeLinks($annotation, ReflectionElement $context)
	{
		$links = array();

		list($types) = Strings::split($annotation);
		if ( ! empty($types) && $types{0} === '$') {
			$types = NULL;
		}

		if (empty($types)) {
			$types = 'mixed';
		}

		foreach (explode('|', $types) as $type) {
			$type = $this->getTypeName($type, FALSE);
			$links[] = $this->resolveLink($type, $context) ?: $this->escapeHtml(ltrim($type, '\\'));
		}

		return implode('|', $links);
	}


	/********************* highlight *********************/

	/**
	 * @param string $source
	 * @param mixed $context
	 * @return mixed
	 */
	public function highlightPHP($source, $context)
	{
		return $this->resolveLink($this->getTypeName($source), $context) ?: $this->highlighter->highlight((string) $source);
	}


	/**
	 * @param string $definition
	 * @param mixed $context
	 * @return mixed
	 */
	public function highlightValue($definition, $context)
	{
		return $this->highlightPHP(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition), $context);
	}

}
