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
     * @var array minimal widths
     */
    protected $minWidths = [];
    /**
     * @var array preferred widths
     */
    protected $preferredWidths = [];
    /**
     * @var array maximal widths
     */
    protected $contentWidths = [];
    /**
     * @var string total min width
     */
    protected $minWidth = '0';
    /**
     * @var string total preferred width
     */
    protected $preferredWidth = '0';
    /**
     * @var string total max width
     */
    protected $contentWidth = '0';
    /**
     * @var array percentages for each percentage column
     */
    protected $percentages = [];
    /**
     * @var string cell spacing total width
     */
    protected $cellSpacingWidth = '0';
    /**
     * @var string total border width
     */
    protected $borderWidth = '0';
    /**
     * @var array percentage columns
     */
    protected $percentColumns = [];
    /**
     * @var array pixel columns
     */
    protected $pixelColumns = [];
    /**
     * @var array auto width columns
     */
    protected $autoColumns = [];
    /**
     * @var array saving state
     */
    protected $beforeWidths = [];
    /**
     * @var array rows
     */
    protected $rows = [];

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
     * Create row group inside table
     * return TableRowGroupBox
     */
    public function createRowGroup()
    {
        $style = (new \YetiForcePDF\Style\Style())
            ->setDocument($this->document)
            ->setContent('')
            ->parseInline();
        $box = (new TableRowGroupBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        return $box;
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
                $cell = $column->getFirstChild();
                $cellStyle = $cell->getStyle();
                $columnStyle = $column->getStyle();
                $columnOuterWidth = $column->getDimensions()->getOuterWidth();
                if ($column->getColSpan() > 1) {
                    $columnOuterWidth = Math::div($columnOuterWidth, (string)$column->getColSpan());
                }
                $columnSpacing = Math::add($columnStyle->getHorizontalBordersWidth(), $columnStyle->getHorizontalPaddingsWidth());
                $columnInnerWidth = Math::sub($columnOuterWidth, $columnSpacing);
                $styleWidth = $columnStyle->getRules('width');
                $this->contentWidths[$columnIndex] = Math::max($this->contentWidths[$columnIndex], $columnInnerWidth);
                $minColumnWidth = $cell->getDimensions()->getMinWidth();
                if ($column->getColSpan() > 1) {
                    $minColumnWidth = Math::div($minColumnWidth, (string)$column->getColSpan());
                }
                $this->minWidths[$columnIndex] = Math::max($this->minWidths[$columnIndex], $minColumnWidth);
                if ($styleWidth !== 'auto' && strpos($styleWidth, '%') === false) {
                    if ($column->getColSpan() > 1) {
                        $styleWidth = Math::div($styleWidth, (string)$column->getColSpan());
                    }
                    $preferred = Math::max($styleWidth, $minColumnWidth);
                    $this->minWidths[$columnIndex] = $preferred;
                } elseif (strpos($styleWidth, '%') > 0) {
                    $preferred = Math::max($this->preferredWidths[$columnIndex], $columnInnerWidth);
                    $this->percentages[$columnIndex] = Math::max($this->percentages[$columnIndex] ?? '0', trim($styleWidth, '%'));
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
    protected function setRowsWidth()
    {
        $width = '0';
        foreach ($this->rows[0]->getChildren() as $column) {
            $width = Math::add($width, $column->getDimensions()->getWidth());
        }
        foreach ($this->getRows() as $row) {
            $rowStyle = $row->getStyle();
            $rowSpacing = Math::add($rowStyle->getHorizontalPaddingsWidth(), $rowStyle->getHorizontalBordersWidth());
            $width = Math::add($width, $rowSpacing);
            $row->getDimensions()->setWidth($width);
            $row->getParent()->getDimensions()->setWidth($width);
        }
        $style = $this->getStyle();
        $spacing = Math::add($style->getHorizontalPaddingsWidth(), $style->getHorizontalBordersWidth());
        $width = Math::add($width, $spacing);
        $this->getDimensions()->setWidth($width);
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
                $this->setColumnWidth($column, $this->minWidths[$columnIndex]);
            }
        }
        $this->setRowsWidth();
        return $this;
    }

    /**
     * Add to preferred others (add left space to preferred width of auto/pixel columns)
     * @param string $leftSpace
     * @return string
     */
    protected function addToPreferredOthers(string $leftSpace)
    {
        $autoNeededTotal = '0';
        $pixelNeededTotal = '0';
        $autoNeeded = [];
        $pixelNeeded = [];
        foreach ($this->autoColumns as $columnIndex => $columns) {
            $colDmns = $columns[0]->getDimensions();
            $colWidth = $colDmns->getInnerWidth();
            if (Math::comp($this->preferredWidth[$columnIndex], $colWidth) > 0) {
                $autoNeeded[$columnIndex] = Math::sub($this->preferredWidth[$columnIndex], $colWidth);
                $autoNeededTotal = Math::add($autoNeededTotal, $autoNeeded[$columnIndex]);
            }
        }
        foreach ($this->pixelColumns as $columnIndex => $columns) {
            $colDmns = $columns[0]->getDimensions();
            $colWidth = $colDmns->getInnerWidth();
            if (Math::comp($this->preferredWidth[$columnIndex], $colWidth) > 0) {
                $pixelNeeded[$columnIndex] = Math::sub($this->preferredWidth[$columnIndex], $colWidth);
                $pixelNeededTotal = Math::add($pixelNeededTotal, $pixelNeeded[$columnIndex]);
            }
        }
        // ok, we know where we need to add extra space
        $totalNeeded = Math::add($autoNeededTotal, $pixelNeededTotal);
        $totalToAdd = Math::min($leftSpace, $totalNeeded);
        // we know how much we can distribute
        $autoTotalRatio = Math::div($autoNeededTotal, $totalNeeded);
        $addToAutoTotal = Math::mul($autoTotalRatio, $totalToAdd);
        $addToPixelTotal = Math::sub($totalToAdd, $addToAutoTotal);
        // we know how much space we can add to each column type (auto and pixel)
        // now we must distribute this space according to concrete column needs
        foreach ($this->autoColumns as $columnIndex => $columns) {
            if (isset($autoNeeded[$columnIndex])) {
                $neededRatio = Math::div($autoNeeded[$columnIndex], $autoNeededTotal);
                $add = Math::mul($neededRatio, $addToAutoTotal);
                $columnWidth = Math::add($columns[0]->getDimensions()->getWidth(), $add);
                foreach ($columns as $column) {
                    $colDmns = $column->getDimensions();
                    $colDmns->setWidth($columnWidth);
                    $column->getFirstChild()->getDimensions()->setWidth($colDmns->getInnerWidth());
                }
            }
        }
        foreach ($this->pixelColumns as $columnIndex => $columns) {
            if (isset($pixelNeeded[$columnIndex])) {
                $neededRatio = Math::div($pixelNeeded[$columnIndex], $pixelNeededTotal);
                $add = Math::mul($neededRatio, $addToPixelTotal);
                $columnWidth = Math::add($columns[0]->getDimensions()->getWidth(), $add);
                foreach ($columns as $column) {
                    $colDmns = $column->getDimensions();
                    $colDmns->setWidth($columnWidth);
                    $column->getFirstChild()->getDimensions()->setWidth($colDmns->getInnerWidth());
                }
            }
        }
        $leftSpace = Math::sub($leftSpace, $totalToAdd);
        return $leftSpace;
    }

    /**
     * Add to others (left space to auto/pixel columns)
     * @param string $leftSpace
     * @param bool $withPreferred
     * @return $this
     */
    protected function addToOthers(string $leftSpace, bool $withPreferred = false)
    {
        // first of all try to redistribute space to columns that need it most (width is under preferred)
        // left space is the space that we can add to other column types that needs extra space to preferred width
        if ($withPreferred) {
            $leftSpace = $this->addToPreferredOthers($leftSpace);
        }

        // ok, we've redistribute space to columns that needs it but if there is space left we must redistribute it
        // to fulfill percentages
        if (Math::comp($leftSpace, '0') === 0) {
            return $this;
        }
        // first redistribute it to auto columns because they are most flexible ones
        if ($count = count($this->autoColumns)) {
            $autoColumnsMaxWidth = $this->getAutoColumnsMaxWidth();
            foreach ($this->autoColumns as $columnIndex => $columns) {
                $ratio = Math::div($this->contentWidths[$columnIndex], $autoColumnsMaxWidth);
                $add = Math::mul($leftSpace, $ratio);
                $colWidth = Math::add($columns[0]->getDimensions()->getWidth(), $add);
                foreach ($columns as $column) {
                    $colDmns = $column->getDimensions();
                    $colDmns->setWidth($colWidth);
                    $column->getFirstChild()->getDimensions()->setWidth($colDmns->getInnerWidth());
                }
                if (!$withPreferred) {
                    // if not to preferred it means that we adding to min widths
                    $this->minWidths[$columnIndex] = $colWidth;
                }
            }
        } elseif ($count = count($this->pixelColumns)) {
            // next redistribute left space to pixel columns if there where no auto columns
            $add = Math::div($leftSpace, (string)$count);
            foreach ($this->pixelColumns as $columnIndex => $columns) {
                $colWidth = Math::add($columns[0]->getDimensions()->getWidth(), $add);
                foreach ($columns as $column) {
                    $colDmns = $column->getDimensions();
                    $colDmns->setWidth($colWidth);
                    $column->getFirstChild()->getDimensions()->setWidth($colDmns->getInnerWidth());
                }
                if (!$withPreferred) {
                    // if not to preferred it means that we adding to min widths
                    $this->minWidths[$columnIndex] = $colWidth;
                }
            }
        }
        return $this;
    }

    /**
     * Get current others width (auto, pixel columns)
     * @return string
     */
    protected function getCurrentOthersWidth()
    {
        $currentOthersWidth = '0';
        foreach ($this->autoColumns as $columnIndex => $columns) {
            $currentOthersWidth = Math::add($currentOthersWidth, $columns[0]->getDimensions()->getInnerWidth());
        }
        foreach ($this->pixelColumns as $columnIndex => $columns) {
            $currentOthersWidth = Math::add($currentOthersWidth, $columns[0]->getDimensions()->getInnerWidth());
        }
        return $currentOthersWidth;
    }

    /**
     * Get total percentage
     * @return string
     */
    protected function getTotalPercentage()
    {
        $totalPercentageSpecified = '0';
        foreach ($this->percentColumns as $columnIndex => $columns) {
            $totalPercentageSpecified = Math::add($totalPercentageSpecified, $this->percentages[$columnIndex]);
        }
        return $totalPercentageSpecified;
    }

    /**
     * Get total percentages width
     * @return string
     */
    protected function getTotalPercentageWidth()
    {
        $totalPercentageColumnsWidth = '0';
        foreach ($this->percentColumns as $columnIndex => $columns) {
            $totalPercentageColumnsWidth = Math::add($totalPercentageColumnsWidth, $columns[0]->getDimensions()->getInnerWidth());
        }
        return $totalPercentageColumnsWidth;
    }

    /**
     * Expand percents to min width
     * @return $this
     */
    protected function expandPercentsToMin()
    {
        $totalPercentageSpecified = $this->getTotalPercentage();
        $maxPercentRatio = '0';
        $maxPercentRatioIndex = 0;
        $ratioPercent = '0';
        foreach ($this->percentages as $columnIndex => $percent) {
            $ratio = Math::div($this->minWidths[$columnIndex], $percent);
            if (Math::comp($ratio, $maxPercentRatio) > 0) {
                $maxPercentRatio = $ratio;
                $maxPercentRatioIndex = $columnIndex;
                $ratioPercent = $percent;
            }
        }
        $minWidth = $this->minWidths[$maxPercentRatioIndex];
        // lowerPercent = minWidth
        $onePercent = Math::div($minWidth, $ratioPercent);
        // we have one percent width, we must apply this to all percentages and other columns
        $currentPercentsWidth = '0';
        foreach ($this->percentColumns as $columnIndex => $columns) {
            $columnWidth = Math::mul($this->percentages[$columnIndex], $onePercent);
            foreach ($columns as $rowIndex => $column) {
                $columnStyle = $column->getStyle();
                $column->getDimensions()->setWidth(Math::add($columnWidth, $columnStyle->getHorizontalPaddingsWidth()));
                $column->getFirstChild()->getDimensions()->setWidth($columnWidth);
            }
            $this->minWidths[$columnIndex] = $columnWidth;
            $currentPercentsWidth = Math::add($currentPercentsWidth, $column->getDimensions()->getInnerWidth());
        }
        // percentage columns are satisfied, other columns must fulfill percentages
        $otherPercent = Math::sub('100', $totalPercentageSpecified);
        $othersWidth = Math::mul($otherPercent, $onePercent);
        $currentOthersWidth = $this->getCurrentOthersWidth();
        $leftSpace = Math::sub($othersWidth, $currentOthersWidth);
        $this->addToOthers($leftSpace);
        return $this;
    }

    /**
     * Apply percentage dimensions
     * @return $this
     */
    protected function applyPercentage()
    {
        $currentRowWidth = '0';
        if ($this->getParent()->getStyle()->getRules('width') === 'auto') {
            foreach ($this->getRows()[0]->getChildren() as $columnIndex => $column) {
                $currentRowWidth = Math::add($currentRowWidth, $column->getDimensions()->getInnerWidth());
            }
        } else {
            $currentRowWidth = $this->getParent()->getDimensions()->getInnerWidth();
        }
        $mustExpand = false;
        foreach ($this->percentColumns as $columnIndex => $columns) {
            $columnWidth = Math::percent($this->percentages[$columnIndex], $currentRowWidth);
            if (Math::comp($this->minWidths[$columnIndex], $columnWidth) > 0) {
                // we need to expand proportionally
                $mustExpand = true;
                break;
            }
        }
        if ($mustExpand) {
            $this->expandPercentsToMin();
        } else {
            // everything is ok we can resize percentages
            foreach ($this->percentColumns as $columnIndex => $columns) {
                $columnWidth = Math::percent($this->percentages[$columnIndex], $currentRowWidth);
                foreach ($columns as $rowIndex => $column) {
                    $this->setColumnWidth($column, $columnWidth);
                }
            }
        }
        return $this;
    }

    /**
     * Save current columns width state
     * @param array $rows
     * @return $this
     */
    protected function saveState(array $rows)
    {
        $this->beforeWidths = [];
        foreach ($rows[0]->getChildren() as $columnIndex => $column) {
            $this->beforeWidths[$columnIndex] = $column->getDimensions()->getWidth();
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
        $this->saveState($rows);
        $this->applyPercentage();
        $this->setRowsWidth();
        return $this;
    }

    /**
     * Minimal content specified guess
     * @param array $rows
     * @return $this
     */
    protected function minContentSpecifiedGuess(array $rows)
    {
        $this->saveState($rows);
        $leftWidth = '0';
        foreach ($this->pixelColumns as $columnIndex => $columns) {
            foreach ($columns as $column) {
                $this->setColumnWidth($column, $this->preferredWidths[$columnIndex]);
            }
            $leftWidth = Math::add($leftWidth, $column->getDimensions()->getWidth());
        }
        foreach ($this->autoColumns as $columnIndex => $columns) {
            $leftWidth = Math::add($leftWidth, $columns[0]->getDimensions()->getWidth());
        }
        $this->applyPercentage();
        $this->setRowsWidth();
        return $this;
    }

    /**
     * Set column width
     * @param $column
     * @param string $width
     */
    protected function setColumnWidth($column, string $width)
    {
        $columnStyle = $column->getStyle();
        $cell = $column->getFirstChild();
        $width = Math::add($width, $columnStyle->getHorizontalPaddingsWidth());
        $column->getDimensions()->setWidth($width);
        $cell->getDimensions()->setWidth($column->getDimensions()->getInnerWidth());
    }

    /**
     * Maximal content guess
     * @param array $rows
     * @return $this
     */
    protected function maxContentGuess(array $rows)
    {
        $this->saveState($rows);
        foreach ($this->autoColumns as $columnIndex => $columns) {
            foreach ($columns as $column) {
                $this->setColumnWidth($column, $this->contentWidths[$columnIndex]);
            }
        }
        $this->applyPercentage();
        $this->setRowsWidth();
        return $this;
    }

    /**
     * Span rows
     * @return $this
     */
    public function spanRows()
    {
        $toRemove = [];
        foreach ($this->rows as $rowIndex => $row) {
            foreach ($row->getChildren() as $columnIndex => $column) {
                if ($column->getRowSpan() > 1) {
                    $rowSpans = $column->getRowSpan();
                    $spanHeight = '0';
                    for ($i = 1; $i < $rowSpans; $i++) {
                        $nextRowGroup = $row->getParent()->getParent()->getChildren()[$rowIndex + $i];
                        $spanColumn = $nextRowGroup->getFirstChild()->getChildren()[$columnIndex];
                        $spanHeight = Math::add($spanHeight, $spanColumn->getDimensions()->getHeight());
                        $toRemove[] = $spanColumn;
                    }
                    if ($rowIndex + $i === count($this->getChildren()) && $column->getStyle()->getRules('border-collapse') === 'separate') {
                        $spanHeight = Math::sub($spanHeight, $column->getStyle()->getRules('border-spacing'));
                    }
                    $colDmns = $column->getDimensions();
                    $colDmns->setHeight(Math::add($colDmns->getHeight(), $spanHeight));
                    $cell = $column->getFirstChild();
                    $colInnerHeight = $colDmns->getInnerHeight();
                    $cell->getDimensions()->setHeight($colInnerHeight);
                    $cellHeight = '0';
                    foreach ($cell->getChildren() as $cellChild) {
                        $cellHeight = Math::add($cellHeight, $cellChild->getDimensions()->getHeight());
                    }
                    $columnStyle = $column->getStyle();
                    $cellStyle = $column->getFirstChild()->getStyle();
                    if ($columnStyle->getRules('border-collapse') === 'collapse' && $rowIndex + $i === count($this->getChildren())) {
                        $cellStyle->setRule('border-bottom-width', $cellStyle->getRules('border-top-width'));
                    }
                    $toDisposition = Math::sub($colInnerHeight, $cellHeight);
                    switch ($columnStyle->getRules('vertical-align')) {
                        case 'baseline':
                        case 'middle':
                            $padding = Math::div($toDisposition, '2');
                            $cellStyle->setRule('padding-top', $padding);
                            $cellStyle->setRule('padding-bottom', $padding);
                            break;
                        case 'top':
                            $cellStyle->setRule('padding-bottom', $toDisposition);
                            break;
                        case 'bottom':
                            $cellStyle->setRule('padding-top', $toDisposition);
                            break;
                    }
                    $cell->measureWidth();
                    $cell->measureHeight();
                    $cell->measureOffset();
                    $cell->measurePosition();
                }
            }
        }
        foreach ($toRemove as $remove) {
            $remove->getParent()->removeChild($remove);
        }
        return $this;
    }

    /**
     * Finish table width calculations
     * @return $this
     */
    protected function finish()
    {
        foreach ($this->rows as $row) {
            $row->spanColumns();
        }
        $style = $this->getStyle();
        $width = $this->rows[0]->getDimensions()->getWidth();
        $width = Math::add($width, $style->getHorizontalPaddingsWidth(), $style->getHorizontalBordersWidth());
        $this->getDimensions()->setWidth($width);
        $parent = $this->getParent();
        $parentStyle = $parent->getStyle();
        if ($parentStyle->getRules('width') === 'auto') {
            $parentSpacing = Math::add($parentStyle->getHorizontalBordersWidth(), $parentStyle->getHorizontalPaddingsWidth());
            $width = Math::add($width, $parentSpacing);
            $parent->getDimensions()->setWidth($width);
        }
        foreach ($this->getCells() as $cell) {
            $cell->measureWidth();
        }
        return $this;
    }

    /**
     * Check whenever table fill fit to available space
     * @param string $availableSpace
     * @return bool
     */
    protected function willFit(string $availableSpace)
    {
        $row = $this->rows[0];
        $width = $row->getDimensions()->getWidth();
        $width = Math::add($width, $this->getStyle()->getHorizontalBordersWidth(), $this->getStyle()->getHorizontalPaddingsWidth());
        return Math::comp($availableSpace, $width) >= 0;
    }

    /**
     * Get row inner width
     * @return string
     */
    protected function getRowInnerWidth()
    {
        $width = '0';
        foreach ($this->rows[0]->getChildren() as $column) {
            $width = Math::add($width, $column->getDimensions()->getWidth());
        }
        return $width;
    }

    /**
     * Get auto columns max width
     * @return string
     */
    protected function getAutoColumnsMaxWidth()
    {
        $autoColumnsMaxWidth = '0';
        foreach ($this->autoColumns as $columnIndex => $columns) {
            $autoColumnsMaxWidth = Math::add($autoColumnsMaxWidth, $this->contentWidths[$columnIndex]);
        }
        return $autoColumnsMaxWidth;
    }

    /**
     * Get auto columns min width
     * @return string
     */
    protected function getAutoColumnsMinWidth()
    {
        $autoColumnsMinWidth = '0';
        foreach ($this->autoColumns as $columnIndex => $columns) {
            $autoColumnsMinWidth = Math::add($autoColumnsMinWidth, $this->minWidths[$columnIndex]);
        }
        return $autoColumnsMinWidth;
    }

    /**
     * Get auto columns width
     * @return string
     */
    protected function getAutoColumnsWidth()
    {
        $autoColumnsWidth = '0';
        foreach ($this->autoColumns as $columnIndex => $columns) {
            $autoColumnsWidth = Math::add($autoColumnsWidth, $columns[0]->getDimensions()->getInnerWidth());
        }
        return $autoColumnsWidth;
    }

    /**
     * Shrink to fit
     * @param string $availableSpace
     * @param int $step
     * @return TableBox
     */
    protected function shrinkToFit(string $availableSpace, int $step)
    {
        $parentStyle = $this->getParent()->getStyle();
        $parentSpacing = Math::add($parentStyle->getHorizontalBordersWidth(), $parentStyle->getHorizontalPaddingsWidth());
        $availableSpace = Math::sub($availableSpace, $this->cellSpacingWidth, $parentSpacing);
        $currentWidth = Math::sub($this->getRowInnerWidth(), $this->cellSpacingWidth);
        $toRemoveTotal = Math::sub($currentWidth, $availableSpace);
        $totalPercentages = '0';
        foreach ($this->percentages as $percentage) {
            $totalPercentages = Math::add($totalPercentages, $percentage);
        }
        $percentagesFullWidth = Math::percent($totalPercentages, $availableSpace);
        $eachPercentagesWidth = [];
        foreach ($this->percentages as $columnIndex => $percent) {
            $eachPercentagesWidth[$columnIndex] = Math::percent($percent, $availableSpace);
        }
        $nonPercentageSpace = Math::sub($availableSpace, $percentagesFullWidth);
        $autoColumnsMinWidth = $this->getAutoColumnsMinWidth();
        $autoColumnsMaxWidth = $this->getAutoColumnsMaxWidth();
        $totalPixelWidth = '0';
        foreach ($this->pixelColumns as $columnIndex => $columns) {
            $totalPixelWidth = Math::add($totalPixelWidth, $this->preferredWidths[$columnIndex]);
        }
        switch ($step) {
            case 0:
                // minimal stays minimal - decreasing percents
                $rowWidth = '0';
                foreach ($this->percentColumns as $columnIndex => $columns) {
                    $totalPercent = Math::div($this->percentages[$columnIndex], $totalPercentages);
                    $toRemove = Math::percent($totalPercent, $toRemoveTotal);
                    foreach ($columns as $column) {
                        $cDimensions = $column->getDimensions();
                        $cDimensions->setWidth(Math::sub($cDimensions->getWidth(), $toRemove));
                        $column->getFirstChild()->getDimensions()->setWidth($cDimensions->getInnerWidth());
                    }
                    $rowWidth = Math::add($rowWidth, $cDimensions->getWidth());
                }
                $this->setRowsWidth();
                break;
            case 1:
                // minimal stays minimal, decreasing pixels
                $toPixelDisposition = Math::sub($nonPercentageSpace, $autoColumnsMinWidth);
                foreach ($this->pixelColumns as $columnIndex => $columns) {
                    $ratio = Math::div($this->preferredWidths[$columnIndex], $totalPixelWidth);
                    $columnWidth = Math::mul($toPixelDisposition, $ratio);
                    foreach ($columns as $column) {
                        $columnDimensions = $column->getDimensions();
                        $columnDimensions->setWidth(Math::add($columnWidth, $column->getStyle()->getHorizotnalPaddingsWidth()));
                        $column->getFirstChild()->getDimensions()->setWidth($columnDimensions->getInnerWidth());
                    }
                }
                foreach ($this->percentColumns as $columnIndex => $columns) {
                    foreach ($columns as $column) {
                        $columnDimensions = $column->getDimensions();
                        $columnDimensions->setWidth(Math::add($eachPercentagesWidth[$columnIndex], $column->getStyle()->getHorizontalPaddingsWidth()));
                        $column->getFirstChild()->getDimensions()->setWidth($columnDimensions->getInnerWidth());
                    }
                }
                $this->setRowsWidth();
                break;
            case 2:
                // minimal stays minimal, pixels stays untouched, auto columns decreasing
                $toAutoDisposition = Math::sub($nonPercentageSpace, $totalPixelWidth);
                foreach ($this->autoColumns as $columnIndex => $columns) {
                    $ratio = Math::div($this->contentWidths[$columnIndex], $autoColumnsMaxWidth);
                    $columnWidth = Math::mul($toAutoDisposition, $ratio);
                    foreach ($columns as $column) {
                        $columnDimensions = $column->getDimensions();
                        $columnDimensions->setWidth(Math::add($columnWidth, $column->getStyle()->getHorizontalPaddingsWidth()));
                        $column->getFirstChild()->getDimensions()->setWidth($columnDimensions->getInnerWidth());
                    }
                }
                foreach ($this->percentColumns as $columnIndex => $columns) {
                    foreach ($columns as $column) {
                        $columnDimensions = $column->getDimensions();
                        $columnDimensions->setWidth(Math::add($eachPercentagesWidth[$columnIndex], $column->getStyle()->getHorizontalPaddingsWidth()));
                        $column->getFirstChild()->getDimensions()->setWidth($columnDimensions->getInnerWidth());
                    }
                }
                $this->setRowsWidth();
                break;
        }
        return $this->finish();
    }

    /**
     * Try preferred width
     * @param string $leftSpace
     * @param bool $outerWidthSet
     * @return $this|TableBox
     */
    protected function tryPreferred(string $leftSpace, bool $outerWidthSet)
    {
        // left space is 100% width that we can use
        $totalPercentages = '0';
        $totalPercentagesWidth = '0';
        foreach ($this->percentages as $columnIndex => $percentage) {
            $totalPercentages = Math::add($totalPercentages, $percentage);
            $colWidth = $this->rows[0]->getChildren()[$columnIndex]->getDimensions()->getInnerWidth();
            $totalPercentagesWidth = Math::add($totalPercentagesWidth, $colWidth);
        }
        $forPercentages = Math::percent($totalPercentages, $leftSpace);
        $neededTotal = '0';
        $needed = [];
        foreach ($this->percentColumns as $columnIndex => $columns) {
            $colDmns = $columns[0]->getDimensions();
            $colWidth = $colDmns->getInnerWidth();
            if (Math::comp($colWidth, $this->contentWidths[$columnIndex]) < 0) {
                $needed[$columnIndex] = Math::sub($this->contentWidths[$columnIndex], $colWidth);
                $neededTotal = Math::add($neededTotal, $needed[$columnIndex]);
            }
        }
        if (Math::comp($neededTotal, '0') === 0 && !$outerWidthSet) {
            return $this->setRowsWidth();
        }
        $currentPercentsWidth = '0';
        $addToPercents = Math::min($neededTotal, $forPercentages);
        foreach ($this->percentColumns as $columnIndex => $columns) {
            if (Math::comp($addToPercents, $neededTotal) < 0) {
                $ratio = Math::div($this->percentages[$columnIndex], $totalPercentages);
                $add = Math::mul($ratio, $addToPercents);
            } else {
                if (isset($needed[$columnIndex])) {
                    $add = $needed[$columnIndex];
                } else {
                    $add = '0';
                }
            }
            foreach ($columns as $column) {
                $colDmns = $column->getDimensions();
                $colDmns->setWidth(Math::add($colDmns->getWidth(), $add));
                $column->getFirstChild()->getDimensions()->setWidth($colDmns->getInnerWidth());
            }
            $currentPercentsWidth = Math::add($currentPercentsWidth, $colDmns->getInnerWidth());
        }
        // we've added space to percentage columns, now we must calculate how much space we need to add (to have 100%)
        $leftSpace = Math::sub($leftSpace, $addToPercents);
        $leftPercent = Math::sub('100', $totalPercentages);
        $restHave = $this->getCurrentOthersWidth();
        // if 25% = $currentPercentsWidth
        if (Math::comp($totalPercentages, '0') > 0) {
            $onePercent = Math::div($currentPercentsWidth, $totalPercentages);
            $restMustHave = Math::mul($leftPercent, $onePercent);
            $leftSpace = Math::min($leftSpace, Math::sub($restMustHave, $restHave));
        }

        if (Math::comp($leftSpace, '0') === 0) {
            return $this->setRowsWidth();
        }
        // left space MUST be redistributed to fulfill new percentages
        $this->addToOthers($leftSpace, true);
        // percent columns were redistributed in the first step so we don't need to do anything
        $this->setRowsWidth();
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
        $step = 0;
        $columnGroups = $this->getColumns();
        $this->setUpSizingTypes($columnGroups);
        $availableSpace = $this->getParent()->getDimensions()->computeAvailableSpace();
        $outerWidthSet = false;
        if ($this->getParent()->getStyle()->getRules('width') !== 'auto') {
            $this->getParent()->applyStyleWidth();
            $availableSpace = Math::min($availableSpace, $this->getParent()->getDimensions()->getInnerWidth());
            $outerWidthSet = true;
        }
        $rows = $this->rows = $this->getRows();
        $this->setUpWidths($columnGroups, $availableSpace);
        $this->minContentGuess($rows);
        $this->minContentPercentageGuess($rows);
        if (!$this->willFit($availableSpace)) {
            return $this->shrinkToFit($availableSpace, $step);
        } else {
            $step = 1;
        }
        $this->minContentSpecifiedGuess($rows);
        if (!$this->willFit($availableSpace)) {
            return $this->shrinkToFit($availableSpace, $step);
        } else {
            $step = 2;
        }
        $this->maxContentGuess($rows);
        if (!$this->willFit($availableSpace)) {
            return $this->shrinkToFit($availableSpace, $step);
        }
        $leftSpace = Math::sub($availableSpace, $this->getDimensions()->getWidth());
        if (Math::comp($leftSpace, '0') > 0) {
            $this->tryPreferred($leftSpace, $outerWidthSet);
        }
        return $this->finish();
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
                $columnHeight = Math::div($columnHeight, (string)$column->getRowSpan());
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
                $height = Math::div($height, (string)$column->getRowSpan());
                $cellChildrenHeight = '0';
                foreach ($cell->getChildren() as $cellChild) {
                    $cellChildrenHeight = Math::add($cellChildrenHeight, $cellChild->getDimensions()->getOuterHeight());
                }
                $cellStyle = $cell->getStyle();
                $cellVerticalSize = Math::add($cellStyle->getVerticalBordersWidth(), $cellStyle->getVerticalPaddingsWidth());
                $cellChildrenHeight = Math::add($cellChildrenHeight, $cellVerticalSize);
                $cellChildrenHeight = Math::div($cellChildrenHeight, (string)$column->getRowSpan());
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
