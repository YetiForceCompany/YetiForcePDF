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
use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;
use \YetiForcePDF\Layout\Coordinates\Coordinates;
use \YetiForcePDF\Layout\Coordinates\Offset;
use \YetiForcePDF\Layout\Dimensions\BoxDimensions;

/**
 * Class TableColumnBox
 */
class TableColumnBox extends InlineBlockBox
{
    /**
     * {@inheritdoc}
     */
    public function getInstructions(): string
    {
        return ''; // not renderable
    }

    /**
     * Measure width
     * @return $this
     */
    public function measureWidth()
    {
        foreach ($this->getChildren() as $child) {
            $child->measureWidth();
        }
        $maxWidth = 0;
        foreach ($this->getChildren() as $child) {
            $child->measureWidth();
            $maxWidth = Math::comp((string)$maxWidth, (string)$child->getDimensions()->getOuterWidth()) > 0 ? $maxWidth : $child->getDimensions()->getOuterWidth();
        }
        $style = $this->getStyle();
        $maxWidth = (float)Math::add((string)$maxWidth, Math::add((string)$style->getHorizontalBordersWidth(), (string)$style->getHorizontalPaddingsWidth()));
        $this->getDimensions()->setWidth($maxWidth);
        $this->applyStyleWidth();
        return $this;
    }
}
