<?php

declare(strict_types=1);
/**
 * Style class.
 *
 * @package   YetiForcePDF\Style
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License v3
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style;

use Sabberworm\CSS\Parser as CSSParser;
use YetiForcePDF\Html\Element;
use YetiForcePDF\Layout\Box;
use YetiForcePDF\Layout\InlineBox;
use YetiForcePDF\Layout\TableBox;
use YetiForcePDF\Layout\TableCellBox;
use YetiForcePDF\Math;
use YetiForcePDF\Objects\Font;
use YetiForcePDF\Objects\GraphicState;
use YetiForcePDF\Objects\ImageStream;

/**
 * Class Style.
 */
class Style extends \YetiForcePDF\Base
{
	/**
	 * @var \YetiForcePDF\Document
	 */
	protected $document;
	/**
	 * CSS text to parse.
	 *
	 * @var null|string
	 */
	protected $content;
	/**
	 * @var Element
	 */
	protected $element;
	/**
	 * @var Font
	 */
	protected $font;
	/**
	 * @var GraphicState
	 */
	protected $graphicState;
	/**
	 * @var Box
	 */
	protected $box;
	/**
	 * @var bool
	 */
	protected $parsed = false;
	/**
	 * Element name.
	 *
	 * @var string
	 */
	protected $elementName = '';
	/**
	 * @var ImageStream
	 */
	protected $backgroundImage;
	/**
	 * Max line height cache.
	 *
	 * @var string
	 */
	protected $maxLineHeight;

	/**
	 * Css properties that are inherited by default.
	 *
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
		'opacity',
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
	 * Rules that are mandatory with default values.
	 *
	 * @var array
	 */
	protected $mandatoryRules = [
		'font-family' => 'DejaVu Sans',
		'font-size' => '12px',
		'font-weight' => 'normal',
		'font-style' => 'normal',
		'margin-left' => '0',
		'margin-top' => '0',
		'margin-right' => '0',
		'margin-bottom' => '0',
		'padding-left' => '0',
		'padding-top' => '0',
		'padding-right' => '0',
		'padding-bottom' => '0',
		'border-left-width' => '0',
		'border-top-width' => '0',
		'border-right-width' => '0',
		'border-bottom-width' => '0',
		'border-left-color' => '#000000',
		'border-top-color' => '#000000',
		'border-right-color' => '#000000',
		'border-bottom-color' => '#000000',
		'border-left-style' => 'none',
		'border-top-style' => 'none',
		'border-right-style' => 'none',
		'border-bottom-style' => 'none',
		'border-collapse' => 'separate',
		'border-spacing' => '2px',
		'box-sizing' => 'border-box',
		'display' => 'inline',
		'width' => 'auto',
		'height' => 'auto',
		'overflow' => 'visible',
		'vertical-align' => 'baseline',
		'line-height' => '1.2',
		'background-color' => 'transparent',
		'color' => '#000000',
		'word-wrap' => 'normal',
		'max-width' => 'none',
		'min-width' => '0',
		'white-space' => 'normal',
		'opacity' => '1'
	];
	/**
	 * Original css rules.
	 *
	 * @var array
	 */
	protected $originalRules = [];
	/**
	 * Css rules (computed).
	 *
	 * @var array
	 */
	protected $rules = [];

