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
                $textContent = $child->getTextContent();
                if ($textContent === '' || $textContent === ' ') {
                    $this->removeChild($child);
                }
            } else {
                $child->removeEmptyLines();
            }
        }
        return $this;
    }

    /**
     * Get boxes by tag name
     * @param string $tagName
     * @return array
     */
    public function getBoxesByTagName(string $tagName)
    {
        $boxes = [];
        $allChildren = [];
        $this->getAllChildren($allChildren);
        foreach ($allChildren as $child) {
            if ($child instanceof ElementBox && $child->getElement() && $child->getElement()->getDOMElement()) {
                $elementTagName = $child->getElement()->getDOMElement()->tagName;
                if ($elementTagName && strtolower($elementTagName) === strtolower($tagName)) {
                    $boxes[] = $child;
                }
            }
        }
        return $boxes;
    }

    /**
     * Fix tables - iterate through cells and insert missing one
     * @return $this
     */
    protected function fixTables()
    {
        $tables = $this->getBoxesByTagName('table');
        foreach ($tables as $tableBox) {
            $rowGroups = $tableBox->getChildren();
            if (!isset($rowGroups[0])) {
                $rowGroup = $tableBox->createRowGroup();
                $row = $rowGroup->createRow();
                $column = $row->createColumn();
                $column->createCell();
            } else {
                $columnsCount = 0;
                foreach ($rowGroups as $rowGroup) {
                    foreach ($rowGroup->getChildren() as $row) {
                        $columns = $row->getChildren();
                        $columnsCount = max($columnsCount, count($columns));
                    }
                    foreach ($rowGroup->getChildren() as $row) {
                        $columns = $row->getChildren();
                        $missing = $columnsCount - count($columns);
                        for ($i = 0; $i < $missing; $i++) {
                            $column = $row->createColumn();
                            $column->createCell();
                        }
                    }
                }
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
        if ($this->getElement()) {
            $domElement = $this->getElement()->getDOMElement();
        } else {
            // tablebox doesn't have element so we can get it from table wrapper (parent box)
            $domElement = $this->getParent()->getElement()->getDOMElement();
        }
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
                        if (isset($inline) && $childDomElement instanceof \DOMText) {
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
        $this->fixTables();
        return $this;
    }

}
