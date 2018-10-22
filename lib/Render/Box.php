<?php
declare(strict_types=1);
/**
 * Box class
 *
 * @package   YetiForcePDF\Render
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render;

use \YetiForcePDF\Render\Coordinates\Coordinates;
use \YetiForcePDF\Render\Coordinates\Offset;
use \YetiForcePDF\Render\Dimensions\BoxDimensions;
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
	 * @var \DOMElement
	 */
	protected $domTree;
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
	 * @var string
	 */
	protected $text;
	/**
	 * @var bool
	 */
	protected $root = false;
	/**
	 * @var Style
	 */
	protected $style;


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
	 * @param \YetiForcePDF\Render\Box|null $parent
	 * @return $this
	 */
	public function setParent(Box $parent = null)
	{
		$this->parent = $parent;
		return $this;
	}

	/**
	 * Get parent
	 * @return \YetiForcePDF\Render\Box
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Set next
	 * @param \YetiForcePDF\Render\Box|null $next
	 * @return $this
	 */
	public function setNext(Box $next = null)
	{
		$this->next = $next;
		return $this;
	}

	/**
	 * Get next
	 * @return \YetiForcePDF\Render\Box
	 */
	public function getNext()
	{
		return $this->next;
	}

	/**
	 * Set previous
	 * @param \YetiForcePDF\Render\Box|null $previous
	 * @return $this
	 */
	public function setPrevious(Box $previous = null)
	{
		$this->previous = $previous;
		return $this;
	}

	/**
	 * Get previous
	 * @return \YetiForcePDF\Render\Box
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
		$style->setBox($this);
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
	 * Create and append text box (text node) element
	 * @param string   $text
	 * @param Box|null $insertBefore
	 * @return TextBox|null
	 */
	public function createTextBox(string $text, Box $insertBefore = null)
	{
		if (!$this instanceof LineBox && !$this instanceof TextBox) {
			$style = (new Style())->setDocument($this->document)->init();
			$style->setRule('display', 'inline');
			$box = (new TextBox())
				->setDocument($this->document)
				->setStyle($style)
				->setText($text)
				->init();
			if ($insertBefore) {
				$this->insertBefore($box, $insertBefore);
			} else {
				$this->appendChild($box);
			}
			return $box;
		}
		if ($this instanceof LineBox) {
			$box = $this->getParent()->createTextBox($text);
			$this->getParent()->removeChild($box);
			if ($insertBefore) {
				$this->insertBefore($box, $insertBefore);
			} else {
				$this->appendChild($box);
			}
			return $box;
		}
		throw new \InvalidArgumentException('Cannot create child element inside text node.');
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
	 * @param \YetiForcePDF\Render\Box $child
	 * @param \YetiForcePDF\Render\Box $before
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
	 * @return \YetiForcePDF\Render\Box|null
	 */
	public function getFirstChild()
	{
		if (isset($this->children[0])) {
			return $this->children[0];
		}
	}

	/**
	 * Get last child
	 * @return \YetiForcePDF\Render\Box|null
	 */
	public function getLastChild()
	{
		if ($count = count($this->children)) {
			return $this->children[$count - 1];
		}
	}

	/**
	 * Get closest line box
	 * @return \YetiForcePDF\Render\LineBox
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
	 * @return \YetiForcePDF\Render\Box
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
			$parentWidth = $this->getParent()->getDimensions()->getInnerWidth();
			if ($parentWidth) {
				$calculatedWidth = (float)bcmul(bcdiv((string)$parentWidth, '100'), $widthInPercent, 4);
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
