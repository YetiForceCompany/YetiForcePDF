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
                $dimensions->setWidth(bcsub($parent->getDimensions()->getInnerWidth(), $this->getStyle()->getHorizontalMarginsWidth(), 4));
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
                $maxWidth = bccomp($maxWidth, $child->getDimensions()->getOuterWidth(), 4) > 0 ? $maxWidth : $child->getDimensions()->getOuterWidth();
            }
            $style = $this->getStyle();
            $maxWidth = bcadd($maxWidth, bcadd($style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth(), 4), 4);
            $maxWidth = bcsub($maxWidth, $style->getHorizontalMarginsWidth(), 4);
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
     * Divide lines
     * @return $this
     */
    public function divideLines()
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof LineBox) {
                $lines = $child->divide();
                foreach ($lines as $line) {
                    $this->insertBefore($line, $child);
                    $line->getStyle()->init();
                    $line->measureWidth();
                }
                $this->removeChild($child);
                if (isset($lines[1])) {
                    $child->measureWidth();
                }
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
            $height = bcadd($height, $child->getDimensions()->getOuterHeight(), 4);
        }
        $rules = $this->getStyle()->getRules();
        $height = bcadd($height, bcadd($rules['border-top-width'], $rules['padding-top'], 4), 4);
        $height = bcadd($height, bcadd($rules['border-bottom-width'], $rules['padding-bottom'], 4), 4);
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
                $top = bcadd($previous->getOffset()->getTop(), $previous->getDimensions()->getHeight(), 4);
                if ($previous->getStyle()->getRules('display') === 'block') {
                    $marginTop = bccomp($marginTop, $previous->getStyle()->getRules('margin-bottom'), 4) > 0 ? $marginTop : $previous->getStyle()->getRules('margin-bottom');
                } elseif (!$previous instanceof LineBox) {
                    $marginTop = bcadd($marginTop, $previous->getStyle()->getRules('margin-bottom'), 4);
                }
            }
        }
        $top = bcadd($top, $marginTop, 4);
        $left = bcadd($left, $this->getStyle()->getRules('margin-left'), 4);
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
            $x = bcadd($parent->getCoordinates()->getX(), $this->getOffset()->getLeft(), 4);
            $y = bcadd($parent->getCoordinates()->getY(), $this->getOffset()->getTop(), 4);
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
