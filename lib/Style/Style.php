<?php
declare(strict_types=1);
/**
 * Style class
 *
 * @package   YetiForcePDF\Style
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style;

use YetiForcePDF\Html\Element;

/**
 * Class Style
 */
class Style extends \YetiForcePDF\Base
{
	/**
	 * @var \YetiForcePDF\Document
	 */
	protected $document;
	/**
	 * CSS text to parse
	 * @var string|null
	 */
	protected $content = null;
	/**
	 * @var \YetiForcePDF\Html\Element
	 */
	protected $element;
	/**
	 * @var \YetiForcePDF\Objects\Font
	 */
	protected $font;
	/**
	 * @var \YetiForcePDF\Style\Coordinates\Coordinates
	 */
	protected $coordinates;
	/**
	 * @var \YetiForcePDF\Style\Dimensions\Element
	 */
	protected $dimensions;
	/**
	 * @var array element wrapped in "lines" like in browsers
	 */
	protected $lines = [];
	/**
	 * Css properties that are iherited by default
	 * @var array
	 */
	protected $inherited = [
		"azimuth",
		"background-image-resolution",
		"border-collapse",
		"border-spacing",
		"caption-side",
		"color",
		"cursor",
		"direction",
		"elevation",
		"empty-cells",
		"font-family",
		"font-size",
		"font-style",
		"font-variant",
		"font-weight",
		"image-resolution",
		"letter-spacing",
		"line-height",
		"list-style-image",
		"list-style-position",
		"list-style-type",
		"list-style",
		"orphans",
		"page-break-inside",
		"pitch-range",
		"pitch",
		"quotes",
		"richness",
		"speak-header",
		"speak-numeral",
		"speak-punctuation",
		"speak",
		"speech-rate",
		"stress",
		"text-align",
		"text-indent",
		"text-transform",
		"visibility",
		"voice-family",
		"volume",
		"white-space",
		"word-wrap",
		"widows",
		"word-spacing",
	];
	/**
	 * Rules that are mandatory with default values
	 * @var array
	 */
	public static $mandatoryRules = [
		'font-family' => 'NotoSerif-Regular',
		'font-size' => 12,
		'font-weight' => 'normal',
		'margin-left' => 0,
		'margin-top' => 0,
		'margin-right' => 0,
		'margin-bottom' => 0,
		'padding-left' => 0,
		'padding-top' => 0,
		'padding-right' => 0,
		'padding-bottom' => 0,
		'border-left-width' => 0,
		'border-top-width' => 0,
		'border-right-width' => 0,
		'border-bottom-width' => 0,
		'border-left-color' => [0, 0, 0, 0],
		'border-top-color' => [0, 0, 0, 0],
		'border-right-color' => [0, 0, 0, 0],
		'border-bottom-color' => [0, 0, 0, 0],
		'border-left-style' => 'none',
		'border-top-style' => 'none',
		'border-right-style' => 'none',
		'border-bottom-style' => 'none',
		'box-sizing' => 'border-box',
		'display' => 'block',
		'width' => 'auto',
		'height' => 'auto',
		'overflow' => 'visible',
	];
	/**
	 * Css rules
	 * @var array
	 */
	protected $rules = [
		'font-family' => 'NotoSerif-Regular',
		'font-size' => 12,
		'font-weight' => 'normal',
		'margin-left' => 0,
		'margin-top' => 0,
		'margin-right' => 0,
		'margin-bottom' => 0,
		'padding-left' => 0,
		'padding-top' => 0,
		'padding-right' => 0,
		'padding-bottom' => 0,
		'border-left-width' => 0,
		'border-top-width' => 0,
		'border-right-width' => 0,
		'border-bottom-width' => 0,
		'border-left-color' => [0, 0, 0, 0],
		'border-top-color' => [0, 0, 0, 0],
		'border-right-color' => [0, 0, 0, 0],
		'border-bottom-color' => [0, 0, 0, 0],
		'border-left-style' => 'none',
		'border-top-style' => 'none',
		'border-right-style' => 'none',
		'border-bottom-style' => 'none',
		'box-sizing' => 'border-box',
		'display' => 'block',
		'width' => 'auto',
		'height' => 'auto',
		'overflow' => 'visible',
	];

	/**
	 * Initialisation
	 * @return \YetiForcePDF\Style\Style
	 */
	public function init(): Style
	{
		$this->rules = $this->parse();
		/*echo $this->element->getText() . " style parsed\n";
		var_dump($this->rules);*/
		$this->font = (new \YetiForcePDF\Objects\Font())
			->setDocument($this->document)
			->setFamily($this->rules['font-family'])
			->setSize($this->rules['font-size'])
			->init();
		return $this;
	}

