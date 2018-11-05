<?php
declare(strict_types=1);
/**
 * TableBox class
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
 * Class TableBox
 */
class TableBox extends BlockBox
{
    /**
     * Append table row group box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox $parentBlock
     * @return $this
     */
    public function appendTableRowGroupBox($childDomElement, $element, $style, $parentBlock)
    {
        $box = (new TableRowGroupBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        // we don't want to build tree from here - we will do it in TableRowBox
        return $box;
    }

    /**
     * Get all rows from all row groups
     */
    public function getRows()
    {
        $rows = [];
        foreach ($this->getChildren() as $rowGroup) {
            foreach ($rowGroup->getChildren() as $row) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * Get columns - get table cells segregated by columns
     * @return array
     */
    public function getColumns()
    {
        $columns = [];
        foreach ($this->getRows() as $row) {
            $columnIndex = 0;
            foreach ($row->getChildren() as $column) {
                if ($column instanceof TableColumnBox) {
                    $columns[$columnIndex][] = $column;
                    $columnIndex++;
                }
            }
        }
        return $columns;
    }

    /**
     * Add missing cells - rows should have equal numbers of column so if not we will add anonymous cell to it
     * @return $this
     */
    public function addMissingCells()
    {

        return $this;
    }

    /**
     * Get minimal and maximal column widths
     * @param array $columns
     * @return array
     */
    public function getMinMaxWidths($columns)
    {
        $maxWidths = [];
        $minWidths = [];
        foreach ($columns as $columnIndex => $row) {
            foreach ($row as $column) {
                if (!isset($maxWidths[$columnIndex])) {
                    $maxWidths[$columnIndex] = '0';
                }
                if (!isset($minWidths[$columnIndex])) {
                    $minWidths[$columnIndex] = '0';
                }
                $maxWidths[$columnIndex] = Math::max($maxWidths[$columnIndex], $column->getDimensions()->getOuterWidth());
                $minWidths[$columnIndex] = Math::max($minWidths[$columnIndex], $column->getDimensions()->getMinWidth());
            }
        }
        return ['min' => $minWidths, 'max' => $maxWidths];
    }

    /**
     * {@inheritdoc}
     */
    public function measureWidth()
    {
        parent::measureWidth();
        $columns = $this->getColumns();
        $minMax = $this->getMinMaxWidths($columns);
        $maxWidths = $minMax['max'];
        $minWidths = $minMax['min'];
        $maxWidth = '0';
        foreach ($maxWidths as $width) {
            $maxWidth = Math::add($maxWidth, $width);
        }
        $availableSpace = $this->getDimensions()->computeAvailableSpace();
        if ($maxWidth <= $availableSpace) {
            $this->getDimensions()->setWidth($maxWidth);
            foreach ($maxWidths as $columnIndex => $width) {
                foreach ($columns[$columnIndex] as $row) {
                    $cell = $row->getFirstChild();
                    $row->getDimensions()->setWidth($width);
                    $cell->getDimensions()->setWidth($row->getDimensions()->getInnerWidth());
                }
            }
        } else {
            // use min widths
            $fullMinWidth = '0';
            foreach ($minWidths as $min) {
                $fullMinWidth = Math::add($fullMinWidth, $min);
            }
            $left = Math::sub($availableSpace, $fullMinWidth);
            $width = '0';
            foreach ($minWidths as $columnIndex => $minWidth) {
                $maxColumnWidth = $maxWidths[$columnIndex];
                $columnWidth = Math::add($minWidth, Math::mul($left, Math::div($maxColumnWidth, $maxWidth)));
                foreach ($columns[$columnIndex] as $row) {
                    $cell = $row->getFirstChild();
                    $row->getDimensions()->setWidth($columnWidth);
                    $cell->getDimensions()->setWidth($row->getDimensions()->getInnerWidth());
                }
                $width = Math::add($width, $columnWidth);
            }
            $this->getDimensions()->setWidth($width);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function measureHeight()
    {
        parent::measureHeight();
        $maxHeights = [];
        $rows = $this->getRows();
        foreach ($rows as $rowIndex => $row) {
            foreach ($row->getChildren() as $column) {
                if (!isset($maxHeights[$rowIndex])) {
                    $maxHeights[$rowIndex] = '0';
                }
                $maxHeights[$rowIndex] = Math::max($maxHeights[$rowIndex], $column->getDimensions()->getOuterHeight());
            }
        }
        foreach ($rows as $rowIndex => $row) {
            $row->getDimensions()->setHeight($maxHeights[$rowIndex]);
            foreach ($row->getChildren() as $column) {
                $column->getDimensions()->setHeight($row->getDimensions()->getInnerHeight());
                $cell = $column->getFirstChild();
                $height = $column->getDimensions()->getInnerHeight();
                $cellChildrenHeight = '0';
                foreach ($cell->getChildren() as $cellChild) {
                    $cellChildrenHeight = Math::add($cellChildrenHeight, $cellChild->getDimensions()->getOuterHeight());
                }
                // add vertical padding if needed
                if ($cellChildrenHeight < $height) {
                    $freeSpace = Math::sub($height, $cellChildrenHeight);
                    $style = $cell->getStyle();
                    switch ($style->getRules('vertical-align')) {
                        case 'top':
                            $style->setRule('padding-bottom', $freeSpace);
                            break;
                        case 'bottom':
                            $style->setRule('padding-top', $freeSpace);
                            break;
                        case 'baseline':
                        case 'middle':
                        default:
                            $padding = Math::div($freeSpace, '2');
                            $style->setRule('padding-top', $padding);
                            $style->setRule('padding-bottom', $padding);
                            break;
                    }
                }
                $cell->getDimensions()->setHeight($height);
            }
        }
        return $this;
    }
}
