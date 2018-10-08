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

use YetiForcePDF\Render\Box;

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
	 * Css properties that are inherited by default
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
		$this->font = (new \YetiForcePDF\Objects\Font())
			->setDocument($this->document)
			->setFamily($this->rules['font-family'])
			->setSize($this->rules['font-size'])
			->init();
		return $this;
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
	 * @return null|\YetiForcePDF\Style\Style
	 */
	public function getParent()
	{
		if ($this->element && $parent = $this->element->getParent()) {
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
	 * Do we have children?
	 * @return bool
	 */
	public function hasChildren()
	{
		return $this->getElement()->hasChildren();
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
		if ($this->element) {
			if ($this->element->isTextNode()) {
				$parsed['display'] = 'inline';
			}
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
				$normalizer = (new $normalizerName())->setDocument($this->document)->init();
				foreach ($normalizer->normalize($ruleValue) as $name => $value) {
					$parsed[$name] = $value;
				}
			}
		}
		return $parsed;
	}
}