	/**
	 * Default styles for certain elements.
	 *
	 * @var array
	 */
	protected $elementDefaults = [
		'a:link' => [
			'color' => 'blue',
			'text-decoration' => 'underline',
			'cursor' => 'auto',
		],
		'a:visited' => [
			'color' => 'blue',
			'text-decoration' => 'underline',
			'cursor' => 'auto',
		],
		'a:link:active' => [
			'color' => 'blue',
		],
		'a:visited:active' => [
			'color' => 'blue',
		],
		'address' => [
			'display' => 'block',
			'font-style' => 'italic',
		],
		'area' => [
			'display' => 'none',
		],
		'article' => [
			'display' => 'block',
		],
		'aside' => [
			'display' => 'block',
		],
		'b' => [
			'font-weight' => 'bold',
		],
		'bdo' => [
			'unicode-bidi' => 'bidi-override',
		],
		'blockquote' => [
			'display' => 'block',
			'margin-top' => '1em',
			'margin-bottom' => '1em',
			'margin-left' => '40px',
			'margin-right' => '40px',
		],
		'body' => [
			'display' => 'block',
			'margin' => '8px',
		],
		'body:focus' => [
			'outline' => 'none',
		],
		'br' => [
			'display' => 'block',
		],
		'button' => [
			'display' => 'inline-block',
			'padding' => '10px',
		],
		'caption' => [
			'display' => 'table-caption',
			'text-align' => 'center',
		],
		'cite' => [
			'font-style' => 'italic',
		],
		'code' => [
			'font-family' => 'monospace',
		],
		'col' => [
			'display' => 'table-column',
		],
		'colgroup' => [
			'display:table-column-group',
		],
		'datalist' => [
			'display' => 'none',
		],
		'dd' => [
			'display' => 'block',
			'margin-left' => '40px',
		],
		'del' => [
			'text-decoration' => 'line-through',
		],
		'details' => [
			'display' => 'block',
		],
		'dfn' => [
			'font-style' => 'italic',
		],
		'div' => [
			'display' => 'block',
		],
		'dl' => [
			'display' => 'block',
			'margin-top' => '1em',
			'margin-bottom' => '1em',
			'margin-left' => '0',
			'margin-right' => '0',
		],
		'dt' => [
			'display' => 'block',
		],
		'em' => [
			'font-style' => 'italic',
		],
		'embed:focus' => [
			'outline' => 'none',
		],
		'fieldset' => [
			'display' => 'block',
			'margin-left' => '2px',
			'margin-right' => '2px',
			'padding-top' => '0.35em',
			'padding-bottom' => '0.625em',
			'padding-left' => '0.75em',
			'padding-right' => '0.75em',
		],
		'figcaption' => [
			'display' => 'block',
		],
		'figure' => [
			'display' => 'block',
			'margin-top' => '1em',
			'margin-bottom' => '1em',
			'margin-left' => '40px',
			'margin-right' => '40px',
		],
		'footer' => [
			'display' => 'block',
		],
		'form' => [
			'display' => 'block',
			'margin-top' => '0em',
		],
		'h1' => [
			'display' => 'block',
			'font-size' => '2em',
			'margin-top' => '0.67em',
			'margin-bottom' => '0.67em',
			'margin-left' => '0',
			'margin-right' => '0',
			'font-weight' => 'bold',
		],
		'h2' => [
			'display' => 'block',
			'font-size' => '1.5em',
			'margin-top' => '0.83em',
			'margin-bottom' => '0.83em',
			'margin-left' => '0',
			'margin-right' => '0',
			'font-weight' => 'bold',
		],
		'h3' => [
			'display' => 'block',
			'font-size' => '1.17em',
			'margin-top' => '1em',
			'margin-bottom' => '1em',
			'margin-left' => '0',
			'margin-right' => '0',
			'font-weight' => 'bold',
		],
		'h4' => [
			'display' => 'block',
			'margin-top' => '1.33em',
			'margin-bottom' => '1.33em',
			'margin-left' => '0',
			'margin-right' => '0',
			'font-weight' => 'bold',
		],
		'h5' => [
			'display' => 'block',
			'font-size' => '.83em',
			'margin-top' => '1.67em',
			'margin-bottom' => '1.67em',
			'margin-left' => '0',
			'margin-right' => '0',
			'font-weight' => 'bold',
		],
		'h6' => [
			'display' => 'block',
			'font-size' => '.67em',
			'margin-top' => '2.33em',
			'margin-bottom' => '2.33em',
			'margin-left' => '0',
			'margin-right' => '0',
			'font-weight' => 'bold',
		],
		'head' => [
			'display' => 'none',
		],
		'header' => [
			'display' => 'block',
		],
		'hr' => [
			'display' => 'block',
			'margin-top' => '0.5em',
			'margin-bottom' => '0.5em',
			'margin-left' => '0px',
			'margin-right' => '0px',
			'border-style' => 'solid',
			'border-color' => 'lightgray',
			'border-top-width' => '1px',
		],
		'html' => [
			'display' => 'block',
		],
		'html:focus' => [
			'outline' => 'none',
		],
		'i' => [
			'font-style' => 'italic',
		],
		'iframe:focus' => [
			'outline' => 'none',
		],
		'iframe[seamless]' => [
			'display' => 'block',
		],
		'img' => [
			'display' => 'inline-block',
		],
		'ins' => [
			'text-decoration' => 'underline',
		],
		'kbd' => [
			'font-family' => 'monospace',
		],
		'label' => [
			'cursor' => 'default',
		],
		'legend' => [
			'display' => 'block',
			'padding-left' => '2px',
			'padding-right' => '2px',
			'border' => 'none',
		],
		'li' => [
			'display' => 'list-item',
		],
		'link' => [
			'display' => 'none',
		],
		'map' => [
			'display' => 'inline',
		],
		'mark' => [
			'background-color' => 'yellow',
			'color' => 'black',
		],
		'menu' => [
			'display' => 'block',
			'list-style-type' => 'disc',
			'margin-top' => '1em',
			'margin-bottom' => '1em',
			'margin-left' => '0',
			'margin-right' => '0',
			'padding-left' => '40px',
		],
		'nav' => [
			'display' => 'block',
		],
		'object:focus' => [
			'outline' => 'none',
		],
		'ol' => [
			'display' => 'block',
			'list-style-type' => 'decimal',
			'margin-top' => '1em',
			'margin-bottom' => '1em',
			'margin-left' => '0',
			'margin-right' => '0',
			'padding-left' => '40px',
		],
		'output' => [
			'display' => 'inline',
		],
		'p' => [
			'display' => 'block',
			'margin-top' => '1em',
			'margin-bottom' => '1em',
			'margin-left' => '0',
			'margin-right' => '0',
		],
		'param' => [
			'display' => 'none',
		],
		'pre' => [
			'display' => 'block',
			'font-family' => 'monospace',
			'white-space' => 'pre',
			'margin' => '1em0',
		],
		'q' => [
			'display' => 'inline',
		],
		'q::before' => [
			'content' => 'open-quote',
		],
		'q::after' => [
			'content' => 'close-quote',
		],
		'rt' => [
			'line-height' => 'normal',
		],
		's' => [
			'text-decoration' => 'line-through',
		],
		'samp' => [
			'font-family' => 'monospace',
		],
		'script' => [
			'display' => 'none',
		],
		'section' => [
			'display' => 'block',
		],
		'small' => [
			'font-size' => 'smaller',
		],
		'strike' => [
			'text-decoration' => 'line-through',
		],
		'strong' => [
			'font-weight' => 'bold',
		],
		'style' => [
			'display' => 'none',
		],
		'sub' => [
			'vertical-align' => 'sub',
			'font-size' => 'smaller',
		],
		'summary' => [
			'display' => 'block',
		],
		'sup' => [
			'vertical-align' => 'super',
			'font-size' => 'smaller',
		],
		'table' => [
			'display' => 'table',
			'border-collapse' => 'separate',
			'border-spacing' => '2px',
			'border-color' => 'gray',
		],
		'tbody' => [
			'display' => 'table-row-group',
			'vertical-align' => 'middle',
			'border-color' => 'inherit',
		],
		'td' => [
			'display' => 'table-cell',
			'vertical-align' => 'inherit',
			'padding' => '1px',
		],
		'tfoot' => [
			'display' => 'table-footer-group',
			'vertical-align' => 'middle',
			'border-color' => 'inherit',
		],
		'th' => [
			'display' => 'table-cell',
			'vertical-align' => 'inherit',
			'font-weight' => 'bold',
			'text-align' => 'center',
			'padding' => '1px',
			'background-color' => '#ddd'
		],
		'thead' => [
			'display' => 'table-header-group',
			'vertical-align' => 'middle',
			'border-color' => 'inherit',
		],
		'title' => [
			'display' => 'none',
		],
		'tr' => [
			'display' => 'table-row',
			'vertical-align' => 'inherit',
			'border-color' => 'inherit',
		],
		'u' => [
			'text-decoration' => 'underline',
		],
		'ul' => [
			'display' => 'block',
			'list-style-type' => 'disc',
			'margin-top' => '1em',
			'margin-bottom' => '1em',
			'margin-left' => '0',
			'margin-right' => '0',
			'padding-left' => '40px',
		],
		'var' => [
			'font-style' => 'italic',
		],
	];

