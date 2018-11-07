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
     * @return array of LineBoxes and TableRowBoxes
     */
    public function getRows()
    {
        $rows = [];
        foreach ($this->getChildren() as $rowGroup) {
            if ($rowGroup instanceof TableRowGroupBox) {
                foreach ($rowGroup->getChildren() as $row) {
                    $rows[] = $row;
                }
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
     * Get cells
     * @return array
     */
    public function getCells()
    {
        $cells = [];
        foreach ($this->getColumns() as $columnIndex => $row) {
            foreach ($row as $column) {
                $cells[] = $column->getFirstChild();
            }
        }
        return $cells;
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
        foreach ($this->getCells() as $cell) {
            $cell->measureWidth();
        }
        $columnGroups = $this->getColumns();
        $minMax = $this->getMinMaxWidths($columnGroups);
        $maxWidths = $minMax['max'];
        $minWidths = $minMax['min'];
        $maxWidth = '0';
        foreach ($maxWidths as $width) {
            $maxWidth = Math::add($maxWidth, $width);
        }
        $availableSpace = $this->getDimensions()->computeAvailableSpace();
        if (Math::comp($maxWidth, $availableSpace) <= 0) {
            $this->getDimensions()->setWidth($maxWidth);
            foreach ($maxWidths as $columnIndex => $width) {
                foreach ($columnGroups[$columnIndex] as $rowIndex => $column) {
                    $cell = $column->getFirstChild();
                    $column->getDimensions()->setWidth($width);
                    $row = $column->getParent();
                    $row->getDimensions()->setWidth($column->getDimensions()->getOuterWidth());
                    $rowGroup = $row->getParent();
                    $rowGroup->getDimensions()->setWidth($row->getDimensions()->getOuterWidth());
                    $cell->getDimensions()->setWidth($column->getDimensions()->getInnerWidth());
                    $cell->measureWidth();
                }
            }
        } else {
            // use min widths
            $fullMinWidth = '0';
            foreach ($minWidths as $min) {
                $fullMinWidth = Math::add($fullMinWidth, $min);
            }
            $left = Math::sub($maxWidth, $availableSpace);
            $width = '0';
            foreach ($minWidths as $columnIndex => $minWidth) {
                $maxColumnWidth = $maxWidths[$columnIndex];
                $columnWidth = Math::sub($maxColumnWidth, Math::mul($left, Math::div($maxColumnWidth, $maxWidth)));
                foreach ($columnGroups[$columnIndex] as $rowIndex => $column) {
                    $cell = $column->getFirstChild();
                    $column->getDimensions()->setWidth($columnWidth);
                    $row = $column->getParent();
                    $row->getDimensions()->setWidth($column->getDimensions()->getOuterWidth());
                    $rowGroup = $row->getParent();
                    $rowGroup->getDimensions()->setWidth($row->getDimensions()->getOuterWidth());
                    $cell->getDimensions()->setWidth($column->getDimensions()->getInnerWidth());
                    $cell->measureWidth();
                }
                $width = Math::add($width, $columnWidth);
            }
            $style = $this->getStyle();
            $this->getDimensions()->setWidth(Math::add($width, $style->getHorizontalPaddingsWidth(), $style->getVerticalBordersWidth()));
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function measureHeight()
    {
        foreach ($this->getCells() as $cell) {
            $cell->measureHeight();
        }
        $style = $this->getStyle();
        $maxRowHeights = [];
        $rows = $this->getRows();
        foreach ($rows as $rowIndex => $row) {
            foreach ($row->getChildren() as $column) {
                $cell = $column->getFirstChild();
                if (!isset($maxRowHeights[$rowIndex])) {
                    $maxRowHeights[$rowIndex] = '0';
                }
                $columnStyle = $column->getStyle();
                $columnVerticalSize = Math::add($columnStyle->getVerticalMarginsWidth(), $columnStyle->getVerticalPaddingsWidth(), $columnStyle->getVerticalBordersWidth());
                $columnHeight = Math::add($cell->getDimensions()->getOuterHeight(), $columnVerticalSize);
                $maxRowHeights[$rowIndex] = Math::max($maxRowHeights[$rowIndex], $columnHeight);
            }
        }
        $tableHeight = '0';
        foreach ($rows as $rowIndex => $row) {
            $row->getDimensions()->setHeight(Math::add($maxRowHeights[$rowIndex], $style->getVerticalBordersWidth(), $style->getVerticalPaddingsWidth()));
            $row->getParent()->getDimensions()->setHeight($row->getDimensions()->getOuterHeight());
            foreach ($row->getChildren() as $column) {
                $column->getDimensions()->setHeight($row->getDimensions()->getInnerHeight());
                $cell = $column->getFirstChild();
                $height = $column->getDimensions()->getInnerHeight();
                $cellChildrenHeight = '0';
                foreach ($cell->getChildren() as $cellChild) {
                    $cellChildrenHeight = Math::add($cellChildrenHeight, $cellChild->getDimensions()->getOuterHeight());
                }
                $cellStyle = $cell->getStyle();
                $cellVerticalSize = Math::add($cellStyle->getVerticalBordersWidth(), $cellStyle->getVerticalPaddingsWidth());
                $cellChildrenHeight = Math::add($cellChildrenHeight, $cellVerticalSize);
                // add vertical padding if needed
                if (Math::comp($height, $cellChildrenHeight) > 0) {
                    $freeSpace = Math::sub($height, $cellChildrenHeight);
                    $cellStyle = $cell->getStyle();
                    switch ($cellStyle->getRules('vertical-align')) {
                        case 'top':
                            $cellStyle->setRule('padding-bottom', $freeSpace);
                            break;
                        case 'bottom':
                            $cellStyle->setRule('padding-top', $freeSpace);
                            break;
                        case 'baseline':
                        case 'middle':
                        default:
                            $padding = Math::div($freeSpace, '2');
                            $cellStyle->setRule('padding-top', $padding);
                            $cellStyle->setRule('padding-bottom', $padding);
                            break;
                    }
                }
                $cell->getDimensions()->setHeight(Math::max($height, $cellChildrenHeight));
            }
            $tableHeight = Math::add($tableHeight, $row->getParent()->getDimensions()->getOuterHeight());
        }
        $this->getDimensions()->setHeight(Math::add($tableHeight, $style->getVerticalBordersWidth(), $style->getVerticalPaddingsWidth()));
        return $this;
    }
}
