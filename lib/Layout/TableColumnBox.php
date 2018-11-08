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
     * @var string
     */
    protected $intrinsicPercentage = '0';

    /**
     * Get intrinsic percentage
     * @return string
     */
    public function getIntrinsicPercentage()
    {
        return $this->intrinsicPercentage;
    }

    /**
     * Set intrinsic percentage
     * @para string $percentage
     * @return $this
     */
    public function setIntrinsicPercentage(string $percentage)
    {
        $this->intrinsicPercentage = $percentage;
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
