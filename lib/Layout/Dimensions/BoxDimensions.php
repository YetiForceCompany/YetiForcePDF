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

use \YetiForcePDF\Math;
use \YetiForcePDF\Layout\Box;
use \YetiForcePDF\Layout\LineBox;
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
        return Math::sub(Math::sub($this->getWidth(), $style->getHorizontalBordersWidth()), $style->getHorizontalPaddingsWidth());
    }

    /**
     * Get innerHeight
     * @return string
     */
    public function getInnerHeight(): string
    {
        $box = $this->getBox();
        $style = $box->getStyle();
        return Math::sub(Math::sub($this->getHeight(), $style->getVerticalBordersWidth()), $style->getVerticalPaddingsWidth());
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
                    $childrenWidth = Math::add($childrenWidth, $child->getDimensions()->getOuterWidth());
                }
            } else {
                foreach ($box->getChildren() as $child) {
                    $outerWidth = $child->getDimensions()->getOuterWidth();
                    $childrenWidth = Math::comp($childrenWidth, $outerWidth) > 0 ? $childrenWidth : $outerWidth;
                }
            }
            $width = Math::add($this->getWidth(), Math::add($rules['margin-left'], $rules['margin-right']));
            return Math::comp($width, $childrenWidth) > 0 ? $width : $childrenWidth;
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
        return Math::add($this->getHeight(), Math::add($rules['margin-top'], $rules['margin-bottom']));
    }

    /**
     * Get minimum space that current box could have without overflow
     * @return string
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
                $maxTextWidth = Math::comp($maxTextWidth, $textWidth) > 0 ? $maxTextWidth : $textWidth;
            } else {
                $minWidth = $childBox->getDimensions()->getMinWidth();
                $maxTextWidth = Math::comp($maxTextWidth, $minWidth) > 0 ? $maxTextWidth : $minWidth;
            }
        }
        return $maxTextWidth;
    }

    /**
     * Get text width
     * @param string $text
     * @return string
     */
    public function getTextWidth($text)
    {
        $font = $this->box->getStyle()->getFont();
        return $font->getTextWidth($text);
    }

    /**
     * Get text height
     * @param string $text
     * @return string
     */
    public function getTextHeight($text)
    {
        $font = $this->box->getStyle()->getFont();
        return $font->getTextHeight($text);
    }

    /**
     * Compute available space (basing on parent available space and parent border and padding)
     * @return string
     */
    public function computeAvailableSpace()
    {
        if ($parent = $this->getBox()->getParent()) {
            $parentStyle = $parent->getStyle();
            if ($parent->getDimensions()->getWidth() === null) {
                return Math::sub(Math::sub($this->getBox()->getParent()->getDimensions()->computeAvailableSpace(), $parentStyle->getHorizontalBordersWidth()), $parentStyle->getHorizontalPaddingsWidth());
            } else {
                return $this->getBox()->getParent()->getDimensions()->getInnerWidth();
            }
        } else {
            return $this->document->getCurrentPage()->getDimensions()->getWidth();
        }
    }

}
