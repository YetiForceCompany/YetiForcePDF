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

/**
 * Class Parser
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
	 * Parent style if exists
	 * @var null|\YetiForcePDF\Style\Style
	 */
	protected $parent = null;
	/**
	 * @var \YetiForcePDF\Style\Style
	 */
	protected $previous;
	/**
	 * @var \YetiForcePDF\Style\Style
	 */
	protected $next;
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
		'font-family' => 'Helvetica',
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
		'font-family' => 'Helvetica',
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
		$display = ucfirst($this->rules['display']);
		$dimensionsClassName = "\\YetiForcePDF\\Style\\Dimensions\\Display\\$display";
		$this->dimensions = (new $dimensionsClassName())
			->setDocument($this->document)
			->setStyle($this)
			->init();
		$coordinatesClassName = "\\YetiForcePDF\\Style\\Coordinates\\Display\\$display";
		$this->coordinates = (new $coordinatesClassName())
			->setDocument($this->document)
			->setStyle($this)
			->init();
		$this->font = (new \YetiForcePDF\Objects\Font())
			->setDocument($this->document)
			->setName($this->rules['font-family'])
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
	 * Set parent
	 * @param \YetiForcePDF\Style\Style $parent
	 * @return \YetiForcePDF\Style\Style
	 */
	public function setParent(Style $parent = null): Style
	{
		$this->parent = $parent;
		return $this;
	}

	/**
	 * Get parent style
	 * @return null|\YetiForcePDF\Style\Style
	 */
	public function getParent()
	{
		return $this->parent;
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
	 * Get rules
	 * @return array|mixed
	 */
	public function getRules()
	{
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
	 * Get rules that are inherited from parent
	 * @return array
	 */
	public function getInheritedRules(bool $withMandatoryRules = true): array
	{
		$inheritedRules = [];
		foreach ($this->rules as $ruleName => $ruleValue) {
			if (in_array($ruleName, $this->inherited)) {
				$inheritedRules[$ruleName] = $ruleValue;
			}
		}
		if ($withMandatoryRules) {
			foreach (static::$mandatoryRules as $mandatoryName => $mandatoryValue) {
				if (!isset($inheritedRules[$mandatoryName])) {
					$inheritedRules[$mandatoryName] = $mandatoryValue;
				}
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
		$parsed = static::$mandatoryRules;
		if ($this->parent) {
			$parsed = $this->parent->getInheritedRules();
		}
		if ($this->content === null) {
			return $parsed;
		}
		$rules = explode(';', $this->content);
		foreach ($rules as $rule) {
			$rule = trim($rule);
			if ($rule !== '') {
				$ruleExploded = explode(':', $rule);
				$ruleName = trim($ruleExploded[0]);
				$ruleValue = trim($ruleExploded[1]);
				$ucRuleName = str_replace('-', '', ucwords($ruleName, '-'));
				$normalizerName = "YetiForcePDF\\Style\\Normalizer\\$ucRuleName";
				$normalizer = (new $normalizerName())->setDocument($this->document)->setElement($this->element)->init();
				foreach ($normalizer->normalize($ruleValue) as $name => $value) {
					$parsed[$name] = $value;
				}
			}
		}
		return $parsed;
	}
}