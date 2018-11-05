<?php
declare(strict_types=1);
/**
 * LineBox class
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
 * Class LineBox
 */
class LineBox extends Box implements BoxInterface
{

    /**
     * Append block box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox|null $parentBlock
     * @return \YetiForcePDF\Layout\BlockBox
     */
    public function appendBlock($childDomElement, $element, $style, $parentBlock)
    {
        return $parentBlock->appendBlock($childDomElement, $element, $style, $parentBlock);
    }

    /**
     * Append table block box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox|null $parentBlock
     * @return \YetiForcePDF\Layout\BlockBox
     */
    public function appendTableBlock($childDomElement, $element, $style, $parentBlock)
    {
        return $parentBlock->appendTableBlock($childDomElement, $element, $style, $parentBlock);
    }

    /**
     * Append inline block box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox|null $parentBlock
     * @return \YetiForcePDF\Layout\InlineBlockBox
     */
    public function appendInlineBlock($childDomElement, $element, $style, $parentBlock)
    {
        $box = (new InlineBlockBox())
            ->setDocument($this->document)
            ->setElement($element)
            ->setParent($this)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        $box->buildTree($box);
        return $box;
    }

    /**
     * Add inline child (and split text to individual characters)
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox|null $parentBlock
     * @return \YetiForcePDF\Layout\InlineBox
     */
    public function appendInline($childDomElement, $element, $style, $parentBlock)
    {
        $box = (new InlineBox())
            ->setDocument($this->document)
            ->setElement($element)
            ->setParent($this)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        $box->buildTree($parentBlock);
        return $box;
    }

    /**
     * Will this box fit in line? (or need to create new one)
     * @param \YetiForcePDF\Layout\Box $box
     * @return bool
     */
    public function willFit(Box $box)
    {
        $childrenWidth = $this->getChildrenWidth();
        $availableSpace = $this->getDimensions()->computeAvailableSpace();
        $boxOuterWidth = $box->getDimensions()->getOuterWidth();
        return Math::comp(Math::sub($availableSpace, $childrenWidth), $boxOuterWidth) >= 0;
    }

    /**
     * Remove white spaces
     * @param $childBox
     * @return $this
     */
    public function removeWhiteSpaces($childBox)
    {
        if (!empty($childBox->getTextContent())) {
            return $this;
        }
        $previous = $childBox->getPrevious();
        if ($previous) {
            $previousText = $previous->getTextContent();
            while ($previous !== null && ($previousText === '' || $previousText === ' ')) {
                if ($previousText === ' ') {
                    $previous->getFirstTextBox()->setText('');
                    $previous->measureWidth();
                }
                $previous = $previous->getPrevious();
                $previousText = $previous->getTextContent();
            }
        }
        return $this;
    }

    /**
     * Divide this line into more lines when objects doesn't fit
     * @return LineBox[]
     */
    public function divide()
    {
        $lines = [];
        $line = (new LineBox())
            ->setDocument($this->document)
            ->setParent($this->getParent())
            ->setStyle(clone $this->style)
            ->init();
        $children = $this->getChildren();
        foreach ($children as $index => $childBox) {
            if ($line->willFit($childBox)) {
                // remove first white space
                if ($line->getTextContent() === '') {
                    $text = $childBox->getTextContent();
                    if ($text !== ' ') {
                        $line->appendChild($childBox);
                    } else {
                        $childBox->getFirstTextBox()->setText('');
                        $line->appendChild($childBox);
                    }
                } else {
                    if ($childBox->getTextContent() === ' ') {
                        $previousText = $children[$index - 1]->getTextContent();
                        if ($previousText !== ' ' && $previousText !== '') {
                            $line->appendChild($childBox);
                        } else {
                            $childBox->getFirstTextBox()->setText('');
                            $line->appendChild($childBox);
                        }
                    } else {
                        $line->appendChild($childBox);
                    }
                }
            } else {
                $textContent = $line->getTextContent();
                if ($textContent !== '' && $textContent !== ' ') {
                    $lines[] = $line->removeWhiteSpaces($childBox);
                }
                $line = (new LineBox())
                    ->setDocument($this->document)
                    ->setParent($this->getParent())
                    ->setStyle(clone $this->style)
                    ->init();
                $firstTextBox = $childBox->getFirstTextBox();
                if ($firstTextBox) {
                    $text = $firstTextBox->getText();
                    if ($text === ' ') {
                        $text = '';
                    }
                    $firstTextBox->setText($text);
                }
                $line->appendChild($childBox);
            }
        }
        // append last line
        $textContent = $line->getTextContent();
        if ($textContent !== '' && $textContent !== ' ') {
            $lines[] = $line->removeWhiteSpaces($childBox);
        }
        return $lines;

    }

