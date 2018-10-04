<?php
declare(strict_types=1);
/**
 * Layout class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Style\Style;

/**
 * Class Layout
 */
class Layout extends \YetiForcePDF\Base
{
	/**
	 * @var Box[]
	 */
	protected $boxes = [];

	/**
	 * @var Style
	 */
	protected $style;

	/**
	 * Get lines
	 * @return \YetiForcePDF\Layout\Box[]
	 */
	public function getBoxes()
	{
		return $this->boxes;
	}

	/**
	 * Append line
	 * @param \YetiForcePDF\Layout\Box $box
	 * @return $this
	 */
	public function appendBox(Box $box)
	{
		$this->boxes[] = $box;
		return $this;
	}

	/**
	 * Set style
	 * @param \YetiForcePDF\Style\Style $style
	 * @return $this
	 */
	public function setStyle(Style $style)
	{
		$this->style = $style;
		return $this;
	}

	/**
	 * Get height
	 * @return float
	 */
	public function getHeight()
	{
		$height = 0;
		foreach ($this->getBoxes() as $box) {
			$height += $box->getHeight();
		}
		return $height;
	}

	/**
	 * Arrange elements inside boxes
	 * @return $this
	 */
	public function reflow()
	{
		$element = $this->style->getElement();
		$style = $this->style;
		$rules = $style->getRules();
		if ($parent = $style->getParent()) {
			$parentCoordinates = $parent->getCoordinates();
			$parentDimensions = $parent->getDimensions();
			$parentRules = $parent->getRules();
			$offsetLeft = $parentRules['border-left-width'] + $parentRules['padding-left'];
			$offsetTop = $parentRules['border-top-width'] + $parentRules['padding-top'];
			$left = $parentCoordinates->getAbsoluteHtmlX() + $offsetLeft + $rules['margin-left'];
			$top = $parentCoordinates->getAbsoluteHtmlY() + $offsetTop + $rules['margin-top'];
			if ($element->isTextNode()) {
				$width = $style->getDimensions()->getTextWidth();
				$height = $style->getDimensions()->getTextHeight();
			} elseif ($rules['display'] === 'block') {
				$width = $parentDimensions->getInnerWidth() - $rules['margin-left'] - $rules['margin-right'];
			} else {
				$width = 0; // will be calculated later
			}
			$parentLayout = $style->getParent()->getLayout();
			foreach ($parentLayout->getBoxes() as $box) {
				$boxHeight = $box->getHeight();
				$top += $boxHeight;
				$offsetTop += $boxHeight;
			}
		} else {
			// absolute coordinates and offsets from page margins
			$offsetLeft = $this->document->getCurrentPage()->getCoordinates()->getAbsoluteHtmlX();
			$offsetTop = $this->document->getCurrentPage()->getCoordinates()->getAbsoluteHtmlY();
			$left = $offsetLeft;
			$top = $offsetTop;
			$width = $this->document->getCurrentPage()->getPageDimensions()->getInnerWidth();
		}

		$boxLeft = $left + $rules['border-left-width'] + $rules['padding-left'];
		$boxTop = $top + $rules['border-top-width'] + $rules['padding-top'];

		$offset = $this->style->getOffset();
		$offset->setLeft($offsetLeft);
		$offset->setTop($offsetTop);
		$coordinates = $this->style->getCoordinates();
		$coordinates->setAbsoluteHtmlX($left);
		$coordinates->setAbsoluteHtmlY($top);
		$coordinates->convertHtmlToPdf();
		$dimensions = $this->style->getDimensions();
		$dimensions->setWidth($width);
		if ($element->isTextNode()) {
			$dimensions->setInnerWidth($width);
			$dimensions->setHeight($height);
			$dimensions->setInnerHeight($height);
			return $this;
			// no more calculations are needed - text node doesn't have children
		}
		$paddingWidth = $rules['padding-left'] + $rules['padding-right'];
		$borderWidth = $rules['border-left-width'] + $rules['border-right-width'];
		$dimensions->setInnerWidth($dimensions->getWidth() - $paddingWidth - $borderWidth);
		// initial positioning and dimensions are set for block box because first element is always block box
		// now iterate through children and convert it to line boxes or another block box
		$lineChildren = [];
		$lineChildrenWidth = 0;
		$lineChildrenHeight = 0;
		$currentLeft = $boxLeft;
		$height = 0;
		foreach ($this->style->getChildren() as $child) {
			$childRules = $child->getRules();
			$childDimensions = $child->getDimensions();
			$childOffset = $child->getOffset();
			$childCoordinates = $child->getCoordinates();
			if ($childRules['display'] === 'block') {
				// close line and add block box after if needed
				if (!empty($lineChildren)) {
					$line = (new LineBox())->setDocument($this->document)
						->setStyles($lineChildren)
						->setChildrenWidth($lineChildrenWidth)
						->setChildrenHeight($lineChildrenHeight)
						->setLeftPosition($boxLeft)
						->setTopPosition($boxTop + $offsetTop)
						->init();
					$this->appendBox($line);
					$height += $lineChildrenHeight;
					$offsetTop += $lineChildrenHeight;
					$lineChildren = [];
					$lineChildrenWidth = 0;
					$lineChildrenHeight = 0;
				}
				$child->getLayout()->reflow();
				$childHeight = $child->getLayout()->getHeight();
				$blockBox = (new BlockBox())->setDocument($this->document)->setStyles([$child])
					->setChildrenWidth($dimensions->getInnerWidth())
					->setChildrenHeight($childHeight)
					->setLeftPosition($boxLeft)
					->setTopPosition($boxTop + $offsetTop)
					->init();
				$this->appendBox($blockBox);
				$height += $childHeight;
				$offsetTop += $childHeight;
				continue;
			}
			// calculate child position and dimension
			$child->getLayout()->reflow();
			// offset left are set at 0,0 after reflow so correct it
			$childOffset->setLeft($lineChildrenWidth + $childRules['margin-left']);
			$childOffset->setTop($offsetTop + $childRules['margin-top']);
			$childCoordinates->setAbsoluteHtmlX($boxLeft + $childOffset->getLeft());
			$childCoordinates->setAbsoluteHtmlY($boxTop + $childOffset->getTop());
			$childCoordinates->convertHtmlToPdf();
			$childMarginWidth = $childRules['margin-left'] + $childRules['margin-right'];
			$childMarginHeight = $childRules['margin-top'] + $childRules['margin-bottom'];
			$lineChildrenHeight = max($lineChildrenHeight, $child->getDimensions()->getHeight() + $childMarginHeight);
			$lineChildrenWidth += $child->getDimensions()->getWidth() + $childMarginWidth;
			$lineChildren[] = $child;
		}
		// add collected boxes inside line
		if (!empty($lineChildren)) {
			$line = (new LineBox())->setDocument($this->document)
				->setStyles($lineChildren)
				->setChildrenWidth($lineChildrenWidth)
				->setChildrenHeight($lineChildrenHeight)
				->setLeftPosition($boxLeft)
				->setTopPosition($boxTop)
				->init();
			$this->appendBox($line);
		}
		$paddingHeight = $rules['padding-top'] + $rules['padding-bottom'];
		$borderHeight = $rules['border-top-width'] + $rules['border-bottom-width'];
		if ($rules['height'] !== 'auto' && $rules['display'] !== 'inline') {
			$dimensions->setInnerHeight($rules['height']);
			$dimensions->setHeight($rules['height'] + $paddingHeight + $borderHeight);
		} else {
			$dimensions->setInnerHeight($height);
			$dimensions->setHeight($height + $paddingHeight + $borderHeight);
		}
		return $this;
	}
}
