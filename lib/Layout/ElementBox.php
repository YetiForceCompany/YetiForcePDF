<?php
declare(strict_types=1);
/**
 * ElementBox class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Layout\Coordinates\Coordinates;
use \YetiForcePDF\Layout\Coordinates\Offset;
use \YetiForcePDF\Layout\Dimensions\BoxDimensions;
use \YetiForcePDF\Html\Element;
use YetiForcePDF\Style\Style;

/**
 * Class ElementBox
 */
class ElementBox extends Box
{
    /**
     * @var Element
     */
    protected $element;

    /**
     * Get element
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Set element
     * @param Element $element
     * @return $this
     */
    public function setElement(Element $element)
    {
        $this->element = $element;
        $element->setBox($this);
        return $this;
    }

    /**
     * Remove empty lines
     * @return $this
     */
    public function removeEmptyLines()
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof LineBox) {
                if ($child->getTextContent() === '') {
                    $this->removeChild($child);
                }
            } else {
                $child->removeEmptyLines();
            }
        }
        return $this;
    }

    /**
     * Build tree
     * @param $parentBlock
     * @return $this
     */
    public function buildTree($parentBlock = null)
    {
        $domElement = $this->getElement()->getDOMElement();
        if ($domElement->hasChildNodes()) {
            foreach ($domElement->childNodes as $childDomElement) {
                if ($childDomElement instanceof \DOMComment) {
                    continue;
                }
                $styleStr = '';
                if ($childDomElement instanceof \DOMElement && $childDomElement->hasAttribute('style')) {
                    $styleStr = $childDomElement->getAttribute('style');
                }
                $element = (new Element())
                    ->setDocument($this->document)
                    ->setDOMElement($childDomElement)
                    ->init();
                // for now only basic style is used - from current element only (with defaults)
                $style = (new \YetiForcePDF\Style\Style())
                    ->setDocument($this->document)
                    ->setElement($element)
                    ->setContent($styleStr)
                    ->parseInline();
                $display = $style->getRules('display');
                switch ($display) {
                    case 'block':
                        $this->appendBlockBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'table':
                        $tableWrapper = $this->appendTableWrapperBlockBox($childDomElement, $element, $style, $parentBlock);
                        $tableWrapper->appendTableBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'table-row':
                        $rowGroup = $this->appendTableRowGroupBox($childDomElement, $element, $style, $parentBlock);
                        $rowGroup->appendTableRowBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'table-cell':
                        $this->appendTableCellBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'inline':
                        $inline = $this->appendInlineBox($childDomElement, $element, $style, $parentBlock);
                        if ($childDomElement instanceof \DOMText) {
                            $inline->setAnonymous(true)->appendText($childDomElement, null, null, $parentBlock);
                        }
                        break;
                    case 'inline-block':
                        $this->appendInlineBlockBox($childDomElement, $element, $style, $parentBlock);
                        break;
                }
            }
        }
        $this->removeEmptyLines();
        return $this;
    }
}
