<?php
declare(strict_types=1);
/**
 * TableCellBox class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Math;
use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;
use \YetiForcePDF\Layout\Coordinates\Coordinates;
use \YetiForcePDF\Layout\Coordinates\Offset;
use \YetiForcePDF\Layout\Dimensions\BoxDimensions;

/**
 * Class TableCellBox
 */
class TableCellBox extends BlockBox
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
            $maxWidth = Math::comp((string)$maxWidth, (string)$child->getDimensions()->getOuterWidth()) > 0 ? $maxWidth : $child->getDimensions()->getOuterWidth();
        }
        // do not set up width because it was set by TableBox measureWidth method
        return $this;
    }

    /**
     * Measure height
     * @return $this
     */
    public function measureHeight()
    {
        $height = '0';
        foreach ($this->getChildren() as $child) {
            $child->measureHeight();
            $height = Math::add($height, (string)$child->getDimensions()->getOuterHeight());
        }
        $dimensions = $this->getDimensions();
        $style = $this->getStyle();
        $height = (float)Math::add($height, Math::add((string)$style->getVerticalBordersWidth(), (string)$style->getVerticalPaddingsWidth()));
        $dimensions->setHeight($height);
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
        // margin top inside inline and inline block doesn't affect relative to line top position
        // it only affects line margins
        $left = (string)$rules['margin-left'];
        if ($previous = $this->getPrevious()) {
            $left = Math::add($left, Math::add($previous->getOffset()->getLeft(), Math::add($previous->getDimensions()->getWidth(), $previous->getStyle()->getRules('margin-right'))));
        } else {
            $left = Math::add($left, (string)$parent->getStyle()->getOffsetLeft());
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
        $this->getCoordinates()->setX(Math::add($parent->getCoordinates()->getX(), $this->getOffset()->getLeft()));
        if (!$parent instanceof InlineBox) {
            $this->getCoordinates()->setY(Math::add($parent->getCoordinates()->getY(), $this->getOffset()->getTop()));
        } else {
            $this->getCoordinates()->setY($this->getClosestLineBox()->getCoordinates()->getY());
        }
        foreach ($this->getChildren() as $child) {
            $child->measurePosition();
        }
        return $this;
    }
}
