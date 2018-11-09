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

    protected $minWidths = [];
    protected $preferredWidths = [];
    protected $contentWidths = [];
    protected $minWidth = '0';
    protected $preferredWidth = '0';
    protected $contentWidth = '0';
    protected $gridMin = '0';
    protected $gridMax = '0';
    protected $percentages = [];
    protected $intrinsicSum = '0';
    protected $cellSpacingWidth = '0';
    protected $borderWidth = '0';
    protected $usedWidth = '0';
    protected $assignableWidth = '0';
    protected $percentColumns = [];
    protected $pixelColumns = [];
    protected $autoColumns = [];

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
     * @param array $columnGroups
     * @return array
     */
    public function setUpWidths($columnGroups)
    {
        foreach ($columnGroups as $columnIndex => $columns) {
            foreach ($columns as $column) {
                if (!isset($this->preferredWidths[$columnIndex])) {
                    $this->preferredWidths[$columnIndex] = '0';
                }
                if (!isset($this->minWidths[$columnIndex])) {
                    $this->minWidths[$columnIndex] = '0';
                }
                if (!isset($this->contentWidths[$columnIndex])) {
                    $this->contentWidths[$columnIndex] = '0';
                }
                if (!isset($this->percentages[$columnIndex])) {
                    $this->percentages[$columnIndex] = '0';
                }
                $columnWidth = $column->getDimensions()->getOuterWidth();
                $styleWidth = $column->getStyle()->getRules('width');
                if ($styleWidth !== 'auto' && strpos($styleWidth, '%') === -1) {
                    $preferred = $styleWidth;
                } elseif (strpos($styleWidth, '%') > 0) {
                    $preferred = Math::max($this->preferredWidths[$columnIndex], $columnWidth);
                    $this->percentages[$columnIndex] = Math::max($this->percentages[$columnIndex], trim($styleWidth, '%'));
                } else {
                    $preferred = Math::max($this->preferredWidths[$columnIndex], $columnWidth);
                }
                $this->contentWidths[$columnIndex] = Math::max($this->contentWidths[$columnIndex], $columnWidth);
                $this->minWidths[$columnIndex] = Math::max($this->minWidths[$columnIndex], $column->getDimensions()->getMinWidth());
                $this->preferredWidths[$columnIndex] = $preferred;
                $this->borderWidth = Math::add($this->borderWidth, $column->getStyle()->getVerticalBordersWidth());
                if ($this->getStyle()->getRules('border-collapse') !== 'collapse') {
                    $columnStyle = $column->getStyle();
                    $this->cellSpacingWidth = Math::add($this->cellSpacingWidth, $columnStyle->getHorizontalPaddingsWidth());
                }
            }
            $this->minWidth = Math::add($this->minWidth, $this->minWidths[$columnIndex]);
            $this->contentWidth = Math::add($this->contentWidth, $this->contentWidths[$columnIndex]);
            $this->preferredWidth = Math::add($this->preferredWidth, $this->preferredWidths[$columnIndex]);
        }
        $this->gridMin = Math::add($this->minWidth, $this->cellSpacingWidth, $this->borderWidth);
        $this->gridMax = Math::add($this->preferredWidth, $this->cellSpacingWidth, $this->borderWidth);
        $this->usedWidth = Math::max(Math::min($this->gridMax, $this->getParent()->getParent()->getDimensions()->getWidth()), $this->gridMin);
        $this->assignableWidth = Math::sub($this->usedWidth, $this->cellSpacingWidth);
        $this->getDimensions()->setWidth($this->usedWidth);
        $parentStyle = $this->getParent()->getStyle();
        $this->getParent()->getDimensions()->setWidth(Math::add($this->usedWidth, $parentStyle->getHorizontalPaddingsWidth(), $parentStyle->getHorizontalBordersWidth()));
    }

    /**
     * Set up sizing types for columns
     * @param array $columnGroups
     * @return $this
     */
    protected function setUpSizingTypes(array $columnGroups)
    {
        foreach ($columnGroups as $columnIndex => $columns) {
            $firstColumn = $columns[0];
            $columnStyleWidth = $firstColumn->getStyle()->getRules('width');
            if (strpos($columnStyleWidth, '%') > 0) {
                foreach ($columns as $rowIndex => $column) {
                    $this->percentColumns[$columnIndex][$rowIndex] = $column;
                }
            } elseif ($columnStyleWidth !== 'auto') {
                foreach ($columns as $rowIndex => $column) {
                    $this->pixelColumns[$columnIndex][$rowIndex] = $column;
                }
            } else {
                foreach ($columns as $rowIndex => $column) {
                    $this->autoColumns[$columnIndex][$rowIndex] = $column;
                }
            }
        }
        return $this;
    }

    /**
     * Set rows width
     * @param string $width
     * @return $this
     */
    protected function setRowsWidth(string $width)
    {
        foreach ($this->getRows() as $row) {
            $row->getDimensions()->setWidth($width);
            $row->getParent()->getDimensions()->setWidth($width);
        }
        return $this;
    }

    /**
     * Minimal content percentage guess
     * @param array $columnGroups
     * @param string $availableSpace
     * @return $this
     */
    protected function minContentPercentageGuess(string $availableSpace)
    {
        $rowWidth = '0';
        $columnsWidths = [];
        $currentIntrinsicPercentage = '0';
        foreach ($this->percentColumns as $columnIndex => $columns) {
            if (!isset($columnsWidths[$columnIndex])) {
                $columnsWidths[$columnIndex] = '0';
            }
            $intrinsicPercentageWidth = Math::min($this->percentages[$columnIndex], Math::sub('100', $currentIntrinsicPercentage));
            $currentIntrinsicPercentage = Math::add($currentIntrinsicPercentage, $intrinsicPercentageWidth);
            $columnWidth = Math::mul(Math::div($intrinsicPercentageWidth, '100'), $this->assignableWidth);
            foreach ($columns as $rowIndex => $column) {
                $column->getDimensions()->setWidth($columnWidth);
                $column->getFirstChild()->getDimensions()->setWidth($columnWidth);
                $columnStyle = $column->getStyle();
                $spacing = Math::add($columnStyle->getHorizontalPaddingsWidth(), $columnStyle->getHorizontalBordersWidth());
                $rowWidth = Math::add($rowWidth, $columnWidth, $spacing);
            }
        }
        foreach ($this->pixelColumns as $columnIndex => $columns) {
            foreach ($columns as $rowIndex => $column) {
                $columnStyle = $column->getStyle();
                $spacing = Math::add($columnStyle->getHorizontalPaddingsWidth(), $columnStyle->getHorizontalBordersWidth());
                $columnWidth = Math::add($this->minWidths[$columnIndex], $spacing);
                $column->getDimensions()->setWidth($columnWidth);
                $column->getFirstChild()->getDimensions()->setWidth($this->minWidths[$columnIndex]);
                $rowWidth = Math::add($rowWidth, $columnWidth);
            }
        }
        foreach ($this->autoColumns as $columnIndex => $columns) {
            foreach ($columns as $rowIndex => $column) {
                $columnStyle = $column->getStyle();
                $spacing = Math::add($columnStyle->getHorizontalPaddingsWidth(), $columnStyle->getHorizontalBordersWidth());
                $columnWidth = Math::add($this->minWidths[$columnIndex], $spacing);
                $column->getDimensions()->setWidth($columnWidth);
                $column->getFirstChild()->getDimensions()->setWidth($this->minWidths[$columnIndex]);
                $rowWidth = Math::add($rowWidth, $columnWidth);
            }
        }
        $this->setRowsWidth($rowWidth);
        return $this;
    }

    /**
     * Minimal content specified guess
     * @param array $rows
     * @return $this
     */
    protected function minContentSpecifiedGuess(array $rows)
    {
        $columnsWidths = [];
        foreach ($rows as $row) {
            foreach ($row->getChildren() as $columnIndex => $column) {
                if (!isset($columnsWidths[$columnIndex])) {
                    $columnsWidths[$columnIndex] = '0';
                }
                $columnStyle = $column->getStyle();
                $columnStyleWidth = $columnStyle->getRules('width');
                if (strpos($columnStyleWidth, '%') > 0) {
                    $percentWidth = Math::percent($columnStyleWidth, $this->getDimensions()->getWidth());
                    $columnsWidths[$columnIndex] = Math::max($columnsWidths[$columnIndex], $percentWidth, $column->getDimensions()->getWidth());
                } elseif ($columnStyle->getRules('width') !== 'auto') {
                    $columnsWidths[$columnIndex] = Math::max($columnStyle->getRules('width'), $column->getDimensions()->getWidth());
                } else {
                    $columnsWidths[$columnIndex] = Math::max($columnsWidths[$columnIndex], $this->minWidths[$columnIndex]);
                }
            }
        }
        foreach ($rows as $row) {
            $rowWidth = '0';
            foreach ($row->getChildren() as $columnIndex => $column) {
                $cell = $column->getFirstChild();
                $column->getDimensions()->setWidth($columnsWidths[$columnIndex]);
                $cell->getDimensions()->setWidth($column->getDimensions()->getInnerWidth());
                $rowWidth = Math::add($rowWidth, $column->getDimensions()->getWidth());
            }
            $row->getDimensions()->setWidth($rowWidth);
            $rowGroup = $row->getParent();
            $rowGroup->getDimensions()->setWidth($row->getDimensions()->getWidth());
        }
        return $this;
    }

    /**
     * Maximal content guess
     * @param array $rows
     * @return $this
     */
    protected function maxContentGuess(array $rows)
    {

        $columnsWidths = [];
        foreach ($rows as $rowIndex => $row) {
            foreach ($row->getChildren() as $columnIndex => $column) {
                if (!isset($columnsWidths[$columnIndex])) {
                    $columnsWidths[$columnIndex] = '0';
                }
                $columnStyle = $column->getStyle();
                if (strpos($columnStyle->getRules('width'), '%') > 0) {
                    $percentWidth = Math::percent($columnStyle->getRules('width'), $this->getDimensions()->getWidth());
                    $columnsWidths[$columnIndex] = Math::max($columnsWidths[$columnIndex], $percentWidth, $column->getDimensions()->getWidth());
                } elseif ($columnStyle->getRules('width') === 'auto') {
                    $columnsWidths[$columnIndex] = $this->preferredWidths[$columnIndex];
                }
            }
        }
        foreach ($rows as $row) {
            $rowWidth = '0';
            foreach ($row->getChildren() as $columnIndex => $column) {
                $cell = $column->getFirstChild();
                $column->getDimensions()->setWidth($columnsWidths[$columnIndex]);
                $cell->getDimensions()->setWidth($column->getDimensions()->getInnerWidth());
                $rowWidth = Math::add($rowWidth, $column->getDimensions()->getWidth());
            }
            $row->getDimensions()->setWidth($rowWidth);
            $rowGroup = $row->getParent();
            $rowGroup->getDimensions()->setWidth($row->getDimensions()->getWidth());
        }
        return $this;
    }

    /**
     * Redistribute space that is available or exceed assignable width
     * @param string $availableSpace
     * @param array $rows
     * @return $this
     */
    protected function redistributeSpace(string $availableSpace, array $rows)
    {
        $tableWidth = $this->getDimensions()->getInnerWidth();
        if (Math::comp($availableSpace, $tableWidth) > 0) {
            $steps = [[], [], []];
            foreach ($rows as $row) {
                foreach ($row->getChildren() as $columnIndex => $column) {
                    $columnStyleWidth = $column->getStyle()->getRules('width');
                    if ($this->percentages[$columnIndex] === '0') {
                        // non constrained first
                        if ($columnStyleWidth === 'auto') {
                            if (Math::comp($this->contentWidths[$columnIndex], '0') > 0) {
                                $steps[0][$columnIndex] = $column;//proportional auto
                            } else {
                                $steps[1][$columnIndex] = $column;//equal auto
                            }
                        } else {
                            // constrained px
                            $steps[2][$columnIndex] = $column; // proportional constrained
                        }
                    } else {
                        $steps[3][$columnIndex] = $column; // percent %
                    }
                }
            }
            $freeSpace = Math::sub($availableSpace, $tableWidth);
            if ($freeSpace === '0') {
                return $this;
            }
            if (count($steps[0])) {
                $fullMaxWidths = '0';
                foreach ($steps[0] as $columnIndex => $column) {
                    $fullMaxWidths = Math::add($fullMaxWidths, $this->contentWidths[$columnIndex]);
                }
                foreach ($steps[0] as $columnIndex => $column) { //proportional auto
                    $proportion = Math::div($this->contentWidths[$columnIndex], $fullMaxWidths);
                    $add = Math::mul($freeSpace, $proportion);
                    $columnDimensions = $column->getDimensions();
                    $currentWidth = $columnDimensions->getWidth();
                    $columnDimensions->setWidth(Math::add($currentWidth, $add));
                    $column->getFirstChild()->getDimensions()->setWidth($columnDimensions->getInnerWidth());
                }
            } elseif (count($steps[1])) {
                foreach ($steps[1] as $column) { //equal auto
                    $add = Math::div($freeSpace, (string)count($steps[1]));
                    $columnDimensions = $column->getDimensions();
                    $currentWidth = $columnDimensions->getWidth();
                    $columnDimensions->setWidth(Math::add($currentWidth, $add));
                    $column->getFirstChild()->getDimensions()->setWidth($columnDimensions->getInnerWidth());
                }
            } elseif (count($steps[2])) {
                $fullMaxWidths = '0';
                foreach ($steps[2] as $columnIndex => $column) {
                    $fullMaxWidths = Math::add($fullMaxWidths, $this->contentWidths[$columnIndex]);
                }
                foreach ($steps[2] as $column) { //proportional px
                    $proportion = Math::div($this->contentWidths[$columnIndex], $fullMaxWidths);
                    $add = Math::mul($freeSpace, $proportion);
                    $columnDimensions = $column->getDimensions();
                    $currentWidth = $columnDimensions->getWidth();
                    $columnDimensions->setWidth(Math::add($currentWidth, $add));
                    $column->getFirstChild()->getDimensions()->setWidth($columnDimensions->getInnerWidth());
                }
            } else {
                foreach ($steps[3] as $column) { // percent
                    $percent = trim($column->getStyle()->getRules('width'), '%');
                    $add = Math::percent($percent, $freeSpace);
                    $columnDimensions = $column->getDimensions();
                    $currentWidth = $columnDimensions->getWidth();
                    $columnDimensions->setWidth(Math::add($currentWidth, $add));
                    $column->getFirstChild()->getDimensions()->setWidth($columnDimensions->getInnerWidth());
                }
            }
        }
        foreach ($rows as $row) {
            $rowWidth = '0';
            foreach ($row->getChildren() as $column) {
                $rowWidth = Math::add($rowWidth, $column->getDimensions()->getOuterWidth());
            }
            $row->getDimensions()->setWidth($rowWidth);
            $rowGroup = $row->getParent();
            $rowGroup->getDimensions()->setWidth($row->getDimensions()->getOuterWidth());
        }
        $style = $this->getStyle();
        $width = $rowGroup->getDimensions()->getOuterWidth();
        $this->getDimensions()->setWidth(Math::add($width, $style->getHorizontalPaddingsWidth(), $style->getVerticalBordersWidth()));
        return $this;
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
        $this->setUpWidths($columnGroups);
        $this->setUpSizingTypes($columnGroups);
        $availableSpace = $this->getDimensions()->computeAvailableSpace();
        $this->minContentPercentageGuess($availableSpace);
        /*$rows = $this->getRows();
        $this->minContentSpecifiedGuess($rows);
        $this->maxContentGuess($rows);
        $this->redistributeSpace($availableSpace, $rows);*/
        foreach ($this->getCells() as $cell) {
            $cell->measureWidth();
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
