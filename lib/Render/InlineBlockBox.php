<?php
declare(strict_types=1);
/**
 * InlineBlockBox class
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
 * Class InlineBlockBox
 */
class InlineBlockBox extends BlockBox
{

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

	public function __clone()
	{
		$this->element = clone $this->element;
		$this->style = clone $this->style;
		$this->offset = clone $this->offset;
		$this->dimensions = clone $this->dimensions;
		$this->coordinates = clone $this->coordinates;
		$this->children = [];
	}
}
