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
     * {@inheritdoc}
     */
    public function getInstructions(): string
    {
        return ''; // not renderable
    }

}
