<?php
declare(strict_types=1);
/**
 * BlockBox class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;
use \YetiForcePDF\Layout\Coordinates\Coordinates;
use \YetiForcePDF\Layout\Coordinates\Offset;
use \YetiForcePDF\Layout\Dimensions\BoxDimensions;
use \YetiForcePDF\Math;

/**
 * Class BlockBox
 */
class BlockBox extends ElementBox implements BoxInterface, AppendChildInterface, BuildTreeInterface
{

    /**
     * @var \YetiForcePDF\Layout\LineBox
     */
    protected $currentLineBox;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->dimensions = (new BoxDimensions())
            ->setDocument($this->document)
            ->setBox($this)
            ->init();
        $this->coordinates = (new Coordinates())
            ->setDocument($this->document)
            ->setBox($this)
            ->init();
        $this->offset = (new Offset())
            ->setDocument($this->document)
            ->setBox($this)
            ->init();
        return $this;
    }

    /**
     * Get element
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Set element
     * @param Element $element
     * @return $this
     */
    public function setElement(Element $element)
    {
        $this->element = $element;
        $element->setBox($this);
        return $this;
    }

    /**
     * Get new line box
     * @return \YetiForcePDF\Layout\LineBox
     */
    public function getNewLineBox()
    {
        $this->currentLineBox = (new LineBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->init();
        $this->appendChild($this->currentLineBox);
        $style = (new Style())
            ->setDocument($this->document)
            ->setBox($this->currentLineBox);
        $this->currentLineBox->setStyle($style);
        $this->currentLineBox->getStyle()->init();
        return $this->currentLineBox;
    }

    /**
     * Close line box
     * @param \YetiForcePDF\Layout\LineBox|null $lineBox
     * @param bool $createNew
     * @return \YetiForcePDF\Layout\LineBox
     */
    public function closeLine()
    {
        $this->currentLineBox = null;
        return $this->currentLineBox;
    }

    /**
     * Get current linebox
     * @return \YetiForcePDF\Layout\LineBox
     */
    public function getCurrentLineBox()
    {
        return $this->currentLineBox;
    }

    /**
     * {@inheritdoc}
     */
    public function appendBlockBox($childDomElement, $element, $style, $parentBlock)
    {
        if ($this->getCurrentLineBox()) {
            $this->closeLine();
        }
        $box = (new BlockBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        $box->buildTree($box);
        return $box;
    }

    /**
     * {@inheritdoc}
     */
    public function appendTableWrapperBlockBox($childDomElement, $element, $style, $parentBlock)
    {
        if ($this->getCurrentLineBox()) {
            $this->closeLine();
        }
        $box = (new TableWrapperBlockBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        // we wan't to build tree from here - we will build it from TableBox
        return $box;
    }

    /**
     * {@inheritdoc}
     */
    public function appendInlineBlockBox($childDomElement, $element, $style, $parentBlock)
    {
        if ($this->getCurrentLineBox()) {
            $currentLineBox = $this->getCurrentLineBox();
        } else {
            $currentLineBox = $this->getNewLineBox();
        }
        return $currentLineBox->appendInlineBlock($childDomElement, $element, $style, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function appendInlineBox($childDomElement, $element, $style, $parentBlock)
    {
        if ($this->getCurrentLineBox()) {
            $currentLineBox = $this->getCurrentLineBox();
        } else {
            $currentLineBox = $this->getNewLineBox();
        }
        return $currentLineBox->appendInline($childDomElement, $element, $style, $this);
    }

    /**
     * Measure width of this block
     * @return $this
     */
    public function measureWidth()
    {
        $dimensions = $this->getDimensions();
        $parent = $this->getParent();
        if ($parent) {
            if ($parent->getDimensions()->getWidth() !== null) {
                $dimensions->setWidth(Math::sub($parent->getDimensions()->getInnerWidth(), $this->getStyle()->getHorizontalMarginsWidth()));
                $this->applyStyleWidth();
                foreach ($this->getChildren() as $child) {
                    $child->measureWidth();
                }
                $this->divideLines();
                return $this;
            }
            foreach ($this->getChildren() as $child) {
                $child->measureWidth();
            }
            $this->divideLines();
            $maxWidth = '0';
            foreach ($this->getChildren() as $child) {
                $maxWidth = Math::comp($maxWidth, $child->getDimensions()->getOuterWidth()) > 0 ? $maxWidth : $child->getDimensions()->getOuterWidth();
            }
            $style = $this->getStyle();
            $maxWidth = Math::add($maxWidth, Math::add($style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth()));
            $maxWidth = Math::sub($maxWidth, $style->getHorizontalMarginsWidth());
            $dimensions->setWidth($maxWidth);
            $this->applyStyleWidth();
            return $this;
        }
        $dimensions->setWidth($this->document->getCurrentPage()->getDimensions()->getWidth());
        $this->applyStyleWidth();
        foreach ($this->getChildren() as $child) {
            $child->measureWidth();
        }
        $this->divideLines();
        return $this;
    }

    /**
     * Group sibling line boxes into two dimensional array
     * @return array
     */
    public function groupLines()
    {
        $lineGroups = [];
        $currentGroup = 0;
        foreach ($this->getChildren() as $child) {
            if ($child instanceof LineBox) {
                $lineGroups[$currentGroup][] = $child;
            } else {
                if (isset($lineGroups[$currentGroup])) {
                    $currentGroup++;
                }
            }
        }
        return $lineGroups;
    }

    /**
     * Merge line groups into one line (reverse divide - reorganize)
     * @return LineBox[]
     */
    public function mergeLineGroups(array $lineGroups)
    {
        $lines = [];
        foreach ($lineGroups as $index => $lines) {
            $curentLine = $this->getNewLineBox();
            foreach ($lines as $line) {
                foreach ($line->getChildren() as $child) {
                    $curentLine->appendChild($line->removeChild($child));
                }
            }
            $lines[] = $curentLine;
        }
        return $lines;
    }

    /**
     * Divide lines
     * @return $this
     */
    public function divideLines()
    {
        $this->mergeLineGroups($this->groupLines());
        foreach ($this->getChildren() as $child) {
            if ($child instanceof LineBox) {
                $lines = $child->divide();
                foreach ($lines as $line) {
                    $this->insertBefore($line, $child);
                    $line->getStyle()->init();
                    $line->measureWidth();
                }
                $this->removeChild($child);
            }
        }
        $this->removeEmptyLines();
        return $this;
    }

    /**
     * Measure height
     * @return $this
     */
    public function measureHeight()
    {
        foreach ($this->getChildren() as $child) {
            $child->measureHeight();
        }
        $height = '0';
        foreach ($this->getChildren() as $child) {
            $height = Math::add($height, $child->getDimensions()->getOuterHeight());
        }
        $rules = $this->getStyle()->getRules();
        $height = Math::add($height, Math::add($rules['border-top-width'], $rules['padding-top']));
        $height = Math::add($height, Math::add($rules['border-bottom-width'], $rules['padding-bottom']));
        $this->getDimensions()->setHeight($height);
        $this->applyStyleHeight();
        return $this;
    }

    /**
     * Offset elements
     * @return $this
     */
    public function measureOffset()
    {
        $top = $this->document->getCurrentPage()->getCoordinates()->getY();
        $left = $this->document->getCurrentPage()->getCoordinates()->getX();
        $marginTop = $this->getStyle()->getRules('margin-top');
        if ($parent = $this->getParent()) {
            $parentStyle = $parent->getStyle();
            $top = $parentStyle->getOffsetTop();
            $left = $parentStyle->getOffsetLeft();
            if ($previous = $this->getPrevious()) {
                $top = Math::add($previous->getOffset()->getTop(), $previous->getDimensions()->getHeight());
                if ($previous->getStyle()->getRules('display') === 'block') {
                    $marginTop = Math::comp($marginTop, $previous->getStyle()->getRules('margin-bottom')) > 0 ? $marginTop : $previous->getStyle()->getRules('margin-bottom');
                } elseif (!$previous instanceof LineBox) {
                    $marginTop = Math::add($marginTop, $previous->getStyle()->getRules('margin-bottom'));
                }
            }
        }
        $top = Math::add($top, $marginTop);
        $left = Math::add($left, $this->getStyle()->getRules('margin-left'));
        $this->getOffset()->setTop($top);
        $this->getOffset()->setLeft($left);
        foreach ($this->getChildren() as $child) {
            $child->measureOffset();
        }
        return $this;
    }

    /**
     * Position
     * @return $this
     */
    public function measurePosition()
    {
        $x = $this->document->getCurrentPage()->getCoordinates()->getX();
        $y = $this->document->getCurrentPage()->getCoordinates()->getY();
        if ($parent = $this->getParent()) {
            $x = Math::add($parent->getCoordinates()->getX(), $this->getOffset()->getLeft());
            $y = Math::add($parent->getCoordinates()->getY(), $this->getOffset()->getTop());
        }
        $this->getCoordinates()->setX($x);
        $this->getCoordinates()->setY($y);
        foreach ($this->getChildren() as $child) {
            $child->measurePosition();
        }
        return $this;
    }

    /**
     * Layout elements
     * @return $this
     */
    public function layout()
    {
        $this->measureWidth();
        $this->measureHeight();
        $this->measureOffset();
        $this->alignText();
        $this->measurePosition();
        return $this;
    }

    /**
     * Add background color instructions
     * @param array $element
     * @param $pdfX
     * @param $pdfY
     * @param $width
     * @param $height
     * @return array
     */
    public function addBackgroundColorInstructions(array $element, $pdfX, $pdfY, $width, $height)
    {
        $rules = $this->style->getRules();
        if ($rules['background-color'] !== 'transparent') {
            $x1 = '0';
            $y1 = $height;
            $x2 = $width;
            $y2 = '0';
            $bgColor = [
                'q',
                "1 0 0 1 $pdfX $pdfY cm",
                "{$rules['background-color'][0]} {$rules['background-color'][1]} {$rules['background-color'][2]} rg",
                "0 0 $width $height re",
                'f',
                'Q'
            ];
            $element = array_merge($element, $bgColor);
        }
        return $element;
    }

    /**
     * Get element PDF instructions to use in content stream
     * @return string
     */
    public function getInstructions(): string
    {
        $coordinates = $this->getCoordinates();
        $pdfX = $coordinates->getPdfX();
        $pdfY = $coordinates->getPdfY();
        $dimensions = $this->getDimensions();
        $width = $dimensions->getWidth();
        $height = $dimensions->getHeight();
        $element = [];
        $element = $this->addBackgroundColorInstructions($element, $pdfX, $pdfY, $width, $height);
        $element = $this->addBorderInstructions($element, $pdfX, $pdfY, $width, $height);
        return implode("\n", $element);
    }
}