	/**
	 * Initialise dimensions
	 * @return $this
	 */
	public function initDimensions()
	{
		if ($this->dimensions === null) {
			$this->dimensions = new \YetiForcePDF\Style\Dimensions\Element();
		}
		$this->dimensions->setDocument($this->document)
			->setStyle($this)
			->init();
		foreach ($this->getChildren() as $child) {
			$child->initDimensions();
		}
		return $this;
	}

	/**
	 * Initialise coordinates
	 * @return $this
	 */
	public function initCoordinates()
	{
		if ($this->coordinates === null) {
			$this->coordinates = new \YetiForcePDF\Style\Coordinates\Coordinates();
		}
		$this->coordinates->setDocument($this->document)
			->setStyle($this)
			->init();
		foreach ($this->getChildren() as $child) {
			$child->initCoordinates();
		}
		return $this;
	}

	/**
	 * Calculate element width (and children)
	 * @return $this
	 */
	protected function calculateWidths()
	{
		if ($this->rules['display'] === 'inline') {
			foreach ($this->getChildren() as $child) {
				$child->calculateWidths();
			}
		}
		$this->getDimensions()->calculateWidth();
		if ($this->rules['display'] === 'block') {
			foreach ($this->getChildren() as $child) {
				$child->calculateWidths();
			}
		}
		return $this;
	}

	/**
	 * Get lines (or line) of elements
	 * @param int|null $lineIndex
	 * @return array|mixed
	 */
	protected function getLines(int $lineIndex = null)
	{
		return $this->getElement()->getLines($lineIndex);
	}

	/**
	 * Create line
	 * @param Element[] $children
	 * @return Element|false
	 */
	public function createLine(array $children)
	{
		return $this->getElement()->createLine($children);
	}

	/**
	 * Arrange elements (wrap move etc)
	 */
	public function layout()
	{
		$currentChildren = [];
		$currentWidth = 0;
		foreach ($this->getChildren() as $child) {
			$availableSpace = $this->getDimensions()->getInnerWidth();
			$currentWidth += $child->getDimensions()->getWidth() + $child->getRules('margin-left') + $child->getRules('margin-right');
			$willFit = $currentWidth <= $availableSpace;
			if ($child->getRules('display') === 'block' || !$willFit) {
				// wrap current collected elements as line
				$this->createLine($currentChildren);
				// and add new line with block element
				$this->createLine([$child->getElement()]);
				// start collecting again
				$currentChildren = [];
				$currentWidth = 0;
			} else {
				// we can add one more element to current line
				$currentChildren[] = $child->getElement();
			}
		}
	}

	protected function calculateOffsets()
	{
		$this->getOffset()->calculate();
		foreach ($this->getChildren() as $child) {
			$child->calculateOffsets();
		}
	}

	protected function calculateHeights()
	{
		foreach ($this->getChildren() as $child) {
			$child->calculateHeights();
		}
		$this->getDimensions()->calculateHeight();
	}

	protected function calculateCoordinates()
	{
		$this->getCoordinates()->calculate();
		foreach ($this->getChildren() as $child) {
			$child->calculateCoordinates();
		}
	}

	/**
	 * Calculate layout / dimensions / positions
	 */
	public function calculate()
	{
		$this->calculateWidths();
		$this->layout();
		$this->calculateHeights();
		$this->calculateOffsets();
		$this->calculateCoordinates();
	}

	/**
	 * Set element
	 * @param \YetiForcePDF\Html\Element $element
	 * @return \YetiForcePDF\Style\Style
	 */
	public function setElement(\YetiForcePDF\Html\Element $element): Style
	{
		$this->element = $element;
		return $this;
	}

	/**
	 * Get element
	 * @return \YetiForcePDF\Html\Element
	 */
	public function getElement(): \YetiForcePDF\Html\Element
	{
		return $this->element;
	}

	/**
	 * Set content
	 * @param string|null $content
	 * @return \YetiForcePDF\Style\Style
	 */
	public function setContent(string $content = null): Style
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * Get parent style
	 * @return null|\YetiForcePDF\Style\Style
	 */
	public function getParent()
	{
		if ($parent = $this->element->getParent()) {
			return $parent->getStyle();
		}
	}

	/**
	 * Get children styles
	 * @param array $rules - filter styles with specified rules
	 * @return \YetiForcePDF\Style\Style[]
	 */
	public function getChildren(array $rules = [])
	{
		$childrenStyles = [];
		foreach ($this->element->getChildren() as $child) {
			$style = $child->getStyle();
			$rulesCompatible = true;
			foreach ($rules as $name => $value) {
				if ($style->getRules($name) !== $value) {
					$rulesCompatible = false;
					break;
				}
			}
			if ($rulesCompatible) {
				$childrenStyles[] = $style;
			}
		}
		return $childrenStyles;
	}

