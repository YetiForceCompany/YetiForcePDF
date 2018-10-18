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
class InlineBox extends ElementBox implements BoxInterface
{

	/**
	 * @var string
	 */
	protected $text;

	/**
	 * Set text
	 * @param string $text
	 * @return $this
	 */
	public function setText(string $text)
	{
		$this->text = $text;
		return $this;
	}

	/**
	 * Get text
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
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
	 * Append block box element
	 * @param \DOMNode                      $childDomElement
	 * @param Element                       $element
	 * @param \YetiForcePDF\Render\BlockBox $parentBlock
	 * @return $this
	 */
	public function appendBlock($childDomElement, $element, $parentBlock)
	{
		$box = (new BlockBox())
			->setDocument($this->document)
			->setElement($element)
			->setStyle($element->parseStyle())//second phase with css inheritance
			->init();
		// if we add this child to parent box we loose parent inline styles if nested
		// so we need to wrap this box later and split lines at block element
		if (isset($currentChildren[0])) {
			$this->cloneParent($box);
		} else {
			$this->appendChild($box);
		}
		$box->buildTree($box);
		return $this;
	}

	/**
	 * Append inline block box element
	 * @param \DOMNode                           $childDomElement
	 * @param Element                            $element
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
	 * @return $this
	 */
	public function appendInlineBlock($childDomElement, $element, $parentBlock)
	{
		$box = (new InlineBlockBox())
			->setDocument($this->document)
			->setElement($element)
			->setStyle($element->parseStyle())//second phase with css inheritance
			->init();
		// if we add this child to parent box we loose parent inline styles if nested
		// so we need to wrap this box later and split lines at block element
		if (isset($currentChildren[0])) {
			$this->cloneParent($box);
		} else {
			$this->appendChild($box);
		}
		$box->buildTree($box);
		return $this;
	}

	/**
	 * Add inline child (and split text to individual characters)
	 * @param \DOMNode                           $childDomElement
	 * @param Element                            $element
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
	 * @return $this
	 */
	public function appendInline($childDomElement, $element, $parentBlock)
	{
		$box = (new InlineBox())
			->setDocument($this->document)
			->setElement($element)
			->setStyle($element->parseStyle())
			->init();
		$currentChildren = $this->getChildren();
		if (isset($currentChildren[0])) {
			$this->cloneParent($box);
		} else {
			$this->appendChild($box);
		}
		if ($childDomElement instanceof \DOMText) {
			$box->setTextNode(true)->setText($childDomElement->textContent);
		} else {
			$box->buildTree($parentBlock);
		}
		return $this;
	}

	/**
	 * Measure width
	 * @return $this
	 */
	public function measureWidth()
	{
		$width = 0;
		foreach ($this->getChildren() as $child) {
			$width += $child->getDimensions()->getOuterWidth();
			$style = $this->getStyle();
			$width += $style->getHorizontalBordersWidth() + $style->getHorizontalPaddingsWidth();
		}
		if ($this->isTextNode()) {
			$width = $this->getStyle()->getFont()->getTextWidth($this->getText());
		}
		$this->getDimensions()->setWidth($width);
		return $this;
	}

	/**
	 * Measure height
	 * @return $this
	 */
	public function measureHeight()
	{
		if ($this->isTextNode()) {
			$this->getDimensions()->setHeight($this->getStyle()->getFont()->getTextHeight($this->getText()));
		} else {
			$height = 0;
			foreach ($this->getChildren() as $child) {
				if ($this->getStyle()->getRules('display') === 'inline') {
					$height += $child->getDimensions()->getHeight();
				} else {
					$height += $child->getDimensions()->getOuterHeight();
				}
			}
			$style = $this->getStyle();
			$height += $style->getVerticalBordersWidth() + $style->getVerticalPaddingsWidth();
			$this->getDimensions()->setHeight($height);
		}
		return $this;
	}

	/**
	 * Position
	 * @return $this
	 */
	public function measureOffset()
	{
		$rules = $this->getStyle()->getRules();
		$parent = $this->getClosestBox();
		$top = $parent->getStyle()->getOffsetTop();
		// margin top inside inline and inline block doesn't affect relative to line top position
		// it only affects line margins
		$left = $rules['margin-left'];
		if ($previous = $this->getPrevious()) {
			$left += $previous->getOffset()->getLeft() + $previous->getDimensions()->getWidth() + $previous->getStyle()->getRules('margin-right');
		} else {
			$left += $parent->getStyle()->getOffsetLeft();
		}
		$this->getOffset()->setLeft($left);
		$this->getOffset()->setTop($top);
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
		$this->getCoordinates()->setY($parent->getCoordinates()->getY() + $this->getOffset()->getTop());
		return $this;
	}

	/**
	 * Reflow
	 * @return $this
	 */
	public function reflow()
	{
		$this->getDimensions()->computeAvailableSpace();
		if ($this->isTextNode()) {
			$this->measureWidth();
			$this->measureHeight();
		}
		$this->measureOffset();
		$this->measurePosition();
		foreach ($this->getChildren() as $child) {
			$child->reflow();
		}
		if (!$this->isTextNode()) {
			$this->measureWidth();
			$this->measureHeight();
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
	 * Filter text
	 * Filter the text, this is applied to all text just before being inserted into the pdf document
	 * it escapes the various things that need to be escaped, and so on
	 *
	 * @return string
	 */
	protected function filterText($text)
	{
		$text = trim(preg_replace('/[\n\r\t\s]+/', ' ', mb_convert_encoding($text, 'UTF-8')));
		$text = preg_replace('/\s+/', ' ', $text);
		$text = mb_convert_encoding($text, 'UTF-16');
		return strtr($text, [')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r']);
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
		$style = $this->getStyle();
		$rules = $style->getRules();
		$font = $style->getFont();
		$fontStr = '/' . $font->getNumber() . ' ' . $font->getSize() . ' Tf';
		$coordinates = $this->getCoordinates();
		$pdfX = $coordinates->getPdfX();
		$pdfY = $coordinates->getPdfY();
		$htmlX = $coordinates->getX();
		$htmlY = $coordinates->getY();
		$dimensions = $this->getDimensions();
		$width = $dimensions->getWidth();
		$height = $dimensions->getHeight();
		$baseLine = $style->getFont()->getDescender();
		$baseLineY = $pdfY - $baseLine;
		if ($this->isTextNode()) {
			$textWidth = $style->getFont()->getTextWidth($this->getText());
			$textHeight = $style->getFont()->getTextHeight();
			$textContent = '(' . $this->filterText($this->getText()) . ')';
			$element = [
				'q',
				"1 0 0 1 $pdfX $baseLineY cm % html x:$htmlX y:$htmlY",
				"{$rules['color'][0]} {$rules['color'][1]} {$rules['color'][2]} rg",
				'BT',
				$fontStr,
				"$textContent Tj",
				'ET',
				'Q'
			];
			if ($this->drawTextOutline) {
				$element = array_merge($element, [
					'q',
					'1 w',
					'1 0 0 RG',
					"1 0 0 1 $pdfX $pdfY cm",
					"0 0 $textWidth $textHeight re",
					'S',
					'Q'
				]);
			}
		} else {
			$element = [];
			$element = $this->addBackgroundColorInstructions($element, $pdfX, $pdfY, $width, $height);
			$element = $this->addBorderInstructions($element, $pdfX, $pdfY, $width, $height);
		}
		return implode("\n", $element);
	}
}
