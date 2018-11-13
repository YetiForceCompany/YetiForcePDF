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
        return Math::sub($this->getWidth(), $style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth());
    }

    /**
     * Get innerHeight
     * @return string
     */
    public function getInnerHeight(): string
    {
        $box = $this->getBox();
        $style = $box->getStyle();
        return Math::sub($this->getHeight(), $style->getVerticalBordersWidth(), $style->getVerticalPaddingsWidth());
    }


    /**
     * Get width with margins
     * @return string
     */
    public function getOuterWidth()
    {
        $box = $this->getBox();
        if (!$box instanceof LineBox) {
            $style = $this->getBox()->getStyle();
            $childrenWidth = '0';
            // if some of the children overflows
            if ($box->getStyle()->getRules('display') === 'inline') {
                foreach ($box->getChildren() as $child) {
                    $childrenWidth = Math::add($childrenWidth, $child->getDimensions()->getOuterWidth());
                }
            } else {
                foreach ($box->getChildren() as $child) {
                    $childrenWidth = Math::max($childrenWidth, $child->getDimensions()->getOuterWidth());
                }
            }
            if ($this->getWidth() !== null) {
                $childrenWidth = Math::add($childrenWidth, $style->getHorizontalMarginsWidth(), $style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth());
                $width = Math::add($this->getWidth(), $style->getHorizontalMarginsWidth());
                return Math::max($width, $childrenWidth);
            } else {
                return Math::add($childrenWidth, $style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth());
            }
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
        $box = $this->getBox();
        $style = $this->getBox()->getStyle();
        if (!$box instanceof LineBox) {
            $childrenHeight = '0';
            // if some of the children overflows
            if ($box->getStyle()->getRules('display') === 'inline') {
                foreach ($box->getChildren() as $child) {
                    $childrenHeight = Math::add($childrenHeight, $child->getDimensions()->getOuterHeight());
                }
            } else {
                foreach ($box->getChildren() as $child) {
                    $childrenHeight = Math::max($childrenHeight, $child->getDimensions()->getOuterHeight());
                }
            }
            if ($this->getHeight() !== null) {
                $height = Math::add($this->getHeight(), $style->getVerticalMarginsWidth());
                return Math::max($height, $childrenHeight);
            } else {
                return Math::add($childrenHeight, $style->getVerticalBordersWidth(), $style->getVerticalPaddingsWidth());
            }
        } else {
            return Math::add($this->getHeight(), $style->getHorizontalMarginsWidth());
        }
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
                $maxTextWidth = Math::max($maxTextWidth, $textWidth);
            } else {
                $minWidth = $childBox->getDimensions()->getMinWidth();
                $maxTextWidth = Math::max($maxTextWidth, $minWidth);
            }
        }
        $style = $this->getBox()->getStyle();
        return Math::add($maxTextWidth, $style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth(), $style->getHorizontalMarginsWidth());
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
                return Math::sub($parent->getDimensions()->computeAvailableSpace(), $parentStyle->getHorizontalBordersWidth(), $parentStyle->getHorizontalPaddingsWidth());
            } else {
                return $this->getBox()->getParent()->getDimensions()->getInnerWidth();
            }
        } else {
            return $this->document->getCurrentPage()->getDimensions()->getWidth();
        }
    }

    /**
     * Calculate width from style width:10%
     * @return mixed|null|string
     */
    public function getStyleWidth()
    {
        $width = $this->getBox()->getStyle()->getRules('width');
        if ($width === 'auto') {
            return null;
        }
        $percentPos = strpos($width, '%');
        if ($percentPos !== false) {
            $widthInPercent = substr($width, 0, $percentPos);
            $closestBoxDimensions = $this->getBox()->getClosestBox()->getDimensions();
            if ($closestBoxDimensions->getWidth() !== null) {
                $parentWidth = $closestBoxDimensions->getInnerWidth();
                if ($parentWidth) {
                    return Math::percent($widthInPercent, $parentWidth);
                }
            }
        }
        return null;
    }

}
