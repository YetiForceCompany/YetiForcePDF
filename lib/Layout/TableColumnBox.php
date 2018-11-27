<?php
declare(strict_types=1);
/**
 * TableColumnBox class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Math;


/**
 * Class TableColumnBox
 */
class TableColumnBox extends InlineBlockBox
{
    /**
     * @var int
     */
    protected $colSpan = 1;

    /**
     * @var int row span
     */
    protected $rowSpan = 1;

    /**
     * Set row span
     * @param int $rowSpan
     * @return $this
     */
    public function setRowSpan(int $rowSpan)
    {
        $this->rowSpan = $rowSpan;
        return $this;
    }

    /**
     * Get row span
     * @return int
     */
    public function getRowSpan()
    {
        return $this->rowSpan;
    }

    /**
     * Get column span
     * @return int
     */
    public function getColSpan()
    {
        return $this->colSpan;
    }

    /**
     * Set column span
     * @param int $colSpan
     * @return $this
     */
    public function setColSpan(int $colSpan)
    {
        $this->colSpan = $colSpan;
        return $this;
    }

    /**
     * We shouldn't append block box here
     */
    public function appendBlockBox($childDomElement, $element, $style, $parentBlock)
    {
    }

    /**
     * We shouldn't append table wrapper here
     */
    public function appendTableWrapperBox($childDomElement, $element, $style, $parentBlock)
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
     * Create cell box
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function createCellBox()
    {
        $style = (new \YetiForcePDF\Style\Style())
            ->setDocument($this->document)
            ->setContent('')
            ->parseInline();
        $box = (new TableCellBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        return $box;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstructions(): string
    {
        return ''; // not renderable
    }

}
