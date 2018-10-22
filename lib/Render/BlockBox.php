<?php
declare(strict_types=1);
/**
 * BlockBox class
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
 * Class BlockBox
 */
class BlockBox extends ElementBox implements BoxInterface, AppendChildInterface, BuildTreeInterface
{

	/**
	 * @var \YetiForcePDF\Render\LineBox
	 */
	protected $currentLineBox;

	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		parent::init();
		$this->dimensions = (new BoxDimensions())
			->setDocument($this->document)
			->setBox($this)
			->init();
		$this->coordinates = (new Coordinates())
			->setDocument($this->document)
			->setBox($this)
			->init();
		$this->offset = (new Offset())
			->setDocument($this->document)
			->setBox($this)
			->init();
		return $this;
	}

	/**
	 * Get element
	 * @return Element
	 */
	public function getElement()
	{
		return $this->element;
	}

	/**
	 * Set element
	 * @param Element $element
	 * @return $this
	 */
	public function setElement(Element $element)
	{
		$this->element = $element;
		$element->setBox($this);
		return $this;
	}

	/**
	 * Prepare tree - divide each string into characters DOMText - each character will be DOMText from now
	 * This method exists only inside BlockBox because root element is always BlockBox (recurrence)
	 * @return $this
	 */
	public function prepareTree($domElement)
	{
		// clone tree because we don't want to modify source of truth
		if ($this->isRoot()) {
			$domElement = $domElement->cloneNode(true);
		}
		/*if ($domElement->hasChildNodes()) {
			foreach ($domElement->childNodes as $childNode) {

			}
		}*/
		$this->domTree = $domElement;
		return $this;
	}

	/**
	 * Get new line box
	 * @return \YetiForcePDF\Render\LineBox
	 */
	public function getNewLineBox()
	{
		$this->currentLineBox = (new LineBox())->setDocument($this->document)->init();
		$this->appendChild($this->currentLineBox);
		$this->currentLineBox->getDimensions()->computeAvailableSpace();
		return $this->currentLineBox;
	}

	/**
	 * Close line box
	 * @param \YetiForcePDF\Render\LineBox|null $lineBox
	 * @param bool                              $createNew
	 * @return \YetiForcePDF\Render\LineBox
	 */
	public function closeLine()
	{
		$this->currentLineBox = null;
		return $this->currentLineBox;
	}

	/**
	 * Get current linebox
	 * @return \YetiForcePDF\Render\LineBox
	 */
	public function getCurrentLineBox()
	{
		return $this->currentLineBox;
	}

	/**
	 * Append block box element
	 * @param \DOMNode                           $childDomElement
	 * @param Element                            $element
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
	 * @return \YetiForcePDF\Render\BlockBox
	 */
	public function appendBlock($childDomElement, $element, $parentBlock)
	{
		if ($this->getCurrentLineBox()) {
			$this->closeLine();
		}
		$box = (new BlockBox())
			->setDocument($this->document)
			->setParent($this)
			->setElement($element)
			->setStyle($element->parseStyle())//second phase with css inheritance
			->init();
		$this->appendChild($box);
		$box->buildTree($box);
		return $box;
	}

	/**
	 * Append inline block box element
	 * @param \DOMNode                           $childDomElement
	 * @param Element                            $element
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
	 * @return \YetiForcePDF\Render\InlineBlockBox
	 */
	public function appendInlineBlock($childDomElement, $element, $parentBlock)
	{
		if ($this->getCurrentLineBox()) {
			$currentLineBox = $this->getCurrentLineBox();
		} else {
			$currentLineBox = $this->getNewLineBox();
		}
		$box = (new InlineBlockBox())
			->setDocument($this->document)
			->setParent($currentLineBox)
			->setElement($element)
			->setStyle($element->parseStyle())
			->init();
		$currentLineBox->appendChild($box);
		$box->buildTree($this);
		return $box;
	}

	/**
	 * Add inline child (and split text to individual characters)
	 * @param \DOMNode                           $childDomElement
	 * @param Element                            $element
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
	 * @return \YetiForcePDF\Render\InlineBox
	 */
	public function appendInline($childDomElement, $element, $parentBlock)
	{
		if ($this->getCurrentLineBox()) {
			$currentLineBox = $this->getCurrentLineBox();
		} else {
			$currentLineBox = $this->getNewLineBox();
		}
		$box = (new InlineBox())
			->setDocument($this->document)
			->setParent($currentLineBox)
			->setElement($element)
			->setStyle($element->parseStyle())
			->init();
		$currentLineBox->appendChild($box);
		$box->buildTree($this);
		return $box;
	}

	/**
	 * Measure width of this block
	 * @return $this
	 */
	public function measureWidth()
	{
		$dimensions = $this->getDimensions();
		$parent = $this->getParent();
		if ($parent) {
			if ($parent->getDimensions()->getWidth() !== null) {
				$dimensions->setWidth($parent->getDimensions()->getInnerWidth() - $this->getStyle()->getHorizontalMarginsWidth());
				foreach ($this->getChildren() as $child) {
					$child->measureWidth();
				}
				$this->divideLines();
				return $this;
			}
			foreach ($this->getChildren() as $child) {
				$child->measureWidth();
			}
			$this->divideLines();
			$maxWidth = 0;
			foreach ($this->getChildren() as $child) {
				$maxWidth = max($maxWidth, $child->getDimensions()->getOuterWidth());
			}
			$style = $this->getStyle();
			$maxWidth += $style->getHorizontalBordersWidth() + $style->getHorizontalPaddingsWidth();
			$maxWidth -= $style->getHorizontalMarginsWidth();
			$dimensions->setWidth($maxWidth);
			return $this;
		}
		$dimensions->setWidth($this->document->getCurrentPage()->getDimensions()->getWidth());
		$this->applyStyleWidth();
		foreach ($this->getChildren() as $child) {
			$child->measureWidth();
		}
		$this->divideLines();
		return $this;
	}

	/**
	 * Divide lines
	 * @return $this
	 */
	public function divideLines()
	{
		foreach ($this->getChildren() as $child) {
			if ($child instanceof LineBox) {
				$lines = $child->divide();
				foreach ($lines as $line) {
					$this->insertBefore($line, $child);
				}
				$this->removeChild($child);
			}
		}
		foreach ($this->getChildren() as $child) {
			$child->measureWidth();
		}
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
		$height = 0;
		foreach ($this->getChildren() as $child) {
			$height += $child->getDimensions()->getOuterHeight();
		}
		$rules = $this->getStyle()->getRules();
		$height += $rules['border-top-width'] + $rules['padding-top'];
		$height += $rules['border-bottom-width'] + $rules['padding-bottom'];
		$this->getDimensions()->setHeight($height);
		$this->applyStyleHeight();
		return $this;
	}

	/**
	 * Offset elements
	 * @return $this
	 */
	public function measureOffset()
	{
		$top = $this->document->getCurrentPage()->getCoordinates()->getY();
		$left = $this->document->getCurrentPage()->getCoordinates()->getX();
		$marginTop = $this->getStyle()->getRules('margin-top');
		if ($parent = $this->getParent()) {
			$parentStyle = $parent->getStyle();
			$top = $parentStyle->getOffsetTop();
			$left = $parentStyle->getOffsetLeft();
			if ($previous = $this->getPrevious()) {
				$top = $previous->getOffset()->getTop() + $previous->getDimensions()->getHeight();
				if ($previous->getStyle()->getRules('display') === 'block') {
					$marginTop = max($marginTop, $previous->getStyle()->getRules('margin-bottom'));
				} elseif (!$previous instanceof LineBox) {
					$marginTop += $previous->getStyle()->getRules('margin-bottom');
				}
			}
		}
		$top += $marginTop;
		$left += $this->getStyle()->getRules('margin-left');
		$this->getOffset()->setTop($top);
		$this->getOffset()->setLeft($left);
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
		$x = $this->document->getCurrentPage()->getCoordinates()->getX();
		$y = $this->document->getCurrentPage()->getCoordinates()->getY();
		if ($parent = $this->getParent()) {
			$x = $parent->getCoordinates()->getX() + $this->getOffset()->getLeft();
			$y = $parent->getCoordinates()->getY() + $this->getOffset()->getTop();
		}
		$this->getCoordinates()->setX($x);
		$this->getCoordinates()->setY($y);
		foreach ($this->getChildren() as $child) {
			$child->measurePosition();
		}
		return $this;
	}

	/**
	 * Layout elements
	 * @return $this
	 */
	public function layout()
	{
		$this->measureWidth();
		$this->measureHeight();
		$this->measureOffset();
		$this->alignText();
		$this->measurePosition();
		return $this;
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
			$x1 = 0;
			$y1 = $height;
			$x2 = $width;
			$y2 = 0;
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
