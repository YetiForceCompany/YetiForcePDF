<?php
declare(strict_types=1);
/**
 * InlineBlockBox class
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
        $maxWidth = '0';
        foreach ($this->getChildren() as $child) {
            $child->measureWidth();
            $maxWidth = bccomp($maxWidth, $child->getDimensions()->getOuterWidth(), 4) > 0 ? $maxWidth : $child->getDimensions()->getOuterWidth();
        }
        $style = $this->getStyle();
        $maxWidth = bcadd($maxWidth, bcadd($style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth(), 4), 4);
        $this->getDimensions()->setWidth($maxWidth);
        $this->applyStyleWidth();
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
            $height = bcadd($height, $child->getDimensions()->getOuterHeight(), 4);
        }
        $style = $this->getStyle();
        $height = bcadd($height, bcadd($style->getVerticalBordersWidth(), $style->getVerticalPaddingsWidth(), 4), 4);
        $this->getDimensions()->setHeight($height);
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
            $left = bcadd($left, bcadd($previous->getOffset()->getLeft(), bcadd($previous->getDimensions()->getWidth(), $previous->getStyle()->getRules('margin-right'), 4), 4), 4);
        } else {
            $left = bcadd($left, $parent->getStyle()->getOffsetLeft(), 4);
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
        $this->getCoordinates()->setX(bcadd($parent->getCoordinates()->getX(), $this->getOffset()->getLeft(), 4));
        if (!$parent instanceof InlineBox) {
            $this->getCoordinates()->setY(bcadd($parent->getCoordinates()->getY(), $this->getOffset()->getTop()), 4);
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
