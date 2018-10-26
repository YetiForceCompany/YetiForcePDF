<?php
declare(strict_types=1);
/**
 * Box class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Layout\Coordinates\Coordinates;
use \YetiForcePDF\Layout\Coordinates\Offset;
use \YetiForcePDF\Layout\Dimensions\BoxDimensions;
use \YetiForcePDF\Html\Element;
use YetiForcePDF\Style\Style;

/**
 * Class Box
 */
class Box extends \YetiForcePDF\Base
{
    /**
     * Id of this box (should be cloned to track inline wrapped elements)
     * @var string
     */
    protected $id;
    /**
     * @var Box
     */
    protected $parent;
    /**
     * @var Box[]
     */
    protected $children = [];
    /**
     * @var Box
     */
    protected $next;
    /**
     * @var Box
     */
    protected $previous;
    /*
     * @var BoxDimensions
     */
    protected $dimensions;
    /**
     * @var Coordinates
     */
    protected $coordinates;
    /**
     * @var Offset
     */
    protected $offset;
    /**
     * @var bool
     */
    protected $root = false;
    /**
     * @var Style
     */
    protected $style;
    /**
     * Anonymous inline element is created to wrap TextBox
     * @var bool
     */
    protected $anonymous = false;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->id = uniqid();
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
     * Get box id (id might be cloned and then we can track cloned elements)
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get current box dom tree structure
     * @return \DOMElement
     */
    public function getDOMTree()
    {
        return $this->domTree;
    }

