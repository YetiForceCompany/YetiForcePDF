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
    protected $beforeWidths = [];

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

    protected function getPercentWidth()
    {

    }

    /**
     * Get minimal and maximal column widths
     * @param array $columnGroups
     * @param string $availableSpace
     * @return array
     */
    public function setUpWidths(array $columnGroups, string $availableSpace)
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
                $cell = $column->getFirstChild();
                $cellStyle = $cell->getStyle();
                $columnStyle = $column->getStyle();
                $columnOuterWidth = $column->getDimensions()->getOuterWidth();
                $columnSpacing = Math::add($columnStyle->getHorizontalBordersWidth(), $columnStyle->getHorizontalPaddingsWidth());
                $columnInnerWidth = Math::sub($columnOuterWidth, $columnSpacing);
                $styleWidth = $columnStyle->getRules('width');
                $this->contentWidths[$columnIndex] = Math::max($this->contentWidths[$columnIndex], $columnInnerWidth);
                $minColumnWidth = $cell->getDimensions()->getMinWidth();
                $this->minWidths[$columnIndex] = Math::max($this->minWidths[$columnIndex], $minColumnWidth);
                if ($styleWidth !== 'auto' && strpos($styleWidth, '%') === false) {
                    $preferred = Math::max($styleWidth, $minColumnWidth);
                } elseif (strpos($styleWidth, '%') > 0) {
                    $preferred = Math::max($this->preferredWidths[$columnIndex], $columnInnerWidth);
                    $this->percentages[$columnIndex] = Math::max($this->percentages[$columnIndex], trim($styleWidth, '%'));
                } else {
                    $preferred = Math::max($this->preferredWidths[$columnIndex], $columnInnerWidth);
                }
                $this->preferredWidths[$columnIndex] = $preferred;
            }
            $this->borderWidth = Math::add($this->borderWidth, $cellStyle->getHorizontalBordersWidth());
            $this->minWidth = Math::add($this->minWidth, $this->minWidths[$columnIndex]);
            $this->contentWidth = Math::add($this->contentWidth, $this->contentWidths[$columnIndex]);
            $this->preferredWidth = Math::add($this->preferredWidth, $this->preferredWidths[$columnIndex]);
        }
        if ($this->getParent()->getStyle()->getRules('border-collapse') !== 'collapse') {
            $spacing = $this->getStyle()->getRules('border-spacing');
            $this->cellSpacingWidth = Math::mul((string)(count($columnGroups) + 1), $spacing);
        }
        $parentStyle = $this->getParent()->getStyle();
        $availableSpace = Math::sub($availableSpace, $parentStyle->getHorizontalBordersWidth(), $parentStyle->getHorizontalPaddingsWidth());
        $this->gridMin = Math::add($this->minWidth, $this->cellSpacingWidth);
        $this->gridMax = Math::add($this->preferredWidth, $this->borderWidth, $this->cellSpacingWidth);
        $this->usedWidth = Math::max(Math::min($this->gridMax, $availableSpace), $this->gridMin);
        $this->assignableWidth = Math::sub($this->usedWidth, $this->cellSpacingWidth);
        $this->setRowsWidth($this->preferredWidth);
        $this->getDimensions()->setWidth($this->usedWidth);
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
        $style = $this->getParent()->getStyle();
        $spacing = Math::add($style->getHorizontalPaddingsWidth(), $style->getHorizontalBordersWidth());
        $this->getDimensions()->setWidth(Math::add($width, $spacing));
        return $this;
    }

    /**
     * Minimal content guess
     * @param array $rows
     * @return $this
     */
    protected function minContentGuess(array $rows)
    {
        foreach ($rows as $row) {
            foreach ($row->getChildren() as $columnIndex => $column) {
                $columnDimensions = $column->getDimensions();
                $columnStyle = $column->getStyle();
                $columnSpacing = Math::add($columnStyle->getHorizontalBordersWidth(), $columnStyle->getHorizontalPaddingsWidth());
                $columnDimensions->setWidth(Math::add($this->minWidths[$columnIndex], $columnSpacing));
                $column->getFirstChild()->getDimensions()->setWidth($this->minWidths[$columnIndex]);
            }
        }
        $rowWidth = '0';
        foreach ($rows[0]->getChildren() as $column) {
            $rowWidth = Math::add($rowWidth, $column->getDimensions()->getWidth());
        }
        $this->setRowsWidth($rowWidth);
        return $this;
    }

    /**
     * Apply percentage dimensions
     * @param string $leftWidth
     * @return $this
     */
    protected function applyPercentage(string $leftWidth)
    {
        $totalPercentageSpecified = '0';
        foreach ($this->percentColumns as $columnIndex => $columns) {
            $totalPercentageSpecified = Math::add($totalPercentageSpecified, $this->percentages[$columnIndex]);
        }
        $leftPercentage = Math::sub('100', $totalPercentageSpecified);
        $onePercentWidth = Math::div($leftWidth, $leftPercentage);
        foreach ($this->percentColumns as $columnIndex => $columns) {
            $columnWidth = Math::mul($onePercentWidth, $this->percentages[$columnIndex]);
            foreach ($columns as $rowIndex => $column) {
                $this->beforeWidths[$columnIndex] = $column->getDimensions()->getWidth();
                $columnStyle = $column->getStyle();
                $columnSpacing = Math::add($columnStyle->getHorizontalBordersWidth(), $columnStyle->getHorizontalPaddingsWidth());
                $column->getDimensions()->setWidth(Math::add($columnWidth, $columnSpacing));
                $column->getFirstChild()->getDimensions()->setWidth($columnWidth);
            }
        }
        return $this;
    }

    /**
     * Minimal content percentage guess
     * @param array $rows
     * @return $this
     */
    protected function minContentPercentageGuess(array $rows)
    {
        $rowWidth = '0';
        $leftWidth = '0';
        $this->beforeWidths = [];
        foreach ($this->pixelColumns as $columnIndex => $columns) {
            foreach ($columns as $rowIndex => $column) {
                $this->beforeWidths[$columnIndex] = $column->getDimensions()->getWidth();
                $columnStyle = $column->getStyle();
                $columnSpacing = Math::add($columnStyle->getHorizontalBordersWidth(), $columnStyle->getHorizontalPaddingsWidth());
                $column->getDimensions()->setWidth(Math::add($this->minWidths[$columnIndex], $columnSpacing));
                $column->getFirstChild()->getDimensions()->setWidth($this->minWidths[$columnIndex]);
            }
            $leftWidth = Math::add($leftWidth, $column->getDimensions()->getWidth());
        }
        foreach ($this->autoColumns as $columnIndex => $columns) {
            foreach ($columns as $rowIndex => $column) {
                $this->beforeWidths[$columnIndex] = $column->getDimensions()->getWidth();
                $columnStyle = $column->getStyle();
                $columnSpacing = Math::add($columnStyle->getHorizontalBordersWidth(), $columnStyle->getHorizontalPaddingsWidth());
                $column->getDimensions()->setWidth(Math::add($this->minWidths[$columnIndex], $columnSpacing));
                $column->getFirstChild()->getDimensions()->setWidth($this->minWidths[$columnIndex]);
            }
            $leftWidth = Math::add($leftWidth, $column->getDimensions()->getWidth());
        }
        $this->applyPercentage($leftWidth);
        foreach ($rows[0]->getChildren() as $column) {
            $rowWidth = Math::add($rowWidth, $column->getDimensions()->getWidth());
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
        $leftWidth = '0';
        $this->beforeWidths = [];
        foreach ($this->pixelColumns as $columnIndex => $columns) {
            foreach ($columns as $column) {
                $this->beforeWidths[$columnIndex] = $column->getDimensions()->getWidth();
                $columnStyle = $column->getStyle();
                $columnSpacing = Math::add($columnStyle->getHorizontalBordersWidth(), $columnStyle->getHorizontalPaddingsWidth());
                $column->getDimensions()->setWidth(Math::add($this->preferredWidths[$columnIndex], $columnSpacing));
                $column->getFirstChild()->getDimensions()->setWidth($this->preferredWidths[$columnIndex]);
            }
            $leftWidth = Math::add($leftWidth, $column->getDimensions()->getWidth());
        }
        foreach ($this->autoColumns as $columnIndex => $columns) {
            $leftWidth = Math::add($leftWidth, $columns[0]->getDimensions()->getWidth());
        }
        $this->applyPercentage($leftWidth);
        $rowWidth = '0';
        foreach ($rows[0]->getChildren() as $column) {
            $rowWidth = Math::add($rowWidth, $column->getDimensions()->getWidth());
        }
        $this->setRowsWidth($rowWidth);
        return $this;
    }

    /**
     * Maximal content guess
     * @param array $rows
     * @return $this
     */
    protected function maxContentGuess(array $rows)
    {
        $leftWidth = '0';
        $this->beforeWidths = [];
        foreach ($this->autoColumns as $columnIndex => $columns) {
            foreach ($columns as $column) {
                $this->beforeWidths[$columnIndex] = $column->getDimensions()->getWidth();
                $columnStyle = $column->getStyle();
                $columnSpacing = Math::add($columnStyle->getHorizontalBordersWidth(), $columnStyle->getHorizontalPaddingsWidth());
                $column->getDimensions()->setWidth(Math::add($this->contentWidths[$columnIndex], $columnSpacing));
                $column->getFirstChild()->getDimensions()->setWidth($this->contentWidths[$columnIndex]);
            }
            $leftWidth = Math::add($leftWidth, $column->getDimensions()->getWidth());
        }
        foreach ($this->pixelColumns as $columnIndex => $columns) {
            $leftWidth = Math::add($leftWidth, $columns[0]->getDimensions()->getWidth());
        }
        $this->applyPercentage($leftWidth);
        $largestPercentRatio = '0';
        foreach ($this->percentColumns as $columnIndex => $columns) {
            $columnDimensions = $columns[0]->getDimensions();
            $ratio = Math::div($this->preferredWidths[$columnIndex], $columnDimensions->getInnerWidth());
            if (Math::comp($ratio, $largestPercentRatio) >= 0) {
                $largestPercentRatio = $ratio;
            }
        }
        if (Math::comp($largestPercentRatio, '1') > 0) {
            foreach ($rows as $row) {
                foreach ($row->getChildren() as $columnIndex => $column) {
                    $columnDimensions = $column->getDimensions();
                    $innerWidth = Math::mul($columnDimensions->getInnerWidth(), $largestPercentRatio);
                    $columnStyle = $column->getStyle();
                    $spacing = Math::add($columnStyle->getHorizontalBordersWidth(), $columnStyle->getHorizontalPaddingsWidth());
                    $columnWidth = Math::add($innerWidth, $spacing);
                    $columnDimensions->setWidth($columnWidth);
                    $column->getFirstChild()->getDimensions()->setWidth($innerWidth);
                }
            }
        }
        $rowWidth = '0';
        foreach ($rows[0]->getChildren() as $column) {
            $rowWidth = Math::add($rowWidth, $column->getDimensions()->getWidth());
        }
        $this->setRowsWidth($rowWidth);
        return $this;
    }

    /**
     * Redistribute auto columns
     * @param string $spaceLeft
     * @return string left space
     */
    protected function redistributeAutoColumns(string $spaceLeft)
    {
        $full = '0';
        foreach ($this->autoColumns as $columnIndex => $columns) {
            $full = Math::add($full, $this->contentWidths[$columnIndex]);
        }
        $additionalFullWidth = '0';
        foreach ($this->autoColumns as $columnIndex => $columns) {
            $percent = Math::div($this->contentWidths[$columnIndex], $full);
            $additionalWidth = Math::mul($spaceLeft, $percent);
            $additionalFullWidth = Math::add($additionalFullWidth, $additionalWidth);
            foreach ($columns as $column) {
                $columnDimensions = $column->getDimensions();
                $columnDimensions->setWidth(Math::add($columnDimensions->getWidth(), $additionalWidth));
                $column->getFirstChild()->getDimensions()->setWidth($columnDimensions->getInnerWidth());
            }
        }
        return Math::sub($spaceLeft, $additionalFullWidth);
    }

    /**
     * Redistribute space that is available or exceed assignable width
     * @param string $availableSpace
     * @param array $rows
     * @param int $step
     * @return $this
     */
    protected function redistributeSpace(string $availableSpace, array $rows, int $step)
    {
        $currentWidth = $this->getDimensions()->getWidth();
        $spaceLeft = Math::sub($availableSpace, $currentWidth);
        foreach ($this->percentColumns as $columns) {
            $additionalWidth = Math::percent($columns[0]->getStyle()->getRules('width'), $spaceLeft);
            $spaceLeft = Math::sub($spaceLeft, $additionalWidth);
            foreach ($columns as $column) {
                $columnDimensions = $column->getDimensions();
                $columnDimensions->setWidth(Math::add($columnDimensions->getWidth(), $additionalWidth));
                $column->getFirstChild()->getDimensions()->setWidth($columnDimensions->getInnerWidth());
            }
        }
        if (Math::comp($spaceLeft, '0') > 0) {
            $spaceLeft = $this->redistributeAutoColumns($spaceLeft);
            // TODO redistribute pixel columns
        }
        $rowWidth = '0';
        foreach ($rows[0]->getChildren() as $column) {
            $rowWidth = Math::add($rowWidth, $column->getDimensions()->getWidth());
        }
        $this->setRowsWidth($rowWidth);
        return $this->finish();
    }

    /**
     * Finish table width calculations
     * @return $this
     */
    protected function finish()
    {
        $style = $this->getParent()->getStyle();
        $width = $this->getFirstChild()->getDimensions()->getWidth();
        $this->getDimensions()->setWidth(Math::add($width, $style->getHorizontalPaddingsWidth(), $style->getHorizontalBordersWidth()));
        foreach ($this->getCells() as $cell) {
            $cell->measureWidth();
        }
        return $this;
    }

    /**
     * Rollback widths to previous values
     * @param array $rows
     * return $this;
     */
    protected function rollBack($columnGroups, array $rows)
    {
        foreach ($this->beforeWidths as $columnIndex => $width) {
            foreach ($columnGroups[$columnIndex] as $column) {
                $column->getDimensions()->setWidth($this->beforeWidths[$columnIndex]);
                $column->getFirstChild()->getDimensions()->setWidth($column->getDimensions()->getInnerWidth());
            }
        }
        $rowWidth = '0';
        foreach ($rows[0]->getChildren() as $column) {
            $rowWidth = Math::add($rowWidth, $column->getDimensions()->getWidth());
        }
        $this->setRowsWidth($rowWidth);
        return $this;
    }

    /**
     * Check whenever table fill fit to available space
     * @param string $availableSpace
     * @return bool
     */
    protected function willFit(string $availableSpace)
    {
        $row = $this->getFirstChild()->getFirstChild();
        $width = $row->getDimensions()->getWidth();
        $width = Math::add($width, $this->getStyle()->getHorizontalBordersWidth(), $this->getStyle()->getHorizontalPaddingsWidth());
        return Math::comp($availableSpace, $width) >= 0;
    }

    /**
     * {@inheritdoc}
     */
    public function measureWidth()
    {
        foreach ($this->getCells() as $cell) {
            $cell->measureWidth();
        }
        $step = 0;
        $columnGroups = $this->getColumns();
        $this->setUpSizingTypes($columnGroups);
        $availableSpace = $this->getParent()->getDimensions()->computeAvailableSpace();
        if ($this->getParent()->getStyle()->getRules('width') !== 'auto') {
            $this->getParent()->applyStyleWidth();
            $availableSpace = Math::min($availableSpace, $this->getParent()->getDimensions()->getWidth());
        }
        $this->setUpWidths($columnGroups, $availableSpace);
        $rows = $this->getRows();
        $this->minContentGuess($rows);
        $this->minContentPercentageGuess($rows);
        if (!$this->willFit($availableSpace)) {
            $this->rollBack($columnGroups, $rows);
            return $this->redistributeSpace($availableSpace, $rows, $step);
        } else {
            $step = 1;
        }
        $this->minContentSpecifiedGuess($rows);
        if (!$this->willFit($availableSpace)) {
            $this->rollBack($columnGroups, $rows);
            return $this->redistributeSpace($availableSpace, $rows, $step);
        } else {
            $step = 2;
        }
        $this->maxContentGuess($rows);
        if (!$this->willFit($availableSpace)) {
            $this->rollBack($columnGroups, $rows);
            return $this->redistributeSpace($availableSpace, $rows, $step);
        } else {
            $step = 3;
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
