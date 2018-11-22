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
use \YetiForcePDF\Math;

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
     * @var bool do we need to measure this box ?
     */
    protected $forMeasurement = true;
    /**
     * @var bool is this element show up in view? take space?
     */
    protected $renderable = true;
    /**
     * @var array save state before it was unrenderable
     */
    protected $renderableState = [];

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
     * Set for measurement - enable or disable this box measurement
     * @param bool $forMeasure
     * @return $this
     */
    public function setForMeasurement(bool $forMeasurement)
    {
        $allChildren = [];
        $this->getAllChildren($allChildren);
        foreach ($allChildren as $child) {
            $child->forMeasurement = $forMeasurement;
        }
        return $this;
    }

    /**
     * Is this box available for measurement? or it should now have any width?
     * @return bool
     */
    public function isForMeasurement()
    {
        return $this->forMeasurement;
    }

    /**
     * Save renderable state
     * @return $this
     */
    protected function saveRenderableState()
    {
        $this->renderableState['styleRules'] = $this->getStyle()->getRules();
        $this->renderableState['width'] = $this->getDimensions()->getWidth();
        $this->renderableState['height'] = $this->getDimensions()->getHeight();
        return $this;
    }

    /**
     * Restore renderable state
     * @return $this
     */
    protected function restoreRenderableState()
    {
        $this->getStyle()->setRules($this->renderableState['styleRules']);
        $this->getDimensions()->setWidth($this->renderableState['width']);
        $this->getDimensions()->setHeight($this->renderableState['height']);
        return $this;
    }

    /**
     * Hide this element - set width / height to 0 and remove all styles that will change width / height
     * @return $this
     */
    protected function hide()
    {
        $this->getStyle()->setRules([
            'padding-top' => '0',
            'padding-right' => '0',
            'padding-bottom' => '0',
            'padding-left' => '0',
            'margin-top' => '0',
            'margin-right' => '0',
            'margin-bottom' => '0',
            'margin-left' => '0',
            'border-top-width' => '0',
            'border-right-width' => '0',
            'border-bottom-width' => '0',
            'border-left-width' => '0'
        ]);
        $this->getDimensions()->setWidth('0');
        $this->getDimensions()->setHeight('0');
        return $this;
    }

    /**
     * Set renderable
     * @param bool $renderable
     * @return $this
     */
    public function setRenderable(bool $renderable = true)
    {
        $changed = $this->renderable !== $renderable;
        if (!$this->renderable && $renderable) {
            $this->restoreRenderableState();
        }
        if ($this->renderable && !$renderable) {
            $this->saveRenderableState();
            $this->hide();
        }
        if ($changed) {
            $this->renderable = $renderable;
            foreach ($this->getChildren() as $child) {
                $child->setRenderable($renderable);
            }
        }
        return $this;
    }

    /**
     * Contain content - do we have some elements that are not whitespace characters?
     * @return bool
     */
    public function containContent()
    {
        if ($this instanceof TextBox && trim($this->getTextContent()) === '') {
            return false;
        }
        // we are not text node - traverse further
        $children = $this->getChildren();
        if (count($children) === 0 && !$this instanceof LineBox) {
            return true; // we are the content
        }
        if (count($children) === 0 && $this instanceof LineBox) {
            return false; // we are the content
        }
        $childrenContent = false;
        foreach ($children as $child) {
            $childrenContent = $childrenContent || $child->containContent();
        }
        return $childrenContent;
    }

    /**
     * Is this element renderable?
     * @return bool
     */
    public function isRenderable()
    {
        return $this->renderable;
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
     * Remove all children
     * @return $this
     */
    public function removeChildren()
    {
        foreach ($this->getChildren() as $child) {
            $child->removeChildren();
            $this->removeChild($child);
        }
        return $this;
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
     * Iterate all children
     * @param callable $fn
     * @param bool $reverse
     * @param bool $deep
     * @return $this
     */
    public function iterateChildren(callable $fn, bool $reverse = false, bool $deep = true)
    {
        $allChildren = [];
        if ($deep) {
            $this->getAllChildren($allChildren);
        } else {
            $allChildren = $this->getChildren();
        }
        if ($reverse) {
            $allChildren = array_reverse($allChildren);
        }
        foreach ($allChildren as $child) {
            if ($fn($child) === false) {
                break;
            }
        }
        return $this;
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
        $styleWidth = $this->getDimensions()->getStyleWidth();
        if ($styleWidth !== null) {
            $this->getDimensions()->setWidth($styleWidth);
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
        $percentPos = strpos($height, '%');
        if ($percentPos !== false) {
            $heightInPercent = substr($height, 0, $percentPos);
            $parentDimensions = $this->getParent()->getDimensions();
            if ($parentDimensions->getHeight() !== null) {
                $parentHeight = $this->getParent()->getDimensions()->getInnerHeight();
                if ($parentHeight) {
                    $calculatedHeight = Math::mul(Math::div($parentHeight, '100'), $heightInPercent);
                    $this->getDimensions()->setHeight($calculatedHeight);
                    return $this;
                }
            }
            return $this;
        }
        $this->getDimensions()->setHeight($height);
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
                $offset = Math::sub($this->getDimensions()->computeAvailableSpace(), $this->getChildrenWidth());
                foreach ($this->getChildren() as $childBox) {
                    $childBox->getOffset()->setLeft(Math::add($childBox->getOffset()->getLeft(), $offset));
                }
            } elseif ($textAlign === 'center') {
                $offset = Math::sub(Math::div($this->getDimensions()->computeAvailableSpace(), '2'), Math::div($this->getChildrenWidth(), '2'));
                foreach ($this->getChildren() as $childBox) {
                    $childBox->getOffset()->setLeft(Math::add($childBox->getOffset()->getLeft(), $offset));
                }
            }
        } else {
            foreach ($this->getChildren() as $child) {
                $child->alignText();
            }
        }
        return $this;
    }

    /**
     * Add border instructions
     * @param array $element
     * @param string $pdfX
     * @param string $pdfY
     * @param string $width
     * @param string $height
     * @return array
     */
    protected function addBorderInstructions(array $element, string $pdfX, string $pdfY, string $width, string $height)
    {
        if ($this->getStyle()->getRules('display') === 'none') {
            return $element;
        }
        $rules = $this->style->getRules();
        $x1 = '0';
        $x2 = $width;
        $y1 = $height;
        $y2 = '0';
        $element[] = '% start border';
        if ($rules['border-top-width'] && $rules['border-top-style'] !== 'none' && $rules['border-top-color'] !== 'transparent') {
            $path = implode(" l\n", [
                implode(' ', [$x2, $y1]),
                implode(' ', [Math::sub($x2, $rules['border-right-width']), Math::sub($y1, $rules['border-top-width'])]),
                implode(' ', [Math::add($x1, $rules['border-left-width']), Math::sub($y1, $rules['border-top-width'])]),
                implode(' ', [$x1, $y1])
            ]);
            $borderTop = [
                'q',
                "{$rules['border-top-color'][0]} {$rules['border-top-color'][1]} {$rules['border-top-color'][2]} rg",
                "1 0 0 1 $pdfX $pdfY cm",
                "$x1 $y1 m", // move to start point
                $path . ' l h',
                'f',
                'Q'
            ];
            $element = array_merge($element, $borderTop);
        }
        if ($rules['border-right-width'] && $rules['border-right-style'] !== 'none' && $rules['border-right-color'] !== 'transparent') {
            $path = implode(" l\n", [
                implode(' ', [$x2, $y2]),
                implode(' ', [Math::sub($x2, $rules['border-right-width']), Math::add($y2, $rules['border-bottom-width'])]),
                implode(' ', [Math::sub($x2, $rules['border-right-width']), Math::sub($y1, $rules['border-top-width'])]),
                implode(' ', [$x2, $y1]),
            ]);
            $borderTop = [
                'q',
                "1 0 0 1 $pdfX $pdfY cm",
                "{$rules['border-right-color'][0]} {$rules['border-right-color'][1]} {$rules['border-right-color'][2]} rg",
                "$x2 $y1 m",
                $path . ' l h',
                'f',
                'Q'
            ];
            $element = array_merge($element, $borderTop);
        }
        if ($rules['border-bottom-width'] && $rules['border-bottom-style'] !== 'none' && $rules['border-bottom-color'] !== 'transparent') {
            $path = implode(" l\n", [
                implode(' ', [$x2, $y2]),
                implode(' ', [Math::sub($x2, $rules['border-right-width']), Math::add($y2, $rules['border-bottom-width'])]),
                implode(' ', [Math::add($x1, $rules['border-left-width']), Math::add($y2, $rules['border-bottom-width'])]),
                implode(' ', [$x1, $y2]),
            ]);
            $borderTop = [
                'q',
                "1 0 0 1 $pdfX $pdfY cm",
                "{$rules['border-bottom-color'][0]} {$rules['border-bottom-color'][1]} {$rules['border-bottom-color'][2]} rg",
                "$x1 $y2 m",
                $path . ' l h',
                'f',
                'Q'
            ];
            $element = array_merge($element, $borderTop);
        }
        if ($rules['border-left-width'] && $rules['border-left-style'] !== 'none' && $rules['border-left-color'] !== 'transparent') {
            $path = implode(" l\n", [
                implode(' ', [Math::add($x1, $rules['border-left-width']), Math::sub($y1, $rules['border-top-width'])]),
                implode(' ', [Math::add($x1, $rules['border-left-width']), Math::add($y2, $rules['border-bottom-width'])]),
                implode(' ', [$x1, $y2]),
                implode(' ', [$x1, $y1]),
            ]);
            $borderTop = [
                'q',
                "1 0 0 1 $pdfX $pdfY cm",
                "{$rules['border-left-color'][0]} {$rules['border-left-color'][1]} {$rules['border-left-color'][2]} rg",
                "$x1 $y1 m",
                $path . ' l h',
                'f',
                'Q'
            ];
            $element = array_merge($element, $borderTop);
        }
        $element[] = '% end border';
        return $element;
    }

}