	/**
	 * Get styles for elements in specified row (after offset calculation / positioning)
	 * @param int $row - from 0
	 * @return \YetiForcePDF\Style\[]
	 */
	public function getChildrenFromRow(int $row)
	{
		$children = [];
		foreach ($this->getChildren() as $child) {
			if ($child->getElement()->getRow() === $row) {
				$children[] = $child;
			}
		}
		return $children;
	}

	/**
	 * Get smallest offset top from all children in specified row
	 * @param int $row
	 * @return float
	 */
	public function getRowOffsetTop(int $row)
	{
		$offsetTop = 65535; // high enough number
		foreach ($this->getChildrenFromRow($row) as $child) {
			if ($child->getOffset()->getTop() !== null) {
				$offsetTop = min($offsetTop, $child->getOffset()->getTop());
			}
		}
		if ($offsetTop === 65535) {
			// if all children from row doesn't have an offset top specified - get it from parent element
			$parent = $this->getParent();
			if ($parent->getRules('display') !== 'inline') {
				return $parent->getOffset()->getTop() + $parent->getRules('padding-top') + $parent->getRules('border-top-width');
			}
			return $parent->getOffset()->getTop() + $parent->getRules('border-top-width');
		}
		return $offsetTop;
	}

	/**
	 * Get previous element style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getPrevious()
	{
		if ($previous = $this->element->getPrevious()) {
			return $previous->getStyle();
		}
	}

	/**
	 * Get next element style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getNext()
	{
		if ($next = $this->element->getNext()) {
			return $next->getStyle();
		}
	}

	/**
	 * Get dimensions
	 * @return \YetiForcePDF\Style\Dimensions\Element
	 */
	public function getDimensions()
	{
		return $this->dimensions;
	}

	/**
	 * Get rules (or concrete rule if specified)
	 * @param string|null $ruleName
	 * @return array|mixed
	 */
	public function getRules(string $ruleName = null)
	{
		if ($ruleName) {
			return $this->rules[$ruleName];
		}
		return $this->rules;
	}

	/**
	 * Get coordinates
	 * @return \YetiForcePDF\Style\Coordinates\Coordinates
	 */
	public function getCoordinates(): \YetiForcePDF\Style\Coordinates\Coordinates
	{
		return $this->coordinates;
	}

	/**
	 * Shorthand for offset
	 * @return \YetiForcePDF\Style\Coordinates\Offset
	 */
	public function getOffset(): \YetiForcePDF\Style\Coordinates\Offset
	{
		return $this->getCoordinates()->getOffset();
	}

	/**
	 * Get rules that are inherited from parent
	 * @return array
	 */
	public function getInheritedRules(): array
	{
		$inheritedRules = [];
		foreach ($this->rules as $ruleName => $ruleValue) {
			if (in_array($ruleName, $this->inherited)) {
				$inheritedRules[$ruleName] = $ruleValue;
			}
		}
		return $inheritedRules;
	}

	/**
	 * Get current style font
	 * @return \YetiForcePDF\Objects\Font
	 */
	public function getFont(): \YetiForcePDF\Objects\Font
	{
		return $this->font;
	}


	/**
	 * Parse css style
	 * @return array
	 */
	protected function parse(): array
	{
		$parsed = [];
		foreach (static::$mandatoryRules as $mandatoryName => $mandatoryValue) {
			$parsed[$mandatoryName] = $mandatoryValue;
		}
		if ($parent = $this->getParent()) {
			$parsed = array_merge($parsed, $parent->getInheritedRules());
		}
		if ($this->element->isTextNode()) {
			$parsed['display'] = 'inline';
		}
		if (!$this->content) {
			//var_dump('no css' . ($this->element->isTextNode() ? ' [text] ' : ' [html] ') . $this->element->getText());
			return $parsed;
		}
		$rules = explode(';', $this->content);
		foreach ($rules as $rule) {
			$rule = trim($rule);
			if ($rule !== '') {
				$ruleExploded = explode(':', $rule);
				$ruleName = trim($ruleExploded[0]);
				$ruleValue = trim($ruleExploded[1]);
				$normalizerName = \YetiForcePDF\Style\Normalizer\Normalizer::getNormalizerClassName($ruleName);
				$normalizer = (new $normalizerName())->setDocument($this->document)->setElement($this->element)->init();
				foreach ($normalizer->normalize($ruleValue) as $name => $value) {
					$parsed[$name] = $value;
				}
			}
		}
		return $parsed;
	}
}