    /**
     * Set parent
     * @param \YetiForcePDF\Layout\Box|null $parent
     * @return $this
     */
    public function setParent(Box $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     * @return \YetiForcePDF\Layout\Box
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set next
     * @param \YetiForcePDF\Layout\Box|null $next
     * @return $this
     */
    public function setNext(Box $next = null)
    {
        $this->next = $next;
        return $this;
    }

    /**
     * Get next
     * @return \YetiForcePDF\Layout\Box
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * Set previous
     * @param \YetiForcePDF\Layout\Box|null $previous
     * @return $this
     */
    public function setPrevious(Box $previous = null)
    {
        $this->previous = $previous;
        return $this;
    }

    /**
     * Get previous
     * @return \YetiForcePDF\Layout\Box
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * Set root - is this root element?
     * @param bool $isRoot
     * @return $this
     */
    public function setRoot(bool $isRoot)
    {
        $this->root = $isRoot;
        return $this;
    }

    /**
     * Is this root element?
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->root;
    }

    /**
     * Set style
     * @param \YetiForcePDF\Style\Style $style
     * @return $this
     */
    public function setStyle(Style $style)
    {
        $this->style = $style;
        if ($element = $style->getElement()) {
            $element->setBox($this);
        }
        $style->setBox($this)->init();
        return $this;
    }

    /**
     * Get style
     * @return Style
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Is this box anonymous
     * @return bool
     */
    public function isAnonymous()
    {
        return $this->anonymous;
    }

    /**
     * Set anonymous field
     * @param bool $anonymous
     * @return $this
     */
    public function setAnonymous(bool $anonymous)
    {
        $this->anonymous = $anonymous;
        return $this;
    }

    /**
     * Append child box - line box can have only inline/block boxes - not line boxes!
     * @param Box $box
     * @return $this
     */
    public function appendChild(Box $box)
    {
        if ($this instanceof LineBox && $box instanceof LineBox) {
            throw new \InvalidArgumentException('LineBox cannot append another LineBox as child.');
        }
        $box->setParent($this);
        $childrenCount = count($this->children);
        if ($childrenCount > 0) {
            $previous = $this->children[$childrenCount - 1];
            $box->setPrevious($previous);
            $previous->setNext($box);
        } else {
            $box->setPrevious()->setNext();
        }
        $this->children[] = $box;
        return $this;
    }

    /**
     * Remove child
     * @param $child
     * @return Box
     */
    public function removeChild(Box $child)
    {
        $oldChildren = $this->children; // copy children
        $this->children = [];
        foreach ($oldChildren as $currentChild) {
            if ($currentChild !== $child) {
                $this->appendChild($currentChild);
            }
        }
        if ($child->getPrevious()) {
            if ($child->getNext()) {
                $child->getPrevious()->setNext($child->getNext());
            } else {
                $child->getPrevious()->setNext();
            }
        }
        if ($child->getNext()) {
            if ($child->getPrevious()) {
                $child->getNext()->setPrevious($child->getPrevious());
            } else {
                $child->getNext()->setPrevious();
            }
        }
        $child->setParent()->setPrevious()->setNext();
        return $child;
    }

    /**
     * Insert box before other box
     * @param \YetiForcePDF\Layout\Box $child
     * @param \YetiForcePDF\Layout\Box $before
     * @return $this
     */
    public function insertBefore(Box $child, Box $before)
    {
        $currentChildren = $this->children; // copy children
        $this->children = [];
        foreach ($currentChildren as $currentChild) {
            if ($currentChild === $before) {
                $this->appendChild($child);
                $this->appendChild($currentChild);
            } else {
                $this->appendChild($currentChild);
            }
        }
        return $this;
    }

    /**
     * Get children
     * @return Box[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Get all children
     * @param Box[] $allChildren
     * @return Box[]
     */
    public function getAllChildren(&$allChildren = [])
    {
        $allChildren[] = $this;
        foreach ($this->getChildren() as $child) {
            $child->getAllChildren($allChildren);
        }
        return $allChildren;
    }

    /**
     * Do we have children?
     * @return bool
     */
    public function hasChildren()
    {
        return isset($this->children[0]); // faster than count
    }

    /**
     * Get first child
     * @return \YetiForcePDF\Layout\Box|null
     */
    public function getFirstChild()
    {
        if (isset($this->children[0])) {
            return $this->children[0];
        }
    }

    /**
     * Get last child
     * @return \YetiForcePDF\Layout\Box|null
     */
    public function getLastChild()
    {
        if ($count = count($this->children)) {
            return $this->children[$count - 1];
        }
    }

    /**
     * Get closest line box
     * @return \YetiForcePDF\Layout\LineBox
     */
    public function getClosestLineBox()
    {
        $parent = $this->getParent();
        if ($parent instanceof LineBox) {
            return $parent;
        }
        return $parent->getClosestLineBox();
    }

    /**
     * Get closet box that is not a LineBox
     * @return \YetiForcePDF\Layout\Box
     */
    public function getClosestBox()
    {
        $parent = $this->getParent();
        if (!$parent instanceof LineBox) {
            return $parent;
        }
        return $parent->getClosestBox();
    }

    /**
     * Get dimensions
     * @return BoxDimensions
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * Get coordinates
     * @return Coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Shorthand for offset
     * @return Offset
     */
    public function getOffset(): Offset
    {
        return $this->offset;
    }

    /**
     * Get text content from current and all nested boxes
     * @return string
     */
    public function getTextContent()
    {
        $textContent = '';
        $allChildren = [];
        $this->getAllChildren($allChildren);
        foreach ($allChildren as $box) {
            if ($box instanceof TextBox) {
                $textContent .= $box->getText();
            }
        }
        return $textContent;
    }

    /**
     * Get first child text box
     * @return \YetiForcePDF\Layout\TextBox|null
     */
    public function getFirstTextBox()
    {
        if ($this instanceof TextBox) {
            return $this;
        }
        foreach ($this->getChildren() as $child) {
            if ($child instanceof TextBox) {
                return $child;
            }
            return $child->getFirstTextBox();
        }
    }

    /**
     * Get height from style
     * @return $this
     */
    public function applyStyleWidth()
    {
        $width = $this->getStyle()->getRules('width');
        if ($width === 'auto') {
            return $this;
        }
        if (!is_string($width)) {
            $this->getDimensions()->setWidth((float)$width);
            return $this;
        }
        $percentPos = strpos($width, '%');
        if ($percentPos !== false) {
            $widthInPercent = substr($width, 0, $percentPos);
            $parentWidth = $this->getClosestBox()->getDimensions()->getInnerWidth();
            if ($parentWidth) {
                $calculatedWidth = (float)bcmul(bcdiv((string)$parentWidth, '100', 4), $widthInPercent, 4);
                $this->getDimensions()->setWidth($calculatedWidth);
                return $this;
            }
            return $this;
        }
        return $this;
    }

    /**
     * Get height from style
     * @return $this
     */
    public function applyStyleHeight()
    {
        $height = $this->getStyle()->getRules('height');
        if ($height === 'auto') {
            return $this;
        }
        if (!is_string($height)) {
            $this->getDimensions()->setHeight((float)$height);
            return $this;
        }
        $percentPos = strpos($height, '%');
        if ($percentPos !== false) {
            $heightInPercent = substr($height, 0, $percentPos);
            $parentHeight = $this->getParent()->getDimensions()->getInnerHeight();
            if ($parentHeight) {
                $calculatedHeight = (float)bcmul(bcdiv((string)$parentHeight, '100'), $heightInPercent);
                $this->getDimensions()->setHeight($calculatedHeight);
                return $this;
            }
            return $this;
        }
        return $this;
    }

    /**
     * Fix offsets inside lines where text-align !== 'left'
     * @return $this
     */
    public function alignText()
    {
        if ($this instanceof LineBox) {
            $textAlign = $this->getParent()->getStyle()->getRules('text-align');
            if ($textAlign === 'right') {
                $offset = $this->getDimensions()->computeAvailableSpace() - $this->getChildrenWidth();
                foreach ($this->getChildren() as $childBox) {
                    $childBox->getOffset()->setLeft($childBox->getOffset()->getLeft() + $offset);
                }
            } elseif ($textAlign === 'center') {
                $offset = $this->getDimensions()->computeAvailableSpace() / 2 - $this->getChildrenWidth() / 2;
                foreach ($this->getChildren() as $childBox) {
                    $childBox->getOffset()->setLeft($childBox->getOffset()->getLeft() + $offset);
                }
            }
        } else {
            foreach ($this->getChildren() as $child) {
                $child->alignText();
            }
        }
        return $this;
    }

}
