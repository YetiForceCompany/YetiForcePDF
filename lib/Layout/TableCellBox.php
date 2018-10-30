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
            $maxWidth = bccomp((string)$maxWidth, (string)$child->getDimensions()->getOuterWidth(),4) >0 ? $maxWidth : $child->getDimensions()->getOuterWidth();
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
            $height = bcadd($height, (string)$child->getDimensions()->getOuterHeight(), 4);
        }
        $dimensions = $this->getDimensions();
        $style = $this->getStyle();
        $height = (float)bcadd($height, bcadd((string)$style->getVerticalBordersWidth(), (string)$style->getVerticalPaddingsWidth(), 4), 4);
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
            $left = bcadd($left, bcadd((string)$previous->getOffset()->getLeft(), bcadd((string)$previous->getDimensions()->getWidth(), (string)$previous->getStyle()->getRules('margin-right'), 4), 4), 4);
        } else {
            $left = bcadd($left, (string)$parent->getStyle()->getOffsetLeft(), 4);
        }
        $this->getOffset()->setLeft((floaT)$left);
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
        $this->getCoordinates()->setX((float)bcadd((string)$parent->getCoordinates()->getX(), (string)$this->getOffset()->getLeft(), 4));
        if (!$parent instanceof InlineBox) {
            $this->getCoordinates()->setY((float)bcadd((string)$parent->getCoordinates()->getY(), (string)$this->getOffset()->getTop(), 4));
        } else {
            $this->getCoordinates()->setY($this->getClosestLineBox()->getCoordinates()->getY());
        }
        foreach ($this->getChildren() as $child) {
            $child->measurePosition();
        }
        return $this;
    }
}
