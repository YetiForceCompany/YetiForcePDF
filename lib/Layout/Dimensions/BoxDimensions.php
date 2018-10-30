<?php
declare(strict_types=1);
/**
 * BoxDimensions class
 *
 * @package   YetiForcePDF\Layout\Dimensions
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout\Dimensions;

use \YetiForcePDF\Layout\Box;
use \YetiForcePDF\Layout\LineBox;
use \YetiForcePDF\Layout\InlineBox;
use \YetiForcePDF\Layout\TextBox;

/**
 * Class BoxDimensions
 */
class BoxDimensions extends Dimensions
{

    /**
     * @var Box
     */
    protected $box;

    /**
     * Set box
     * @param \YetiForcePDF\Layout\Box $box
     * @return $this
     */
    public function setBox(Box $box)
    {
        $this->box = $box;
        return $this;
    }

    /**
     * Get box
     * @return \YetiForcePDF\Layout\Box
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * Get innerWidth
     * @return string
     */
    public function getInnerWidth(): string
    {
        $box = $this->getBox();
        $style = $box->getStyle();
        return bcsub(bcsub((string)$this->getWidth(), (string)$style->getHorizontalBordersWidth(), 4), (string)$style->getHorizontalPaddingsWidth(), 4);
    }

    /**
     * Get innerHeight
     * @return string
     */
    public function getInnerHeight(): string
    {
        $box = $this->getBox();
        $style = $box->getStyle();
        return bcsub(bcsub((string)$this->getHeight(), (string)$style->getVerticalBordersWidth(), 4), (string)$style->getVerticalPaddingsWidth(), 4);
    }


    /**
     * Get width with margins
     * @return string
     */
    public function getOuterWidth()
    {
        if (!$this->getBox() instanceof LineBox) {
            $rules = $this->getBox()->getStyle()->getRules();
            $childrenWidth = 0;
            // if some of the children overflows
            $box = $this->getBox();
            if ($box instanceof InlineBox) {
                foreach ($box->getChildren() as $child) {
                    $childrenWidth = bcadd((string)$childrenWidth, (string)$child->getDimensions()->getOuterWidth(), 4);
                }
            } else {
                foreach ($box->getChildren() as $child) {
                    $childrenWidth = bccomp((string)$childrenWidth, (string)$child->getDimensions()->getOuterWidth(), 4) > 0 ? $childrenWidth : $child->getDimensions()->getOuterWidth();
                }
            }
            $width = bcadd(bcadd((string)$this->getWidth(), (string)$rules['margin-left'], 4), (string)$rules['margin-right'], 4);
            return bccomp((string)$width, (string)$childrenWidth, 4) > 0 ? $width : $childrenWidth;
        } else {
            return $this->getBox()->getChildrenWidth();
        }
    }

    /**
     * Get height with margins
     * @return string
     */
    public function getOuterHeight()
    {
        $rules = $this->getBox()->getStyle()->getRules();
        return bcadd(bcadd((string)$this->getHeight(), (string)$rules['margin-top'], 4), (string)$rules['margin-bottom'], 4);
    }

    /**
     * Get minimum space that current box could have without overflow
     * @return float
     */
    public function getMinWidth()
    {
        $box = $this->getBox();
        if ($box instanceof TextBox) {
            return $this->getTextWidth($this->getBox()->getText());
        }
        $maxTextWidth = 0;
        foreach ($box->getChildren() as $childBox) {
            if ($childBox instanceof TextBox) {
                $maxTextWidth = bccomp((string)$maxTextWidth, (string)$childBox->getDimensions()->getTextWidth($childBox->getText()), 4) > 0 ? $maxTextWidth : $childBox->getDimensions()->getTextWidth($childBox->getText());
            } else {
                $maxTextWidth = bccomp((string)$maxTextWidth, (string)$childBox->getDimensions()->getMinWidth(), 4) > 0 ? $maxTextWidth : $childBox->getDimensions()->getMinWidth();
            }
        }
        return $maxTextWidth;
    }

    /**
     * Get text width
     * @param string $text
     * @return float
     */
    public function getTextWidth($text)
    {
        $font = $this->box->getStyle()->getFont();
        return $font->getTextWidth($text);
    }

    /**
     * Get text height
     * @param string $text
     * @return float
     */
    public function getTextHeight($text)
    {
        $font = $this->box->getStyle()->getFont();
        return $font->getTextHeight($text);
    }

    /**
     * Compute available space (basing on parent available space and parent border and padding)
     * @return float
     */
    public function computeAvailableSpace()
    {
        if ($parent = $this->getBox()->getParent()) {
            $parentStyle = $parent->getStyle();
            if ($parent->getDimensions()->getWidth() === null) {
                return bcsub(bcsub((string)$this->getBox()->getParent()->getDimensions()->computeAvailableSpace(), (string)$parentStyle->getHorizontalBordersWidth(), 4), (string)$parentStyle->getHorizontalPaddingsWidth(), 4);
            } else {
                return $this->getBox()->getParent()->getDimensions()->getInnerWidth();
            }
        } else {
            return $this->document->getCurrentPage()->getDimensions()->getWidth();
        }
    }

}
