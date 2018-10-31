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
        return bcsub(bcsub($this->getWidth(), $style->getHorizontalBordersWidth(), 4), $style->getHorizontalPaddingsWidth(), 4);
    }

    /**
     * Get innerHeight
     * @return string
     */
    public function getInnerHeight(): string
    {
        $box = $this->getBox();
        $style = $box->getStyle();
        return bcsub(bcsub($this->getHeight(), $style->getVerticalBordersWidth(), 4), $style->getVerticalPaddingsWidth(), 4);
    }


    /**
     * Get width with margins
     * @return string
     */
    public function getOuterWidth()
    {
        $box = $this->getBox();
        if (!$box instanceof LineBox) {
            $rules = $this->getBox()->getStyle()->getRules();
            $childrenWidth = '0';
            // if some of the children overflows
            if ($box->getStyle()->getRules('display') === 'inline') {
                foreach ($box->getChildren() as $child) {
                    $childrenWidth = bcadd($childrenWidth, $child->getDimensions()->getOuterWidth(), 4);
                }
            } else {
                foreach ($box->getChildren() as $child) {
                    $outerWidth = $child->getDimensions()->getOuterWidth();
                    $childrenWidth = bccomp($childrenWidth, $outerWidth, 4) > 0 ? $childrenWidth : $outerWidth;
                }
            }
            $width = bcadd($this->getWidth(), bcadd($rules['margin-left'], $rules['margin-right'], 4), 4);
            return bccomp($width, $childrenWidth, 4) > 0 ? $width : $childrenWidth;
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
        return bcadd($this->getHeight(), bcadd($rules['margin-top'], $rules['margin-bottom'], 4), 4);
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
        $maxTextWidth = '0';
        foreach ($box->getChildren() as $childBox) {
            if ($childBox instanceof TextBox) {
                $textWidth = $childBox->getDimensions()->getTextWidth($childBox->getText());
                $maxTextWidth = bccomp($maxTextWidth, $textWidth, 4) > 0 ? $maxTextWidth : $textWidth;
            } else {
                $minWidth = $childBox->getDimensions()->getMinWidth();
                $maxTextWidth = bccomp($maxTextWidth, $minWidth, 4) > 0 ? $maxTextWidth : $minWidth;
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
                return bcsub(bcsub($this->getBox()->getParent()->getDimensions()->computeAvailableSpace(), $parentStyle->getHorizontalBordersWidth(), 4), $parentStyle->getHorizontalPaddingsWidth(), 4);
            } else {
                return $this->getBox()->getParent()->getDimensions()->getInnerWidth();
            }
        } else {
            return $this->document->getCurrentPage()->getDimensions()->getWidth();
        }
    }

}
