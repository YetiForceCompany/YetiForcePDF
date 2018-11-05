<?php
declare(strict_types=1);
/**
 * TableRowBox class
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

/**
 * Class TableRowBox
 */
class TableRowBox extends BlockBox
{

    /**
     * We shouldn't append block box here
     */
    public function appendBlockBox($childDomElement, $element, $style, $parentBlock)
    {
    }

    /**
     * We shouldn't append table wrapper here
     */
    public function appendTableWrapperBlockBox($childDomElement, $element, $style, $parentBlock)
    {
    }

    /**
     * We shouldn't append inline block box here
     */
    public function appendInlineBlockBox($childDomElement, $element, $style, $parentBlock)
    {
    }

    /**
     * We shouldn't append inline box here
     */
    public function appendInlineBox($childDomElement, $element, $style, $parentBlock)
    {
    }

    /**
     * Append table cell box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox $parentBlock
     * @return $this
     */
    public function appendTableCellBox($childDomElement, $element, $style, $parentBlock)
    {
        $clearStyle = (new \YetiForcePDF\Style\Style())
            ->setDocument($this->document)
            ->parseInline();
        $column = (new TableColumnBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setStyle($clearStyle)
            ->init();
        $this->appendChild($column);
        $column->getStyle()->init();
        $box = (new TableCellBox())
            ->setDocument($this->document)
            ->setParent($column)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        $column->appendChild($box);
        $box->getStyle()->init();
        $box->buildTree($box);
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
                $dimensions->setWidth(Math::sub($parent->getDimensions()->getInnerWidth(), $this->getStyle()->getHorizontalMarginsWidth()));
                $this->applyStyleWidth();
                foreach ($this->getChildren() as $child) {
                    $child->measureWidth();
                }
                return $this;
            }
            foreach ($this->getChildren() as $child) {
                $child->measureWidth();
            }
            $maxWidth = '0';
            foreach ($this->getChildren() as $child) {
                $maxWidth = Math::add($maxWidth, $child->getDimensions()->getOuterWidth());
            }
            $style = $this->getStyle();
            $maxWidth = Math::add($maxWidth, Math::add($style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth()));
            $maxWidth = Math::sub($maxWidth, $style->getHorizontalMarginsWidth());
            $dimensions->setWidth($maxWidth);
            $this->applyStyleWidth();
            return $this;
        }
        $dimensions->setWidth($this->document->getCurrentPage()->getDimensions()->getWidth());
        $this->applyStyleWidth();
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
        $height = '0';
        foreach ($this->getChildren() as $child) {
            $height = Math::max($height, $child->getDimensions()->getOuterHeight());
        }
        $rules = $this->getStyle()->getRules();
        $height = Math::add($height, $rules['border-top-width'], $rules['padding-top']);
        $height = Math::add($height, $rules['border-bottom-width'], $rules['padding-bottom']);
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
                $top = Math::add($previous->getOffset()->getTop(), $previous->getDimensions()->getHeight());
                if ($previous->getStyle()->getRules('display') === 'block') {
                    $marginTop = Math::max($marginTop, $previous->getStyle()->getRules('margin-bottom'));
                } elseif (!$previous instanceof LineBox) {
                    $marginTop = Math::add($marginTop, $previous->getStyle()->getRules('margin-bottom'));
                }
            }
        }
        $top = Math::add($top, $marginTop);
        $left = Math::add($left, $this->getStyle()->getRules('margin-left'));
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
            $x = Math::add($parent->getCoordinates()->getX(), $this->getOffset()->getLeft());
            $y = Math::add($parent->getCoordinates()->getY(), $this->getOffset()->getTop());
        }
        $this->getCoordinates()->setX($x);
        $this->getCoordinates()->setY($y);
        foreach ($this->getChildren() as $child) {
            $child->measurePosition();
        }
        return $this;
    }
}
