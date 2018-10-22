<?php
declare(strict_types=1);
/**
 * InlineBox class
 *
 * @package   YetiForcePDF\Render
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render;

use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;
use \YetiForcePDF\Render\Coordinates\Coordinates;
use \YetiForcePDF\Render\Coordinates\Offset;
use \YetiForcePDF\Render\Dimensions\BoxDimensions;

/**
 * Class InlineBox
 */
class InlineBox extends ElementBox implements BoxInterface, BuildTreeInterface, AppendChildInterface
{

	/**
	 * Anonymous inline element is created to wrap TextBox
	 * @var bool
	 */
	protected $anonymous = false;

	/**
	 * Is this box anonymous
	 * @return bool
	 */
	public function isAnonymous()
	{
		return $this->anonymous;
	}

	/**
	 * Set anonymous field
	 * @param bool $anonymous
	 * @return $this
	 */
	public function setAnonymous(bool $anonymous)
	{
		$this->anonymous = $anonymous;
		return $this;
	}

	/**
	 * Go up to Line box and clone and wrap element
	 * @param Box $box
	 * @return $this
	 */
	public function cloneParent(Box $box)
	{
		if ($parent = $this->getParent()) {
			$clone = clone $this;
			$clone->getStyle()->setBox($clone);
			$clone->getDimensions()->setBox($clone);
			$clone->getOffset()->setBox($clone);
			$clone->getElement()->setBox($clone);
			$clone->appendChild($box);
			if (!$parent instanceof LineBox) {
				$parent->cloneParent($clone);
			} else {
				$parent->appendChild($clone);
			}
		}
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function appendBlock($childDomElement, $element, $style, $parentBlock)
	{
		$box = (new BlockBox())
			->setDocument($this->document)
			->setParent($this)
			->setElement($element)
			->setStyle($style)
			->init();
		// if we add this child to parent box we loose parent inline styles if nested
		// so we need to wrap this box later and split lines at block element
		if (isset($this->getChildren()[0])) {
			$this->cloneParent($box);
		} else {
			$this->appendChild($box);
		}
		$box->getStyle()->init();
		$box->buildTree($box);
		return $box;
	}

	/**
	 * {@inheritdoc}
	 */
	public function appendInlineBlock($childDomElement, $element, $style, $parentBlock)
	{
		$box = (new InlineBlockBox())
			->setDocument($this->document)
			->setParent($this)
			->setElement($element)
			->setStyle($style)
			->init();
		// if we add this child to parent box we loose parent inline styles if nested
		// so we need to wrap this box later and split lines at block element
		if (isset($this->getChildren()[0])) {
			$this->cloneParent($box);
		} else {
			$this->appendChild($box);
		}
		$box->getStyle()->init();
		$box->buildTree($box);
		return $box;
	}

	/**
	 * {@inheritdoc}
	 */
	public function appendInline($childDomElement, $element, $style, $parentBlock)
	{
		$box = (new InlineBox())
			->setDocument($this->document)
			->setParent($this)
			->setElement($element)
			->setStyle($style)
			->init();
		if (isset($this->getChildren()[0])) {
			$this->cloneParent($box);
		} else {
			$this->appendChild($box);
		}
		$box->getStyle()->init();
		$box->buildTree($parentBlock);
		return $box;
	}

	/**
	 * Add text
	 * @param \DOMNode                           $childDomElement
	 * @param Element                            $element
	 * @param Style                              $style
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
	 * @return \YetiForcePDF\Render\TextBox
	 */
	public function appendText($childDomElement, $element = null, $style = null, $parentBlock = null)
	{
		$box = (new TextBox())
			->setDocument($this->document)
			->setParent($this)
			->init();
		if (isset($this->getChildren()[0])) {
			$this->cloneParent($box);
		} else {
			$this->appendChild($box);
		}
		$box->setText($childDomElement->textContent);
		return $box;
	}

	/**
	 * Measure width
	 * @return $this
	 */
	public function measureWidth()
	{
		$width = 0;
		foreach ($this->getChildren() as $child) {
			$child->measureWidth();
			$width += $child->getDimensions()->getOuterWidth();
		}
		$style = $this->getStyle();
		$width += $style->getHorizontalBordersWidth() + $style->getHorizontalPaddingsWidth();
		$this->getDimensions()->setWidth($width);
		$this->applyStyleWidth();
		return $this;
	}

	/**
	 * Measure height
	 * @return $this
	 */
	public function measureHeight()
	{
		foreach ($this->getChildren() as $child) {
			$child->measureHeight();
		}
		$this->getDimensions()->setHeight($this->getStyle()->getLineHeight());
		$this->applyStyleHeight();
		return $this;
	}

	/**
	 * Position
	 * @return $this
	 */
	public function measureOffset()
	{
		$rules = $this->getStyle()->getRules();
		$parent = $this->getParent();
		$top = $parent->getStyle()->getOffsetTop();
		$lineHeight = $this->getClosestLineBox()->getDimensions()->getHeight();
		if ($rules['vertical-align'] === 'bottom') {
			$top = $lineHeight - $this->getDimensions()->getHeight();
		} elseif ($rules['vertical-align'] === 'top') {
			$top = 0;
		} elseif ($rules['vertical-align'] === 'middle' || $rules['vertical-align'] === 'baseline') {
			$height = $this->getDimensions()->getHeight();
			$top = (float)bcsub(bcdiv((string)$lineHeight, '2', 4), bcdiv((string)$height, '2', 4), 4);
		}
		// margin top inside inline and inline block doesn't affect relative to line top position
		// it only affects line margins
		$left = $rules['margin-left'];
		if ($previous = $this->getPrevious()) {
			$left += $previous->getOffset()->getLeft() + $previous->getDimensions()->getOuterWidth();
		} else {
			$left += $parent->getStyle()->getOffsetLeft();
		}
		$this->getOffset()->setLeft($left);
		$this->getOffset()->setTop($top);
		foreach ($this->getChildren() as $child) {
			$child->measureOffset();
		}
		return $this;
	}

	/**
	 * Position
	 * @return $this
	 */
	public function measurePosition()
	{
		$parent = $this->getParent();
		$this->getCoordinates()->setX($parent->getCoordinates()->getX() + $this->getOffset()->getLeft());
		$parent = $this->getClosestLineBox();
		$this->getCoordinates()->setY($parent->getCoordinates()->getY() + $this->getOffset()->getTop());
		foreach ($this->getChildren() as $child) {
			$child->measurePosition();
		}
		return $this;
	}

	public function __clone()
	{
		$this->element = clone $this->element;
		$this->style = clone $this->style;
		$this->offset = clone $this->offset;
		$this->dimensions = clone $this->dimensions;
		$this->coordinates = clone $this->coordinates;
		$this->children = [];
	}

	/**
	 * Add border instructions
	 * @param array $element
	 * @param float $pdfX
	 * @param float $pdfY
	 * @param float $width
	 * @param float $height
	 * @return array
	 */
	protected function addBorderInstructions(array $element, float $pdfX, float $pdfY, float $width, float $height)
	{
		$rules = $this->style->getRules();
		$x1 = 0;
		$x2 = $width;
		$y1 = $height;
		$y2 = 0;
		$element[] = '% start border';
		if ($rules['border-top-width'] && $rules['border-top-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x2, $y1]),
				implode(' ', [$x2 - $rules['border-right-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x1 + $rules['border-left-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x1, $y1])
			]);
			$borderTop = [
				'q',
				"{$rules['border-top-color'][0]} {$rules['border-top-color'][1]} {$rules['border-top-color'][2]} rg",
				"1 0 0 1 $pdfX $pdfY cm",
				"$x1 $y1 m", // move to start point
				$path . ' l h',
				'f',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		if ($rules['border-right-width'] && $rules['border-right-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x2, $y2]),
				implode(' ', [$x2 - $rules['border-right-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x2 - $rules['border-right-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x2, $y1]),
			]);
			$borderTop = [
				'q',
				"1 0 0 1 $pdfX $pdfY cm",
				"{$rules['border-right-color'][0]} {$rules['border-right-color'][1]} {$rules['border-right-color'][2]} rg",
				"$x2 $y1 m",
				$path . ' l h',
				'f',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		if ($rules['border-bottom-width'] && $rules['border-bottom-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x2, $y2]),
				implode(' ', [$x2 - $rules['border-right-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x1 + $rules['border-left-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x1, $y2]),
			]);
			$borderTop = [
				'q',
				"1 0 0 1 $pdfX $pdfY cm",
				"{$rules['border-bottom-color'][0]} {$rules['border-bottom-color'][1]} {$rules['border-bottom-color'][2]} rg",
				"$x1 $y2 m",
				$path . ' l h',
				'f',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		if ($rules['border-left-width'] && $rules['border-left-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x1 + $rules['border-left-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x1 + $rules['border-left-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x1, $y2]),
				implode(' ', [$x1, $y1]),
			]);
			$borderTop = [
				'q',
				"1 0 0 1 $pdfX $pdfY cm",
				"{$rules['border-left-color'][0]} {$rules['border-left-color'][1]} {$rules['border-left-color'][2]} rg",
				"$x1 $y1 m",
				$path . ' l h',
				'f',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		$element[] = '% end border';
		return $element;
	}

	public function addBackgroundColorInstructions(array $element, float $pdfX, float $pdfY, float $width, float $height)
	{
		$rules = $this->style->getRules();
		if ($rules['background-color'] !== 'transparent') {
			$bgColor = [
				'q',
				"1 0 0 1 $pdfX $pdfY cm",
				"{$rules['background-color'][0]} {$rules['background-color'][1]} {$rules['background-color'][2]} rg",
				"0 0 $width $height re",
				'f',
				'Q'
			];
			$element = array_merge($element, $bgColor);
		}
		return $element;
	}

	/**
	 * Get element PDF instructions to use in content stream
	 * @return string
	 */
	public function getInstructions(): string
	{
		$coordinates = $this->getCoordinates();
		$pdfX = $coordinates->getPdfX();
		$pdfY = $coordinates->getPdfY();
		$dimensions = $this->getDimensions();
		$width = $dimensions->getWidth();
		$height = $dimensions->getHeight();
		$element = [];
		$element = $this->addBackgroundColorInstructions($element, $pdfX, $pdfY, $width, $height);
		$element = $this->addBorderInstructions($element, $pdfX, $pdfY, $width, $height);
		return implode("\n", $element);
	}
}
