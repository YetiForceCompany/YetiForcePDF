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
     * Create column box
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function createColumnBox()
    {
        $style = (new \YetiForcePDF\Style\Style())
            ->setDocument($this->document)
            ->setContent('')
            ->parseInline();
        $box = (new TableColumnBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        return $box;
    }

    /**
     * Append table cell box element
     * @param \DOMElement $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox $parentBlock
     * @return $this
     */
    public function appendTableCellBox($childDomElement, $element, $style, $parentBlock)
    {
        $colSpan = 1;
        $attributeColSpan = $childDomElement->getAttribute('colspan');
        if ($attributeColSpan) {
            $colSpan = (int)$attributeColSpan;
        }
        $clearStyle = (new \YetiForcePDF\Style\Style())
            ->setDocument($this->document)
            ->parseInline();
        $column = (new TableColumnBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setStyle($clearStyle)
            ->init();
        $column->setColSpan($colSpan);
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


}