	/**
	 * Initialisation.
	 *
	 * @return \YetiForcePDF\Style\Style
	 */
	public function init(): self
	{
		parent::init();
		$this->parse();

		return $this;
	}

	/**
	 * Set box for this element (element is always inside box).
	 *
	 * @param \YetiForcePDF\Layout\Box $box
	 *
	 * @return $this
	 */
	public function setBox($box)
	{
		$this->box = $box;

		return $this;
	}

	/**
	 * Get box.
	 *
	 * @return \YetiForcePDF\Layout\Box
	 */
	public function getBox()
	{
		return $this->box;
	}

	/**
	 * Set element.
	 *
	 * @param \YetiForcePDF\Html\Element $element
	 *
	 * @return \YetiForcePDF\Style\Style
	 */
	public function setElement(Element $element): self
	{
		$this->element = $element;
		$this->setElementName($element->getDOMElement()->nodeName);

		return $this;
	}

	/**
	 * Get element.
	 *
	 * @return \YetiForcePDF\Html\Element
	 */
	public function getElement()
	{
		return $this->element;
	}

	/**
	 * Set element name.
	 *
	 * @param string $elementName
	 *
	 * @return $this
	 */
	public function setElementName(string $elementName)
	{
		$this->elementName = strtolower($elementName);

		return $this;
	}

	/**
	 * Get element name.
	 *
	 * @return string
	 */
	public function getElementName()
	{
		return $this->elementName;
	}

	/**
	 * Set content.
	 *
	 * @param null|string $content
	 *
	 * @return $this
	 */
	public function setContent(string $content = null)
	{
		$content = preg_replace('/data:image\/([a-z]+);/', 'data_image/$1_', $content);
		$this->content = $content;

		return $this;
	}

	/**
	 * Set margins.
	 *
	 * @param string $top
	 * @param string $right
	 * @param string $bottom
	 * @param string $left
	 *
	 * @return $this
	 */
	public function setMargins(string $top = null, string $right = null, string $bottom = null, string $left = null)
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
	 * Get parent style.
	 *
	 * @return null|Style
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
	 * Get children styles.
	 *
	 * @param array $rules - filter styles with specified rules
	 *
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
	 *
	 * @return bool
	 */
	public function hasChildren()
	{
		return $this->box->hasChildren();
	}

	/**
	 * Get previous element style.
	 *
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getPrevious()
	{
		if ($previous = $this->box->getPrevious()) {
			return $previous->getStyle();
		}
	}

	/**
	 * Get next element style.
	 *
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getNext()
	{
		if ($next = $this->box->getNext()) {
			return $next->getStyle();
		}
	}

	/**
	 * Get rules (or concrete rule if specified).
	 *
	 * @param null|string $ruleName
	 *
	 * @return array|string
	 */
	public function getRules(string $ruleName = null)
	{
		if ($ruleName) {
			if (isset($this->rules[$ruleName])) {
				return $this->rules[$ruleName];
			}

			return '';
		}

		return $this->rules;
	}

	/**
	 * Get original rules (or concrete rule if specified).
	 *
	 * @param null|string $ruleName
	 *
	 * @return array|mixed
	 */
	public function getOriginalRules(string $ruleName = null)
	{
		if ($ruleName) {
			if (isset($this->originalRules[$ruleName])) {
				return $this->originalRules[$ruleName];
			}

			return '';
		}

		return $this->originalRules;
	}

