<?php
declare(strict_types=1);
/**
 * InlineBox class
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
 * Class InlineBox
 */
class InlineBox extends ElementBox implements BoxInterface, BuildTreeInterface, AppendChildInterface
{
    /**
     * @var \YetiForcePDF\Layout\TextBox
     */
    protected $previousTextBox;

    /**
     * Go up to Line box and clone and wrap element
     * @param Box $box
     * @return Box
     */
    public function cloneParent(Box $box)
    {
        if ($parent = $this->getParent()) {
            $clone = clone $this;
            $clone->getStyle()->setBox($clone);
            $clone->getDimensions()->setBox($clone);
            $clone->getOffset()->setBox($clone);
            $clone->getElement()->setBox($clone);
            $clone->appendChild($box);
            if (!$parent instanceof LineBox) {
                $parent->cloneParent($clone);
            } else {
                $parent->appendChild($clone);
            }
        }
        return $box;
    }

    /**
     * {@inheritdoc}
     */
    public function appendBlockBox($childDomElement, $element, $style, $parentBlock)
    {
        $box = (new BlockBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        // if we add this child to parent box we loose parent inline styles if nested
        // so we need to wrap this box later and split lines at block element
        if (isset($this->getChildren()[0])) {
            $this->cloneParent($box);
        } else {
            $this->appendChild($box);
        }
        $box->getStyle()->init();
        $box->buildTree($box);
        return $box;
    }

    /**
     * {@inheritdoc}
     */
    public function appendTableWrapperBlockBox($childDomElement, $element, $style, $parentBlock)
    {
        $box = (new TableWrapperBlockBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        // if we add this child to parent box we loose parent inline styles if nested
        // so we need to wrap this box later and split lines at block element
        if (isset($this->getChildren()[0])) {
            $this->cloneParent($box);
        } else {
            $this->appendChild($box);
        }
        $box->getStyle()->init();
        $box->buildTree($box);
        return $box;
    }

    /**
     * {@inheritdoc}
     */
    public function appendInlineBlockBox($childDomElement, $element, $style, $parentBlock)
    {
        $box = (new InlineBlockBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        // if we add this child to parent box we loose parent inline styles if nested
        // so we need to wrap this box later and split lines at block element
        if (isset($this->getChildren()[0])) {
            $this->cloneParent($box);
        } else {
            $this->appendChild($box);
        }
        $box->getStyle()->init();
        $box->buildTree($box);
        return $box;
    }

    /**
     * {@inheritdoc}
     */
    public function appendInlineBox($childDomElement, $element, $style, $parentBlock)
    {
        $box = (new InlineBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        if (isset($this->getChildren()[0])) {
            $this->cloneParent($box);
        } else {
            $this->appendChild($box);
        }
        $box->getStyle()->init();
        $box->buildTree($parentBlock);
        return $box;
    }

    /**
     * Create text
     * @param $content
     * @return $this
     */
    public function createText($content, bool $sameId = false)
    {
        if ($sameId && $this->previousTextBox) {
            $box = clone $this->previousTextBox;
        } else {
            $box = (new TextBox())
                ->setDocument($this->document)
                ->setParent($this)
                ->init();
        }
        $box->setText($content);
        $this->previousTextBox = $box;
        if (isset($this->getChildren()[0])) {
            $this->previousTextBox = $this->cloneParent($box);
        } else {
            $this->appendChild($box);
            $this->previousTextBox = $box;
        }
        return $box;
    }

    /**
     * Get previous sibling inline-level element text
     * @return string|null
     */
    protected function getPreviousText()
    {
        $closest = $this->getClosestLineBox()->getLastChild();
        if ($previousTop = $closest->getPrevious()) {
            if ($textBox = $previousTop->getFirstTextBox()) {
                return $textBox->getText();
            }
        }
    }

    /**
     * Add text
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox|null $parentBlock
     * @return $this
     */
    public function appendText($childDomElement, $element = null, $style = null, $parentBlock = null)
    {
        $text = $childDomElement->textContent;
        $whiteSpace = $this->getStyle()->getRules('white-space');
        switch ($whiteSpace) {
            case 'normal':
            case 'nowrap':
                $text = preg_replace('/([\t ]+)?\r([\t ]+)?/u', "\r", $text);
                $text = preg_replace('/\r+/u', ' ', $text);
                $text = preg_replace('/\t+/u', ' ', $text);
                $text = preg_replace('/ +/u', ' ', $text);
                break;
        }
        if ($text !== '') {
            if ($whiteSpace === 'normal') {
                $words = preg_split('/ /u', $text, 0, PREG_SPLIT_NO_EMPTY);
                $count = count($words);
                if ($count) {
                    foreach ($words as $index => $word) {
                        $this->createText($word);
                        $parent = $this->getParent();
                        $anonymous = ($parent instanceof InlineBox && $parent->isAnonymous()) || $parent instanceof LineBox;
                        if ($index + 1 !== $count || $anonymous) {
                            $this->createText(' ', true);
                        }
                    }
                } else {
                    $this->createText(' ', true);
                }
            } elseif ($whiteSpace === 'nowrap') {
                $this->createText($text, true);
            }
        }
        return $this;
    }

    /**
     * Measure width
     * @return $this
     */
    public function measureWidth()
    {
        $width = '0';
        foreach ($this->getChildren() as $child) {
            $child->measureWidth();
            $width = bcadd($width, (string)$child->getDimensions()->getOuterWidth(), 4);
        }
        $style = $this->getStyle();
        $width = bcadd($width, bcadd((string)$style->getHorizontalBordersWidth(), (string)$style->getHorizontalPaddingsWidth(), 4), 4);
        $this->getDimensions()->setWidth($width);
        $this->applyStyleWidth();
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
        $this->getDimensions()->setHeight(bcadd((string)$this->getStyle()->getLineHeight(), (string)$this->getStyle()->getVerticalPaddingsWidth(), 4));
        $this->applyStyleHeight();
        return $this;
    }

    /**
     * Position
     * @return $this
     */
    public function measureOffset()
    {
        $rules = $this->getStyle()->getRules();
        $parent = $this->getParent();
        $top = $parent->getStyle()->getOffsetTop();
        $lineHeight = $this->getClosestLineBox()->getDimensions()->getHeight();
        if ($rules['vertical-align'] === 'bottom') {
            $top = $lineHeight - $this->getDimensions()->getHeight();
        } elseif ($rules['vertical-align'] === 'top') {
            $top = 0;
        } elseif ($rules['vertical-align'] === 'middle' || $rules['vertical-align'] === 'baseline') {
            $height = $this->getDimensions()->getHeight();
            $top = (float)bcsub(bcdiv((string)$lineHeight, '2', 4), bcdiv((string)$height, '2', 4), 4);
        }
        // margin top inside inline and inline block doesn't affect relative to line top position
        // it only affects line margins
        $left = (string)$rules['margin-left'];
        if ($previous = $this->getPrevious()) {
            $left = bcadd($left, bcadd(bcadd((string)$previous->getOffset()->getLeft(), (string)$previous->getDimensions()->getWidth(), 4), (string)$previous->getStyle()->getRules('margin-right'), 4));
        } else {
            $left = bcadd($left, (string)$parent->getStyle()->getOffsetLeft());
        }
        $this->getOffset()->setLeft((float)$left);
        $this->getOffset()->setTop($top);
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
        $this->getCoordinates()->setX($parent->getCoordinates()->getX() + $this->getOffset()->getLeft());
        $parent = $this->getClosestLineBox();
        $this->getCoordinates()->setY($parent->getCoordinates()->getY() + $this->getOffset()->getTop());
        foreach ($this->getChildren() as $child) {
            $child->measurePosition();
        }
        return $this;
    }

    public function __clone()
    {
        $this->element = clone $this->element;
        $this->style = clone $this->style;
        $this->offset = clone $this->offset;
        $this->dimensions = clone $this->dimensions;
        $this->coordinates = clone $this->coordinates;
        $this->children = [];
    }



    public function addBackgroundColorInstructions(array $element, $pdfX, $pdfY, $width, $height)
    {
        $rules = $this->style->getRules();
        if ($rules['background-color'] !== 'transparent') {
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
