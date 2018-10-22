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
		foreach ($this->getChildren() as $child) {
			$child->measureWidth();
		}
		$this->divideLines();
		$maxWidth = 0;
		foreach ($this->getChildren() as $child) {
			$child->measureWidth();
			$maxWidth = max($maxWidth, $child->getDimensions()->getOuterWidth());
		}
		$style = $this->getStyle();
		$maxWidth += $style->getHorizontalBordersWidth() + $style->getHorizontalPaddingsWidth();
		$this->getDimensions()->setWidth($maxWidth);
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
			$child->measureHeight();
			$height += $child->getDimensions()->getOuterHeight();
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
		$parent = $this->getParent();
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
		if (!$parent instanceof InlineBox) {
			$this->getCoordinates()->setY($parent->getCoordinates()->getY() + $this->getOffset()->getTop());
		} else {
			$this->getCoordinates()->setY($this->getClosestLineBox()->getCoordinates()->getY());
		}
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
}