	/**
	 * Set rule.
	 *
	 * @param string $ruleName
	 * @param string $ruleValue
	 *
	 * @return $this
	 */
	public function setRule(string $ruleName, $ruleValue)
	{
		$this->rules[$ruleName] = $ruleValue;

		return $this;
	}

	/**
	 * Set rules.
	 *
	 * @param array $rules
	 *
	 * @return $this
	 */
	public function setRules(array $rules)
	{
		$this->rules = array_merge($this->rules, $rules);

		return $this;
	}

	/**
	 * Get rules that are inherited from parent.
	 *
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
	 * Get background image stream.
	 *
	 * @return ImageStream
	 */
	public function getBackgroundImageStream()
	{
		return $this->backgroundImage;
	}

	/**
	 * Get horizontal borders width.
	 *
	 * @return string
	 */
	public function getHorizontalBordersWidth()
	{
		return Math::add($this->rules['border-left-width'], $this->rules['border-right-width']);
	}

	/**
	 * Get vertical borders width.
	 *
	 * @return string
	 */
	public function getVerticalBordersWidth()
	{
		return Math::add($this->rules['border-top-width'], $this->rules['border-bottom-width']);
	}

	/**
	 * Get horizontal paddings width.
	 *
	 * @return string
	 */
	public function getHorizontalPaddingsWidth()
	{
		return Math::add($this->rules['padding-left'], $this->rules['padding-right']);
	}

	/**
	 * Get vertical paddings width.
	 *
	 * @return string
	 */
	public function getVerticalPaddingsWidth()
	{
		return Math::add($this->rules['padding-top'], $this->rules['padding-bottom']);
	}

	/**
	 * Get horizontal margins width.
	 *
	 * @return string
	 */
	public function getHorizontalMarginsWidth()
	{
		return Math::add($this->rules['margin-left'], $this->rules['margin-right']);
	}

	/**
	 * Get vertical paddings width.
	 *
	 * @return string
	 */
	public function getVerticalMarginsWidth()
	{
		return Math::add($this->rules['margin-top'], $this->rules['margin-bottom']);
	}

	/**
	 * Get full left space.
	 *
	 * @return string
	 */
	public function getFullLeftSpace()
	{
		return Math::add($this->rules['margin-left'], Math::add($this->rules['padding-left'], $this->rules['border-left-width']));
	}

	/**
	 * Get full right space.
	 *
	 * @return string
	 */
	public function getFullRightSpace()
	{
		return Math::add(Math::add($this->rules['margin-right'], $this->rules['padding-right']), $this->rules['border-right-width']);
	}

	/**
	 * Get offset top -  get top border width and top padding.
	 *
	 * @param bool $withBorders
	 *
	 * @return string
	 */
	public function getOffsetTop()
	{
		return Math::add($this->rules['border-top-width'], $this->rules['padding-top']);
	}

	/**
	 * Get offset left - get left border width and left padding.
	 *
	 * @param bool $withBorders
	 *
	 * @return string
	 */
	public function getOffsetLeft()
	{
		return Math::add($this->rules['border-left-width'], $this->rules['padding-left']);
	}

	/**
	 * Get current style font.
	 *
	 * @return Font
	 */
	public function getFont(): Font
	{
		return $this->font;
	}

	/**
	 * Get graphic state.
	 *
	 * @return GraphicState
	 */
	public function getGraphicState(): GraphicState
	{
		return $this->graphicState;
	}

	/**
	 * Convert units from unit to pdf document units.
	 *
	 * @param string $unit
	 * @param float  $size
	 *
	 * @return string
	 */
	public function convertUnits(string $unit, string $size)
	{
		if ($size === '' || $size === '0') {
			$size = $this->mandatoryRules['font-size'];
		}
		switch ($unit) {
			case 'px':
			case 'pt':
				return $size;
			case 'mm':
				return Math::div($size, Math::div('72', '25.4'));
			case 'cm':
				return Math::div($size, Math::div('72', '2.54'));
			case 'in':
				return Math::div($size, '72');
			case '%':
				return $size . '%'; // percent values are calculated later
			default: // em too
				if ($this->getParent()) {
					return Math::mul($this->getParent()->getFont()->getTextHeight(), $size);
				}

				return Math::mul($this->getFont()->getTextHeight(), $size);
		}
	}

	/**
	 * Is this box have a borders?
	 *
	 * @return bool
	 */
	public function haveSpacing()
	{
		$spacing = Math::max($this->getHorizontalBordersWidth(), $this->getHorizontalPaddingsWidth());

		return Math::comp($spacing, '0') > 0;
	}

	/**
	 * Get line height.
	 *
	 * @return string
	 */
	public function getLineHeight()
	{
		$box = $this->getBox();
		if (!$box->isRenderable() && !$this->haveSpacing()) {
			return '0';
		}
		if ($box instanceof InlineBox) {
			return Math::add($this->rules['line-height'], $this->getVerticalBordersWidth());
		}

		return Math::add($this->rules['line-height'], Math::add($this->getVerticalPaddingsWidth(), $this->getVerticalBordersWidth()));
	}