    /**
     * Measure width
     * @return $this
     */
    public function measureWidth()
    {
        $this->clearStyles();
        $width = '0';
        foreach ($this->getChildren() as $child) {
            $child->measureWidth();
            $width = Math::add($width, $child->getDimensions()->getOuterWidth());
        }
        $this->getDimensions()->setWidth($width);
        return $this;
    }

    /**
     * Measure height
     * @return $this
     */
    public function measureHeight()
    {
        /*if ($this->getDimensions()->getWidth() === '0') {
            $this->getDimensions()->setHeight('0');
            return $this;
        }*/
        foreach ($this->getChildren() as $child) {
            $child->measureHeight();
        }
        $lineHeight = $this->getStyle()->getMaxLineHeight();
        $this->getDimensions()->setHeight($lineHeight);
        $this->measureMargins();
        return $this;
    }

    /**
     * Measure margins
     * @return $this
     */
    public function measureMargins()
    {
        $allChildren = [];
        $this->getAllChildren($allChildren);
        // array_reverse + array_pop + array_reverse is faster than array_shift
        $allChildren = array_reverse($allChildren);
        array_pop($allChildren);
        $allChildren = array_reverse($allChildren);
        $marginTop = '0';
        $marginBottom = '0';
        foreach ($allChildren as $child) {
            if (!$child instanceof InlineBox) {
                $marginTop = Math::comp($marginTop, $child->getStyle()->getRules('margin-top')) > 0 ? $marginTop : $child->getStyle()->getRules('margin-top');
                $marginBottom = Math::comp($marginBottom, $child->getStyle()->getRules('margin-bottom')) > 0 ? $marginBottom : $child->getStyle()->getRules('margin-bottom');
            }
        }
        $style = $this->getStyle();
        $style->setRule('margin-top', $marginTop);
        $style->setRule('margin-bottom', $marginBottom);
        return $this;
    }

    /**
     * Position
     * @return $this
     */
    public function measureOffset()
    {
        $parent = $this->getParent();
        $parentStyle = $parent->getStyle();
        $top = $parentStyle->getOffsetTop();
        $left = $parentStyle->getOffsetLeft();
        if ($previous = $this->getPrevious()) {
            $top = Math::add($previous->getOffset()->getTop(), Math::add($previous->getDimensions()->getHeight(), $previous->getStyle()->getRules('margin-bottom')));
        }
        $top = Math::add($top, $this->getStyle()->getRules('margin-top'));
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
        $parent = $this->getParent();
        $this->getCoordinates()->setX(Math::add($parent->getCoordinates()->getX(), $this->getOffset()->getLeft()));
        $this->getCoordinates()->setY(Math::add($parent->getCoordinates()->getY(), $this->getOffset()->getTop()));
        foreach ($this->getChildren() as $child) {
            $child->measurePosition();
        }
        return $this;
    }

    /**
     * Clear styles
     * return $this;
     */
    public function clearStyles()
    {
        $allNestedChildren = [];
        $maxLevel = '0';
        foreach ($this->getChildren() as $child) {
            $allChildren = [];
            $child->getAllChildren($allChildren);
            $maxLevel = Math::comp($maxLevel, (string)count($allChildren)) > 0 ? $maxLevel : (string)count($allChildren);
            $allNestedChildren[] = $allChildren;
        }
        $clones = [];
        for ($row = 0; $row < $maxLevel; $row++) {
            foreach ($allNestedChildren as $column => $childArray) {
                if (isset($childArray[$row])) {
                    $current = $childArray[$row];
                    $clones[$current->getId()][] = $current;
                }
            }
        }
        foreach ($clones as $row => $cloneArray) {
            $count = count($cloneArray);
            if ($count > 1) {
                foreach ($cloneArray as $index => $clone) {
                    if ($index === 0) {
                        $clone->getStyle()->clearFirstInline();
                    } elseif ($index === $count - 1) {
                        $clone->getStyle()->clearLastInline();
                    } elseif ($index > 0 && $index < ($count - 1)) {
                        $clone->getStyle()->clearMiddleInline();
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Get children width
     * @return string
     */
    public function getChildrenWidth()
    {
        $width = '0';
        foreach ($this->getChildren() as $childBox) {
            $width = Math::add($width, $childBox->getDimensions()->getOuterWidth());
        }
        return $width;
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
        $element = $this->addBorderInstructions($element, $pdfX, $pdfY, $width, $height);
        return implode("\n", $element);
    }
}
