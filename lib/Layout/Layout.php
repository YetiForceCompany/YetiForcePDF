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
	 * @var LineBox[]
	 */
	protected $lines = [];

	/**
	 * @var Style
	 */
	protected $style;

	/**
	 * Get lines
	 * @return \YetiForcePDF\Layout\LineBox[]
	 */
	public function getLines()
	{
		return $this->lines;
	}

	/**
	 * Append line
	 * @param \YetiForcePDF\Layout\LineBox $line
	 * @return $this
	 */
	public function appendBox(Box $line)
	{
		$this->lines[] = $line;
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
	 * Get inner width - maximal width of line or 0 if there are no children (empty layout)
	 * Layout is for elements with children
	 * @return int
	 */
	public function getInnerWidth()
	{
		$width = 0;
		foreach ($this->getLines() as $line) {
			$width = max($width, $line->getInnerWidth());
		}
		return $width;
	}

	/**
	 * Get inner width
	 * @return int
	 */
	public function getInnerHeight()
	{
		$height = 0;
		foreach ($this->getLines() as $line) {
			$height += $line->getInnerHeight();
		}
		return $height;
	}

	/**
	 * Arrange elements inside lines
	 * @return $this
	 */
	public function reflow()
	{
		$rules = $this->style->getRules();
		if ($parent = $this->style->getParent()) {
			$parentCoordinates = $parent->getCoordinates();
			$parentDimensions = $parent->getDimensions();
			$parentRules = $parent->getRules();
			$offsetLeft = $parentRules['border-left-width'] + $parentRules['padding-left'];
			$offsetTop = $parentRules['border-top-width'] + $parentRules['padding-top'];
			$left = $parentCoordinates->getAbsoluteHtmlX() + $offsetLeft + $rules['margin-left'];
			$top = $parentCoordinates->getAbsoluteHtmlY() + $offsetTop + $rules['margin-top'];
			$width = $parentDimensions->getInnerWidth() - $rules['margin-left'] - $rules['margin-right'];
		} else {
			// absolute coordinates and offsets from page margins
			$offsetLeft = $this->document->getCurrentPage()->getCoordinates()->getAbsoluteHtmlX();
			$offsetTop = $this->document->getCurrentPage()->getCoordinates()->getAbsoluteHtmlY();
			$left = $offsetLeft;
			$top = $offsetTop;
			$width = $this->document->getCurrentPage()->getPageDimensions()->getInnerWidth();
		}
		$offset = $this->style->getOffset();
		$offset->setLeft($offsetLeft);
		$offset->setTop($offsetTop);
		$coordinates = $this->style->getCoordinates();
		$coordinates->setAbsoluteHtmlX($left);
		$coordinates->setAbsoluteHtmlY($top);
		$coordinates->convertHtmlToPdf();
		$dimensions = $this->style->getDimensions();
		$dimensions->setWidth($width);
		$paddingWidth = $rules['padding-left'] + $rules['padding-right'];
		$borderWidth = $rules['border-left-width'] + $rules['border-right-width'];
		$dimensions->setInnerWidth($dimensions->getWidth() - $paddingWidth - $borderWidth);
		// initial positioning and dimensions are set for block box because first element is always block box
		// now iterate through children and convert it to line boxes or another block box
		$lineChildren = [];
		$lineChildrenWidth = 0;
		$lineChildrenHeight = 0;
		$lineLeft = $left + $rules['border-left-width'] + $rules['padding-left'];
		$lineTop = $top + $rules['border-top-width'] + $rules['padding-top'];
		$currentLeft = $lineLeft;
		$height = 0;
		foreach ($this->style->getChildren() as $child) {
			$childRules = $child->getRules();
			if ($childRules['display'] === 'block') {
				// close line and add block box after if needed
				if (!empty($lineChildren)) {
					$line = (new LineBox())->setDocument($this->document)
						->setStyles($lineChildren)
						->setChildrenWidth($lineChildrenWidth)
						->setChildrenHeight($lineChildrenHeight)
						->setLeftPosition($lineLeft)
						->setTopPosition($lineTop)
						->init();
					$this->appendLine($line);
					$lineTop += $lineChildrenHeight;
					$lineChildren = [];
					$lineChildrenWidth = 0;
					$lineChildrenHeight = 0;
					$currentLeft = $lineLeft;
				}
				$this->appendBlock($child);
				$child->getLayout()->reflow();
				continue;
			}
			// calculate child position and dimension
			$childDimensions = $child->getDimensions();
			$childCoordinates = $child->getCoordinates();
			$childOffset = $child->getOffset();

		}
		$paddingHeight = $rules['padding-top'] + $rules['padding-bottom'];
		$borderHeight = $rules['border-top-width'] + $rules['border-bottom-width'];
		if ($rules['height'] !== 'auto') {
			$dimensions->setInnerHeight($rules['height']);
			$dimensions->setHeight($rules['height'] + $paddingHeight + $borderHeight);
		} else {
			$dimensions->setInnerHeight($height);
			$dimensions->setHeight($height + $paddingHeight + $borderHeight);
		}
		return $this;
	}
}