	/**
	 * Get line height.
	 *
	 * @return string
	 */
	public function getMaxLineHeight()
	{
		if ($this->maxLineHeight) {
			return $this->maxLineHeight;
		}
		$box = $this->getBox();
		$lineHeight = $this->rules['line-height'];
		if (!$this->getRules('display') !== 'inline') {
			$lineHeight = Math::add($lineHeight, $this->getVerticalPaddingsWidth(), $this->getVerticalBordersWidth());
		}
		foreach ($box->getChildren() as $child) {
			$maxLineHeight = $child->getStyle()->getMaxLineHeight();
			$lineHeight = Math::max($lineHeight, $maxLineHeight, $child->getDimensions()->getHeight());
		}
		if (!$box instanceof LineBox) {
			$this->maxLineHeight = $lineHeight;
		}

		return $lineHeight;
	}

	/**
	 * Get mandatory rules - with default for all elements.
	 *
	 * @param string $elementName
	 *
	 * @return array
	 */
	public function getMandatoryRules()
	{
		return $this->mandatoryRules;
	}

	/**
	 * Get default element rules.
	 *
	 * @return array
	 */
	public function getDefaultRules()
	{
		$rules = [];
		if (!empty($this->elementDefaults[$this->elementName])) {
			foreach ($this->elementDefaults[$this->elementName] as $ruleName => $ruleValue) {
				$rules[$ruleName] = $ruleValue;
			}
		}

		return $rules;
	}

