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

use YetiForcePDF\Layout\Box;
use \YetiForcePDF\Layout\InlineBox;

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
	 * @var Box
	 */
	protected $box;
	/**
	 * Css properties that are inherited by default
	 * @var array
	 */
	protected $inherited = [
		'azimuth',
		'background-image-resolution',
		'border-collapse',
		'border-spacing',
		'caption-side',
		'color',
		'cursor',
		'direction',
		'elevation',
		'empty-cells',
		'font-family',
		'font-size',
		'font-style',
		'font-variant',
		'font-weight',
		'image-resolution',
		'letter-spacing',
		'line-height',
		'list-style-image',
		'list-style-position',
		'list-style-type',
		'list-style',
		'orphans',
		'page-break-inside',
		'pitch-range',
		'pitch',
		'quotes',
		'richness',
		'speak-header',
		'speak-numeral',
		'speak-punctuation',
		'speak',
		'speech-rate',
		'stress',
		'text-align',
		'text-indent',
		'text-transform',
		'visibility',
		'voice-family',
		'volume',
		'white-space',
		'word-wrap',
		'widows',
		'word-spacing',
	];
	/**
	 * Rules that are mandatory with default values
	 * @var array
	 */
	public static $mandatoryRules = [
		'font-family' => 'NotoSerif-Regular',
		'font-size' => '12px',
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
		'vertical-align' => 'baseline',
		'line-height' => '1.2',
		'background-color' => 'transparent',
		'color' => '#000000',
		'word-wrap' => 'normal',
		'max-width' => 'none',
		'min-width' => 0,
		'white-space' => 'normal',
	];
	/**
	 * Original css rules
	 * @var array
	 */
	protected $originalRules = [];
	/**
	 * Css rules (computed)
	 * @var array
	 */
	protected $rules = [
		'font-family' => 'NotoSerif-Regular',
		'font-size' => '12px',
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
		'vertical-align' => 'baseline',
		'line-height' => '1.2',
		'background-color' => 'transparent',
		'color' => '#000000',
		'word-wrap' => 'normal',
		'max-width' => 'none',
		'min-width' => 0,
		'white-space' => 'normal',
	];

	/**
	 * Initialisation
	 * @return \YetiForcePDF\Style\Style
	 */
	public function init(): Style
	{
		parent::init();
		$this->parse();
		return $this;
	}

	/**
	 * Set box for this element (element is always inside box)
	 * @param \YetiForcePDF\Html\Box $box
	 * @return $this
	 */
	public function setBox($box)
	{
		$this->box = $box;
		return $this;
	}

	/**
	 * Get box
	 * @return \YetiForcePDF\Html\Box
	 */
	public function getBox()
	{
		return $this->box;
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
	public function getElement()
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
	 * Set margins
	 * @param float|null $top
	 * @param float|null $right
	 * @param float|null $bottom
	 * @param float|null $left
	 * @return $this
	 */
	public function setMargins(float $top = null, float $right = null, float $bottom = null, float $left = null)
	{
		if ($top !== null) {
			$this->rules['margin-top'] = $top;
		}
		if ($right !== null) {
			$this->rules['margin-right'] = $right;
		}
		if ($bottom !== null) {
			$this->rules['margin-bottom'] = $bottom;
		}
		if ($left !== null) {
			$this->rules['margin-left'] = $left;
		}
		return $this;
	}

	/**
	 * Get parent style
	 * @return Style|null
	 */
	public function getParent()
	{
		if ($this->box) {
			if ($parentBox = $this->box->getParent()) {
				return $parentBox->getStyle();
			}
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
		foreach ($this->box->getChildren() as $childBox) {
			$childrenStyles[] = $childBox->getStyle();
		}
		return $childrenStyles;
	}

	/**
	 * Do we have children?
	 * @return bool
	 */
	public function hasChildren()
	{
		return $this->box->hasChildren();
	}

	/**
	 * Get previous element style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getPrevious()
	{
		if ($previous = $this->box->getPrevious()) {
			return $previous->getStyle();
		}
	}

	/**
	 * Get next element style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getNext()
	{
		if ($next = $this->box->getNext()) {
			return $next->getStyle();
		}
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
	 * Get original rules (or concrete rule if specified)
	 * @param string|null $ruleName
	 * @return array|mixed
	 */
	public function getOriginalRules(string $ruleName = null)
	{
		if ($ruleName) {
			return $this->originalRules[$ruleName];
		}
		return $this->originalRules;
	}

	/**
	 * Set rule
	 * @param string $ruleName
	 * @param        $ruleValue
	 * @return $this
	 */
	public function setRule(string $ruleName, $ruleValue)
	{
		$this->rules[$ruleName] = $ruleValue;
		return $this;
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
	 * Get horizontal borders width
	 * @return float
	 */
	public function getHorizontalBordersWidth()
	{
		return $this->rules['border-left-width'] + $this->rules['border-right-width'];
	}

	/**
	 * Get vertical borders width
	 * @return float
	 */
	public function getVerticalBordersWidth()
	{
		return $this->rules['border-top-width'] + $this->rules['border-bottom-width'];
	}

	/**
	 * Get horizontal paddings width
	 * @return float
	 */
	public function getHorizontalPaddingsWidth()
	{
		return $this->rules['padding-left'] + $this->rules['padding-right'];
	}

	/**
	 * Get vertical paddings width
	 * @return float
	 */
	public function getVerticalPaddingsWidth()
	{
		return $this->rules['padding-top'] + $this->rules['padding-bottom'];
	}

	/**
	 * Get horizontal margins width
	 * @return float
	 */
	public function getHorizontalMarginsWidth()
	{
		return $this->rules['margin-left'] + $this->rules['margin-right'];
	}

	/**
	 * Get vertical paddings width
	 * @return float
	 */
	public function getVerticalMarginsWidth()
	{
		return $this->rules['margin-top'] + $this->rules['margin-bottom'];
	}

	/**
	 * Get offset top -  get top border width and top padding
	 * @return float
	 */
	public function getOffsetTop()
	{
		return $this->rules['border-top-width'] + $this->rules['padding-top'];
	}

	/**
	 * Get offset left - get left border width and left padding
	 * @return float
	 */
	public function getOffsetLeft()
	{
		return $this->rules['border-left-width'] + $this->rules['padding-left'];
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
	 * Convert units from unit to pdf document units
	 * @param string $unit
	 * @param float  $size
	 * @return float
	 */
	public function convertUnits(string $unit, float $size)
	{
		switch ($unit) {
			case 'px':
			case 'pt':
				return $size;
			case 'mm':
				return $size / (72 / 25.4);
			case 'cm':
				return $size / (72 / 2.54);
			case 'in':
				return $size / 72;
			case '%':
				return $size . '%';
			case 'em':
				return (float)bcmul((string)$this->getFont()->getTextHeight(), (string)$size, 4);
		}
	}

	/**
	 * Get line height
	 * @return float
	 */
	public function getLineHeight()
	{
		if ($this->getBox() instanceof InlineBox) {
			return $this->rules['line-height'];
		}
		return $this->rules['line-height'] + $this->getVerticalPaddingsWidth() + $this->getVerticalBordersWidth();
	}

	/**
	 * Get line height
	 * @return float
	 */
	public function getMaxLineHeight()
	{
		$lineHeight = $this->rules['line-height'];
		if (!$this->getBox() instanceof InlineBox) {
			$lineHeight += $this->getVerticalPaddingsWidth() + $this->getVerticalBordersWidth();
		}
		foreach ($this->getBox()->getChildren() as $child) {
			$lineHeight = max($lineHeight, $child->getStyle()->getMaxLineHeight());
		}
		return $lineHeight;
	}

	/**
	 * Parse inline style without inheritance and normalizer
	 * @return $this
	 */
	public function parseInline()
	{
		$parsed = [];
		foreach (static::$mandatoryRules as $mandatoryName => $mandatoryValue) {
			$parsed[$mandatoryName] = $mandatoryValue;
		}
		if ($this->content) {
			$rules = explode(';', $this->content);
		} else {
			$rules = [];
		}
		$rulesParsed = [];
		foreach ($rules as $rule) {
			$rule = trim($rule);
			if ($rule !== '') {
				$ruleExploded = explode(':', $rule);
				$ruleName = trim($ruleExploded[0]);
				$ruleValue = trim($ruleExploded[1]);
				$rulesParsed[$ruleName] = $ruleValue;
			}
		}
		$rulesParsed = array_merge($parsed, $rulesParsed);
		if ($this->getElement()) {
			if ($this->getElement()->getDOMElement() instanceof \DOMText) {
				$rulesParsed['display'] = 'inline';
			}
		}
		$this->rules = $rulesParsed;
		return $this;
	}

	/**
	 * First of all parse font for convertUnits method
	 * @param array $ruleParsed
	 * @return $this
	 */
	protected function parseFont(array $ruleParsed)
	{
		$finalRules = [];
		foreach ($ruleParsed as $ruleName => $ruleValue) {
			if (substr($ruleName, 0, 4) === 'font') {
				$normalizerName = \YetiForcePDF\Style\Normalizer\Normalizer::getNormalizerClassName($ruleName);
				$normalizer = (new $normalizerName())
					->setDocument($this->document)
					->setStyle($this)
					->init();
				foreach ($normalizer->normalize($ruleValue) as $name => $value) {
					$finalRules[$name] = $value;
				}
			}
		}
		$this->font = (new \YetiForcePDF\Objects\Font())
			->setDocument($this->document)
			->setFamily($finalRules['font-family'])->init();
		$this->font->setSize($finalRules['font-size'])
			->init();
		return $this;
	}

	/**
	 * Parse css style
	 * @return $this
	 */
	protected function parse()
	{
		$parsed = [];
		foreach (static::$mandatoryRules as $mandatoryName => $mandatoryValue) {
			$parsed[$mandatoryName] = $mandatoryValue;
		}
		if ($parent = $this->getParent()) {
			$parsed = array_merge($parsed, $parent->getInheritedRules());
		}
		if ($this->content) {
			$rules = explode(';', $this->content);
		} else {
			$rules = [];
		}
		$rulesParsed = [];
		foreach ($rules as $rule) {
			$rule = trim($rule);
			if ($rule !== '') {
				$ruleExploded = explode(':', $rule);
				$ruleName = trim($ruleExploded[0]);
				$ruleValue = trim($ruleExploded[1]);
				$rulesParsed[$ruleName] = $ruleValue;
			}
		}
		$rulesParsed = array_merge($parsed, $rulesParsed);
		$this->parseFont($rulesParsed);
		if ($this->getElement()) {
			if ($this->getElement()->getDOMElement() instanceof \DOMText) {
				$rulesParsed['display'] = 'inline';
				$rulesParsed['line-height'] = $this->getFont()->getTextHeight();
				// if this is text node it's mean that it was wrapped by anonymous inline element
				// so wee need to copy vertical align property (because it is not inherited by default)
				if ($this->getParent()) {
					$rulesParsed['vertical-align'] = $this->getParent()->getRules('vertical-align');
				}
			}
		}
		$finalRules = [];
		foreach ($rulesParsed as $ruleName => $ruleValue) {
			$normalizerName = \YetiForcePDF\Style\Normalizer\Normalizer::getNormalizerClassName($ruleName);
			$normalizer = (new $normalizerName())
				->setDocument($this->document)
				->setStyle($this)
				->init();
			foreach ($normalizer->normalize($ruleValue) as $name => $value) {
				$finalRules[$name] = $value;
			}
		}
		if ($finalRules['display'] === 'inline') {
			$finalRules['margin-top'] = 0;
			$finalRules['margin-bottom'] = 0;
		}
		$this->rules = $finalRules;
		return $this;
	}

	/**
	 * Clear style for first inline element (in line)
	 * @return $this
	 */
	public function clearFirstInline()
	{
		$box = $this->getBox();
		$dimensions = $box->getDimensions();
		if ($dimensions->getWidth()) {
			$dimensions->setWidth($dimensions->getWidth() - $this->rules['margin-right'] - $this->rules['border-right-width']);
		}
		$this->rules['margin-right'] = 0;
		$this->rules['border-right-width'] = 0;
		if ($this->rules['display'] === 'inline') {
			$this->rules['margin-top'] = 0;
			$this->rules['margin-bottom'] = 0;
		}
		return $this;
	}

	/**
	 * Clear style for last inline element (in line)
	 * @return $this
	 */
	public function clearLastInline()
	{
		$box = $this->getBox();
		$dimensions = $box->getDimensions();
		if ($dimensions->getWidth()) {
			$dimensions->setWidth($dimensions->getWidth() - $this->rules['margin-left'] - $this->rules['border-left-width']);
		}
		$offset = $box->getOffset();
		if ($offset->getLeft()) {
			$offset->setLeft($offset->getLeft() - $this->rules['margin-left'] - $this->rules['border-left-width']);
		}
		$coordinates = $box->getCoordinates();
		if ($coordinates->getX()) {
			$coordinates->setX($coordinates->getX() - $this->rules['margin-left'] - $this->rules['border-left-width']);
		}
		$this->rules['margin-left'] = 0;
		$this->rules['border-left-width'] = 0;
		if ($this->rules['display'] === 'inline') {
			$this->rules['margin-top'] = 0;
			$this->rules['margin-bottom'] = 0;
		}
		return $this;
	}

	/**
	 * Clear style for middle inline element (in line)
	 * @return $this
	 */
	public function clearMiddleInline()
	{
		$box = $this->getBox();
		$dimensions = $box->getDimensions();
		if ($dimensions->getWidth()) {
			$dimensions->setWidth($dimensions->getWidth() - $this->rules['margin-left'] - $this->rules['border-left-width'] - $this->rules['margin-right'] - $this->rules['border-right-width']);
		}
		$offset = $box->getOffset();
		if ($offset->getLeft()) {
			$offset->setLeft($offset->getLeft() - $this->rules['margin-left'] - $this->rules['border-left-width']);
		}
		$coordinates = $box->getCoordinates();
		if ($coordinates->getX()) {
			$coordinates->setX($coordinates->getX() - $this->rules['margin-left'] - $this->rules['border-left-width']);
		}
		$this->rules['margin-left'] = 0;
		$this->rules['margin-right'] = 0;
		$this->rules['border-right-width'] = 0;
		$this->rules['border-left-width'] = 0;
		if ($this->rules['display'] === 'inline') {
			$this->rules['margin-top'] = 0;
			$this->rules['margin-bottom'] = 0;
		}
		return $this;
	}

	public function __clone()
	{
		$this->font = clone $this->font;
	}

}
