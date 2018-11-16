<?php
declare(strict_types=1);
/**
 * TableWrapperBlockBox class
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
 * Class TableWrapperBlockBox
 */
class TableWrapperBlockBox extends BlockBox
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
     * Append table box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox $parentBlock
     * @return $this
     */
    public function appendTableBox($childDomElement, $element, $style, $parentBlock)
    {
        $cleanStyle = (new \YetiForcePDF\Style\Style())->setDocument($this->document);
        $box = (new TableBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($cleanStyle)
            ->init();
        $cleanStyle->setRule('display', 'block');
        $this->appendChild($box);
        $box->getStyle()->init();
        $box->buildTree($box);
        return $box;
    }

    /**
     * {@inheritdoc}
     */
    public function measureWidth()
    {
        $maxWidth = '0';
        $this->applyStyleWidth();
        $style = $this->getStyle();
        foreach ($this->getChildren() as $child) {
            $child->measureWidth();
            $width = $child->getDimensions()->getWidth();
            $spacing = Math::add($style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth());
            $maxWidth = Math::max($maxWidth, Math::add($width, $spacing));
        }
        /*$availableSpace = $this->getDimensions()->computeAvailableSpace();
        if (Math::comp($maxWidth, $availableSpace) === 1) {
            $maxWidth = $availableSpace;
        } else {
            $style = $this->getStyle();
            $maxWidth = Math::add($maxWidth, $style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth());
        }*/
        $this->getDimensions()->setWidth($maxWidth);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function measureHeight()
    {
        $maxHeight = '0';
        foreach ($this->getChildren() as $child) {
            $child->measureHeight();
        }
        foreach ($this->getChildren() as $child) {
            $child->measureHeight();
            $maxHeight = Math::max($maxHeight, $child->getDimensions()->getOuterHeight());
        }
        $style = $this->getStyle();
        $maxHeight = Math::add($maxHeight, $style->getVerticalBordersWidth(), $style->getVerticalPaddingsWidth());
        $this->getDimensions()->setHeight($maxHeight);
        $this->applyStyleWidth();
        return $this;
    }
}