	/**
	 * Parse inline style without inheritance and normalizer.
	 *
	 * @return $this
	 */
	public function parseInline()
	{
		$parsed = array_merge($this->getMandatoryRules(), $this->getDefaultRules());
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
	 * First of all parse font for convertUnits method.
	 *
	 * @param array $ruleParsed
	 *
	 * @return $this
	 */
	protected function parseFont(array $ruleParsed, array $inherited)
	{
		$finalRules = [];
		foreach ($ruleParsed as $ruleName => $ruleValue) {
			if (substr($ruleName, 0, 4) === 'font' && !isset($inherited[$ruleName])) {
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
		foreach (['font-family', 'font-size', 'font-weight', 'font-style'] as $ruleName) {
			if (isset($inherited[$ruleName]) && !isset($finalRules[$ruleName])) {
				$finalRules[$ruleName] = $inherited[$ruleName];
			}
		}
		if (isset($finalRules['font-family'], $finalRules['font-weight'], $finalRules['font-style'], $finalRules['font-size'])) {
			$this->font = (new \YetiForcePDF\Objects\Font())
				->setDocument($this->document)
				->setFamily($finalRules['font-family'])
				->setWeight($finalRules['font-weight'])
				->setStyle($finalRules['font-style'])
				->init();
			// size must be defined after initialisation because we could get cloned font that already exists
			$this->font->setSize($finalRules['font-size']);
		}

		return $this;
	}

	/**
	 * Parse and load image file.
	 *
	 * @param array $ruleParsed
	 *
	 * @return array
	 */
	protected function parseImage(array $ruleParsed)
	{
		if ($element = $this->getElement()) {
			if (($domElement = $element->getDOMElement()) && isset($domElement->tagName)) {
				if ($domElement->tagName === 'img' && $domElement->getAttribute('src')) {
					$ruleParsed['background-image'] = 'url(' . $domElement->getAttribute('src') . ');';
				}
			}
		}
		if (!isset($ruleParsed['background-image'])) {
			return $ruleParsed;
		}
		$src = $ruleParsed['background-image'];
		if (substr($src, 0, 3) !== 'url') {
			return $ruleParsed;
		}
		$src = trim(substr($src, 3), ';)(\'\"');
		$this->backgroundImage = (new ImageStream())
			->setDocument($this->document)
			->init();
		$this->backgroundImage->loadImage($src);
		$imageName = $this->backgroundImage->getImageName();
		$this->document->getCurrentPage()->addResource('XObject', $imageName, $this->backgroundImage);
		if ($ruleParsed['width'] === 'auto') {
			$ruleParsed['width'] = ((string) $this->backgroundImage->getWidth()) . 'px';
		}
		if ($ruleParsed['height'] === 'auto') {
			$ruleParsed['height'] = ((string) $this->backgroundImage->getHeight()) . 'px';
		}

		return $ruleParsed;
	}

	/**
	 * Parse graphic states.
	 *
	 * @param array $ruleParsed
	 *
	 * @return array
	 */
	protected function parseGraphicState(array $ruleParsed, array $inherited)
	{
		$ruleName = 'opacity';
		if (!isset($ruleParsed[$ruleName])) {
			return $this;
		}
		$ruleValue = $ruleParsed['opacity'];
		$normalizerName = \YetiForcePDF\Style\Normalizer\Normalizer::getNormalizerClassName($ruleName);
		$normalizer = (new $normalizerName())
			->setDocument($this->document)
			->setStyle($this)
			->init();
		foreach ($normalizer->normalize($ruleValue, $ruleName) as $name => $value) {
			$ruleParsed[$name] = $value;
		}
		$this->graphicState = (new GraphicState())
			->setDocument($this->document)
			->init();
		$this->graphicState->addValue('ca', $ruleParsed[$ruleName]);
		$this->graphicState->addValue('CA', $ruleParsed[$ruleName]);

		return $ruleParsed;
	}

	/**
	 * Apply text style - default style for text nodes.
	 *
	 * @param array  $rulesParsed
	 * @param &array $inherited
	 *
	 * @return array
	 */
	public function applyTextStyle($rulesParsed, &$inherited)
	{
		if ($this->getElement()->getDOMElement() instanceof \DOMText) {
			$rulesParsed['display'] = 'inline';
			$rulesParsed['line-height'] = '1';
			unset($inherited['line-height']);
			// if this is text node it's mean that it was wrapped by anonymous inline element
			// so wee need to copy vertical align property (because it is not inherited by default)
			if ($this->getParent()) {
				$rulesParsed['vertical-align'] = $this->getParent()->getRules('vertical-align');
			}
		}

		return $rulesParsed;
	}

	/**
	 * Apply border spacing for table cell elements.
	 *
	 * @param array $rulesParsed
	 *
	 * @return array
	 */
	public function applyBorderSpacing($rulesParsed)
	{
		if ($this->getElement()) {
			if ($this->box instanceof TableCellBox) {
				$parentStyle = $this->getParent();
				if ($parentStyle->getRules('border-collapse') !== 'collapse') {
					$padding = $parentStyle->getRules('border-spacing');
					$parentStyle->setRule('padding-top', $padding);
					$parentStyle->setRule('padding-right', $padding);
					$parentStyle->setRule('padding-bottom', $padding);
					$parentStyle->setRule('padding-left', $padding);
				}
			}
		}

		return $rulesParsed;
	}

	/**
	 * Get parent original value - traverse tree to first occurence.
	 *
	 * @param string $ruleName
	 *
	 * @return null|string
	 */
	public function getParentOriginalValue(string $ruleName)
	{
		if ($parent = $this->getParent()) {
			$parentValue = $parent->getOriginalRules($ruleName);
			if ($parentValue !== null) {
				return $parentValue;
			}

			return $parent->getParentOriginalValue($ruleName);
		}
	}

	/**
	 * Import selector rules if exists.
	 *
	 * @param array $parsed rules
	 */
	protected function importSelectors()
	{
		$element = $this->getElement();
		if($element && !empty($element->getClassNames())){
			return $this->document->getCssSelectorRules($element->getClassNames());
		}
		return [];
	}

	/**
	 * Parse css style.
	 *
	 * @return $this
	 */
	protected function parse()
	{
		if ($this->parsed) {
			return $this;
		}
		$parsed = [];
		foreach ($this->getMandatoryRules() as $mandatoryName => $mandatoryValue) {
			$parsed[$mandatoryName] = $mandatoryValue;
		}
		$inherited = [];
		if ($parent = $this->getParent()) {
			$inherited = $parent->getInheritedRules();
			$parsed = array_merge($parsed, $inherited);
		}
		$defaultRules = $this->getDefaultRules();
		$inherited = array_diff_key($inherited, $defaultRules);
		$parsed = array_merge($parsed, $defaultRules);
		if ($this->document->inDebugMode() && $this->getBox() instanceof \YetiForcePDF\Layout\LineBox) {
			$this->content = 'border:1px solid red;';
		}
		$hasImages = false;
		if ($this->content) {
			$rules = explode(';', $this->content);
			$hasBase64Images = strpos($this->content, 'data_image') > 0;
		} else {
			$rules = [];
		}
		$rulesParsed = [];
		foreach ($rules as $rule) {
			$rule = trim($rule);
			if ($rule !== '') {
				$ruleExploded = explode(':', $rule);
				if ($hasBase64Images && strpos($ruleExploded[1], 'data_image') > 0) {
					$ruleExploded[1] = preg_replace('/data_image\/([a-z]+)_/', 'data:image/$1;', $ruleExploded[1]);
				}
				$ruleName = trim($ruleExploded[0]);
				$ruleValue = trim($ruleExploded[1]);
				$rulesParsed[$ruleName] = $ruleValue;
			}
		}
		$inherited = array_diff_key($inherited, $rulesParsed);
		$rulesParsed = array_merge($parsed, $rulesParsed);
		$this->parseFont($rulesParsed, $inherited);
		if ($this->getElement()) {
			$rulesParsed = $this->applyTextStyle($rulesParsed, $inherited);
			$rulesParsed = $this->applyBorderSpacing($rulesParsed);
		}
		$rulesParsed = $this->parseImage($rulesParsed);
		$rulesParsed = $this->parseGraphicState($rulesParsed, $inherited);
		$selectorRules = $this->importSelectors();
		$finalRules = [];
		foreach ($rulesParsed as $ruleName => $ruleValue) {
			if (is_string($ruleValue) && strtolower($ruleValue) === 'inherit') {
				$parentValue = $this->getParentOriginalValue($ruleName);
				if ($parentValue !== null) {
					$ruleValue = $parentValue;
				}
			}
			$this->originalRules[$ruleName] = $ruleValue;
			if(isset($selectorRules[$ruleName])){
				$ruleValue = $selectorRules[$ruleName];
			}
			if (!isset($inherited[$ruleName]) || isset($selectorRules[$ruleName])) {
				$normalizerName = \YetiForcePDF\Style\Normalizer\Normalizer::getNormalizerClassName($ruleName);
				$normalizer = (new $normalizerName())
					->setDocument($this->document)
					->setStyle($this)
					->init();
				foreach ($normalizer->normalize($ruleValue, $ruleName) as $name => $value) {
					$finalRules[$name] = $value;
				}
			} else {
				$finalRules[$ruleName] = $inherited[$ruleName];
			}
		}
		if ($finalRules['display'] === 'inline') {
			$finalRules['margin-top'] = '0';
			$finalRules['margin-bottom'] = '0';
		} elseif (in_array($finalRules['display'], [
			'table-cell', 'table-row', 'table-row-group', 'table-column',
			'table-column-group', 'table-header-group', 'table-footer-group'
		])) {
			$finalRules['margin-top'] = '0';
			$finalRules['margin-bottom'] = '0';
			$finalRules['margin-left'] = '0';
			$finalRules['margin-right'] = '0';
		}
		$this->rules = $finalRules;
		$this->parsed = true;
		unset($finalRules, $rules, $parsed, $rulesParsed, $ruleParsed, $defaultRules, $inherited);

		return $this;
	}

	/**
	 * Fix tables.
	 *
	 * @param bool $removeBottomBorders
	 *
	 * @return $this
	 */
	public function fixTables(bool $removeBottomBorders)
	{
		$box = $this->getBox();
		if ($box->wasCut()) {
			return $this;
		}
		$boxStyle = $box->getStyle();
		$boxes = [$box];
		if (!$box instanceof TableBox) {
			$boxes = $box->getBoxesByType('TableBox');
		}
		foreach ($boxes as $box) {
			// max cell borders widths top,right,bottom,left
			$cellBorders = ['0', '0', '0', '0'];
			$rowGroups = $box->getChildren(true, true);
			$rowGroupsCount = count($rowGroups);
			foreach ($rowGroups as $rowGroupIndex => $rowGroup) {
				$rows = $rowGroup->getChildren(true, true);
				$rowsCount = count($rows);
				foreach ($rows as $rowIndex => $row) {
					$columns = $row->getChildren(true, true);
					$columnsCount = count($columns);
					foreach ($columns as $columnIndex => $column) {
						$rowStyle = $column->getParent()->getStyle();
						$columnStyle = $column->getStyle();
						if ($columnIndex + 1 < $columnsCount) {
							$columnStyle->setRule('padding-right', '0');
						}
						if (!($rowIndex + $row->getRowSpan() === $rowsCount && $rowGroupIndex + 1 === $rowGroupsCount)) {
							$columnStyle->setRule('padding-bottom', '0');
						} elseif ($columnStyle->getRules('border-collapse') === 'separate') {
							$columnStyle->setRule('padding-bottom', $columnStyle->getRules('border-spacing'));
							$column->getParent()->measureHeight();
							$column->getParent()->getParent()->measureHeight();
						}
						$cellBorders = [
							Math::max($cellBorders[0], $rowStyle->getRules('border-top-width')),
							Math::max($cellBorders[1], $rowStyle->getRules('border-right-width')),
							Math::max($cellBorders[2], $rowStyle->getRules('border-bottom-width')),
							Math::max($cellBorders[3], $rowStyle->getRules('border-left-width')),
						];
						if ($columnStyle->getRules('border-collapse') === 'collapse') {
							$cellStyle = $column->getFirstChild()->getStyle();
							$cellBorders = [
								Math::max($cellBorders[0], $cellStyle->getRules('border-top-width')),
								Math::max($cellBorders[1], $cellStyle->getRules('border-right-width')),
								Math::max($cellBorders[2], $cellStyle->getRules('border-bottom-width')),
								Math::max($cellBorders[3], $cellStyle->getRules('border-left-width')),
							];
							if ($rowIndex + $column->getRowSpan() < $rowsCount && $removeBottomBorders) {
								$cellStyle->setRule('border-bottom-width', '0');
							}
							if ($columnIndex + $column->getColSpan() < $columnsCount) {
								$cellStyle->setRule('border-right-width', '0');
							}
						}
						// move specified css width to proper elements
						$cellStyle = $column->getFirstChild()->getStyle();
						if ($cellStyle->getRules('width') !== 'auto') {
							$columnStyle->setRule('width', $cellStyle->getRules('width'));
							$cellStyle->setRule('width', 'auto');
						}
					}
					if ($boxStyle->getRules('border-collapse') === 'collapse') {
						$parentStyle = $box->getParent()->getStyle();
						if (Math::comp($cellBorders[0], $parentStyle->getRules('border-top-width')) >= 0) {
							$parentStyle->setRule('border-top-width', '0');
						}
						if (Math::comp($cellBorders[1], $parentStyle->getRules('border-right-width')) >= 0) {
							$parentStyle->setRule('border-right-width', '0');
						}
						if (Math::comp($cellBorders[2], $parentStyle->getRules('border-bottom-width')) >= 0) {
							$parentStyle->setRule('border-bottom-width', '0');
						}
						if (Math::comp($cellBorders[3], $parentStyle->getRules('border-left-width')) >= 0) {
							$parentStyle->setRule('border-left-width', '0');
						}
					}
				}
			}
			foreach ($box->getChildren() as $rowGroupIndex => $rowGroup) {
				$rows = $rowGroup->getChildren();
				if ($boxStyle->getRules('border-collapse') === 'collapse') {
					$rowsCount = count($rows) - 1;
					foreach ($rows as $rowIndex => $row) {
						if ($rowIndex < $rowsCount && $removeBottomBorders) {
							$row->getStyle()->setRule('border-bottom-width', '0');
						}
					}
				} else {
					foreach ($rows as $row) {
						$rowStyle = $row->getStyle();
						$rowStyle->setRule('border-top-width', '0');
						$rowStyle->setRule('border-right-width', '0');
						$rowStyle->setRule('border-bottom-width', '0');
						$rowStyle->setRule('border-left-width', '0');
						if ($rowStyle->getRules('background-color') !== 'transparent') {
							foreach ($row->getChildren() as $column) {
								$cell = $column->getFirstChild();
								$cell->getStyle()->setRule('background-color', $rowStyle->getRules('background-color'));
							}
							$rowStyle->setRule('background-color', 'transparent');
						}
					}
				}
			}
		}
		unset($rowGroup, $boxes, $rows, $columns);

		return $this;
	}

	/**
	 * Fix dom tree - after dom tree is parsed we must clean up or add some rules.
	 *
	 * @param bool $removeBottomBorders
	 *
	 * @return $this
	 */
	public function fixDomTree(bool $removeBottomBorders = true)
	{
		if ($this->getBox()->wasCut()) {
			return $this;
		}
		foreach ($this->box->getChildren() as $childBox) {
			$childBox->getStyle()->fixTables($removeBottomBorders);
			$childBox->getStyle()->fixDomTree($removeBottomBorders);
		}

		return $this;
	}

	/**
	 * Clear style for first inline element (in line).
	 *
	 * @return $this
	 */
	public function clearFirstInline()
	{
		$box = $this->getBox();
		$dimensions = $box->getDimensions();
		if ($dimensions->getWidth()) {
			$dimensions->setWidth(Math::sub($dimensions->getWidth(), $this->getFullRightSpace()));
		}
		$this->setRule('margin-right', '0');
		$this->setRule('padding-right', '0');
		$this->setRule('border-right-width', '0');
		if ($this->rules['display'] === 'inline') {
			$this->setRule('margin-top', '0');
			$this->setRule('margin-bottom', '0');
		}

		return $this;
	}

	/**
	 * Clear style for last inline element (in line).
	 *
	 * @return $this
	 */
	public function clearLastInline()
	{
		$box = $this->getBox();
		$leftSpace = $this->getFullLeftSpace();
		$dimensions = $box->getDimensions();
		if ($dimensions->getWidth()) {
			$dimensions->setWidth(Math::sub($dimensions->getWidth(), $leftSpace));
		}
		$offset = $box->getOffset();
		if ($offset->getLeft()) {
			$offset->setLeft(Math::sub($offset->getLeft(), $leftSpace));
		}
		$coordinates = $box->getCoordinates();
		if ($coordinates->getX()) {
			$coordinates->setX(Math::sub($coordinates->getX(), $leftSpace));
		}
		$this->setRule('margin-left', '0');
		$this->setRule('padding-left', '0');
		$this->setRule('border-left-width', '0');
		if ($this->rules['display'] === 'inline') {
			$this->setRule('margin-top', '0');
			$this->setRule('margin-bottom', '0');
		}

		return $this;
	}

	/**
	 * Clear style for middle inline element (in line).
	 *
	 * @return $this
	 */
	public function clearMiddleInline()
	{
		$box = $this->getBox();
		$leftSpace = $this->getFullLeftSpace();
		$dimensions = $box->getDimensions();
		if ($dimensions->getWidth()) {
			$sub = Math::add($this->getHorizontalMarginsWidth(), $this->getHorizontalBordersWidth());
			$dimensions->setWidth(Math::sub($dimensions->getWidth(), $sub));
		}
		$offset = $box->getOffset();
		if ($offset->getLeft()) {
			$offset->setLeft(Math::sub($offset->getLeft(), $leftSpace));
		}
		$coordinates = $box->getCoordinates();
		if ($coordinates->getX()) {
			$coordinates->setX(Math::sub($coordinates->getX(), $leftSpace));
		}
		$this->setRule('margin-left', '0');
		$this->setRule('margin-right', '0');
		$this->setRule('padding-left', '0');
		$this->setRule('padding-right', '0');
		$this->setRule('border-right-width', '0');
		$this->setRule('border-left-width', '0');
		if ($this->rules['display'] === 'inline') {
			$this->setRule('margin-top', '0');
			$this->setRule('margin-bottom', '0');
		}

		return $this;
	}

	/**
	 * Parse css rules.
	 *
	 * @param string $cssString
	 *
	 * @return $this
	 */
	public function parseCss(string $cssString): self
	{
		$css = (new CSSParser($cssString))->parse();
		foreach ($css->getAllRuleSets() as $ruleSet) {
			$selector = implode(' ', $ruleSet->getSelector());
			$selectorRules = (array) $ruleSet->getRules();
			$rules = [];
			foreach ($selectorRules as $rule) {
				$rules[$rule->getRule()] = trim((string) $rule->getValue(), '" \'');
			}
			$this->document->addCssSelectorRules($selector, $rules);
		}

		return $this;
	}

	/**
	 * Get transformations.
	 *
	 * @return string
	 */
	public function getTransformations(string $x, string $y)
	{
		return "1 0 0 1 ${x} ${y} cm";
	}

	/**
	 * Clone.
	 *
	 * @param Box $box
	 *
	 * @return Style
	 */
	public function clone(Box $box)
	{
		$newStyle = clone $this;
		$newStyle->setBox($box);

		return $newStyle;
	}

	public function __clone()
	{
		$this->font = clone $this->font;
		if ($this->element) {
			$this->element = clone $this->element;
		}
	}
}
