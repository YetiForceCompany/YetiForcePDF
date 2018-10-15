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
	protected $textNode = false;
	/**
	 * @var bool
	 */
	protected $root = false;


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
	 * Set text node status
	 * @param bool $isTextNode
	 * @return $this
	 */
	public function setTextNode(bool $isTextNode = false)
	{
		$this->textNode = $isTextNode;
		return $this;
	}

	/**
	 * Is this text node? or element
	 * @return bool
	 */
	public function isTextNode(): bool
	{
		return $this->textNode;
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
	 * Create and append text box (text node) element
	 * @param string   $text
	 * @param Box|null $insertBefore
	 * @return InlineBox|null
	 */
	public function createTextBox(string $text, Box $insertBefore = null)
	{
		if (!$this instanceof LineBox && !$this->isTextNode()) {
			$style = (new Style())->setDocument($this->document)->init();
			$style->setRule('display', 'inline');
			$box = (new InlineBox())
				->setDocument($this->document)
				->setStyle($style)
				->setTextNode(true)
				->setText($text)
				->init();
			if ($insertBefore) {
				$this->insertBefore($box, $insertBefore);
			} else {
				$this->appendChild($box);
			}
			$box->getDimensions()->setUpAvailableSpace();
			return $box;
		} elseif ($this instanceof LineBox) {
			$box = $this->getParent()->createTextBox($text);
			$this->getParent()->removeChild($box);
			if ($insertBefore) {
				$this->insertBefore($box, $insertBefore);
			} else {
				$this->appendChild($box);
			}
			$box->getDimensions()->setUpAvailableSpace();
			return $box;
		} else {
			throw new \InvalidArgumentException('Cannot create child element inside text node.');
		}
	}

	/**
	 * Wrap each child element with clone of this box instance and push each element to parent box
	 */
	public function cutAndWrap()
	{
		if ($this instanceof InlineBox) {
			$parent = $this->getParent();
			$count = count($this->getChildren());
			foreach ($this->getChildren() as $child) {
				$child->cutAndWrap();
			}
			if ($count > 1) {
				foreach ($this->getChildren() as $index => $child) {
					$clone = clone $this;
					$clone->getDimensions()->setBox($clone);
					$clone->getCoordinates()->setBox($clone);
					$clone->getOffset()->setBox($clone);
					$clone->getStyle()->setBox($clone);
					if ($clone->getElement()) {
						$clone->getElement()->setBox($clone);
					}
					$clone->appendChild($child);
					$parent->insertBefore($clone, $this);
					if ($count > 1) {
						if ($index === 0) {
							$clone->getStyle()->clearFirstInline();
						} elseif ($index === $count - 1) {
							$clone->getStyle()->clearLastInline();
						} else {
							$clone->getStyle()->clearMiddleInline();
						}
					}
					$clone->measurePhaseOne();
					$clone->getDimensions()->setUpAvailableSpace();
				}
				$parent->removeChild($this);
				$parent->measurePhaseOne();
			}

		} else {
			foreach ($this->getChildren() as $childBox) {
				$childBox->cutAndWrap();
			}
		}
	}


	/**
	 * Append child box - line box can have only inline/block boxes - not line boxes!
	 * @param Box $box
	 * @return $this
	 */
	public function appendChild(Box $box)
	{
		if ($this instanceof LineBox && $box instanceof LineBox) {
			throw new \InvalidArgumentException('LineBox cannnot append another LineBox as child.');
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
		$box->getDimensions()->setUpAvailableSpace();
		return $this;
	}

	/**
	 * Append children boxes
	 * @param Box $box
	 * @return $this
	 */
	public function appendChildren(array $boxes)
	{
		foreach ($boxes as $box) {
			$this->appendChild($box);
		}
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
	 * Get parent width shorthand
	 * @return float
	 */
	protected function getParentWidth()
	{
		if ($parent = $this->getParent()) {
			return $parent->getDimensions()->getWidth();
		} else {
			// if there is no parent - root element get width from page width - margins
			return $this->document->getCurrentPage()->getDimensions()->getWidth();
		}
	}

	/**
	 * Get parent height shorthand
	 * @return float
	 */
	protected function getParentHeight()
	{
		if ($parent = $this->getParent()) {
			return $parent->getDimensions()->getHeight();
		} else {
			// if there is no parent - root element get width from page width - margins
			return $this->document->getCurrentPage()->getDimensions()->getHeight();
		}
	}

	/**
	 * Get parent inner width shorthand
	 * @return float
	 */
	protected function getParentInnerWidth()
	{
		if ($parent = $this->getParent()) {
			return $parent->getDimensions()->getInnerWidth();
		} else {
			// if there is no parent - root element get width from page width - margins
			return $this->document->getCurrentPage()->getDimensions()->getWidth();
		}
	}

	/**
	 * Get parent height shorthand
	 * @return float
	 */
	protected function getParentInnerHeight()
	{
		if ($parent = $this->getParent()) {
			return $parent->getDimensions()->getInnerHeight();
		} else {
			// if there is no parent - root element get width from page width - margins
			return $this->document->getCurrentPage()->getDimensions()->getHeight();
		}
	}

	/**
	 * Get percent height of the parent box
	 * @param string $width
	 * @return float|int|string
	 */
	public function getPercentWidth(string $width)
	{
		$percentPos = strpos($width, '%');
		if ($percentPos !== false) {
			$widthInPercent = substr($width, 0, $percentPos);
			$parentWidth = $this->getParentInnerWidth();
			if ($parentWidth) {
				return $parentWidth / 100 * (float)$widthInPercent;
			}
		} else {
			return (float)$width;
		}
	}

	/**
	 * Get percent height of the parent box
	 * @param string $height
	 * @return float|int|string
	 */
	public function getPercentHeight(string $height)
	{
		$percentPos = strpos($height, '%');
		if ($percentPos !== false) {
			$heightInPercent = substr($height, 0, $percentPos);
			$parentHeight = $this->getParentInnerHeight();
			if ($parentHeight) {
				return $parentHeight / 100 * $heightInPercent;
			}
		} else {
			return (float)$height;
		}
	}

	/**
	 * Take style specified dimensions instead of calculated one
	 * @return $this
	 */
	protected function takeStyleDimensions()
	{
		if (!$this instanceof LineBox) {
			if ($this->getStyle()->getRules('display') !== 'inline') {
				$dimensions = $this->getDimensions();
				$width = $this->getStyle()->getRules('width');
				if ($width !== 'auto') {
					$dimensions->setWidth($this->getPercentWidth((string)$width));
				}
				$height = $this->getStyle()->getRules('height');
				if ($height !== 'auto') {
					$dimensions->setHeight($this->getPercentHeight((string)$height));
				}
			}
		}
		return $this;
	}

	/**
	 * Split lines into more lines if elements want fit
	 * @return $this
	 */
	public function splitLines()
	{
		if (!$this instanceof LineBox && !$this instanceof InlineBox) {
			// we are block box so we can operate on our children and divide lines into more lines
			foreach ($this->getChildren() as $child) {
				if ($child instanceof LineBox) {
					if (!$child->elementsFit()) {
						$newLines = $child->divide();
						foreach ($newLines as $newLine) {
							// insert new line before old one
							$this->insertBefore($newLine, $child);
						}
						// remove old line
						$this->removeChild($child);
					}
				} else {
					$child->splitLines();
				}
			}
		}
		return $this;
	}


	/**
	 * Get offset from top relative to parent element
	 * @return float
	 */
	protected function getParentOffsetTop()
	{
		if ($parent = $this->getParent()) {
			$top = 0;
			if ($parent instanceof LineBox) {
				if ($this->getStyle()->getRules('display') !== 'inline') {
					$top = $this->getStyle()->getRules('margin-top');
				} else {
					$verticalAlign = $this->getStyle()->getRules('vertical-align');
					if ($verticalAlign === 'middle') {
						$top = $parent->getDimensions()->getHeight() / 2 - $this->getDimensions()->getHeight() / 2;
					} elseif ($verticalAlign === 'top') {
						$top = 0;
					} elseif ($verticalAlign === 'bottom') {
						$top = $parent->getDimensions()->getHeight() - $this->getDimensions()->getHeight();
					} elseif ($verticalAlign === 'baseline') {
						$top = $parent->getDimensions()->getHeight() / 2 - $this->getDimensions()->getHeight() / 2;
					}
				}
			} else {
				if ($previous = $this->getPrevious()) {
					$top = $previous->getOffset()->getTop();
					$top += $previous->getDimensions()->getHeight();
					if (!$previous instanceof LineBox) {
						$top += $previous->getStyle()->getRules('margin-bottom');
					}
				} else {
					$parentRules = $parent->getStyle()->getRules();
					if ($parentRules['display'] !== 'inline') {
						$top += $parentRules['border-top-width'] + $parentRules['padding-top'];
					}
				}
				if (!$this instanceof LineBox) {
					$top += $this->getStyle()->getRules('margin-top');
				}
			}
			return $top;
		}
		return $this->document->getCurrentPage()->getCoordinates()->getY();
	}

	/**
	 * Get offset from left relative to parent element
	 * @return float
	 */
	protected function getParentOffsetLeft()
	{
		if ($parent = $this->getParent()) {
			$left = 0;
			if ($parent instanceof LineBox) {
				if ($previous = $this->getPrevious()) {
					$left += $previous->getOffset()->getLeft();
					$left += $previous->getDimensions()->getWidth();
					$left += $previous->getStyle()->getRules('margin-right');
					$left += $this->getStyle()->getRules('margin-left');
				}
			} else {
				$rules = $parent->getStyle()->getRules();
				$left = $rules['padding-left'] + $rules['border-left-width'];
				if (!$this instanceof LineBox) {
					$left += $this->getStyle()->getRules('margin-left');
				}
			}
			return $left;
		}
		return $this->document->getCurrentPage()->getCoordinates()->getX();
	}

	/**
	 * Measure relative offsets
	 * @return $this
	 */
	public function measureOffsets()
	{
		$offset = $this->getOffset();
		$offset->setTop($this->getParentOffsetTop());
		$offset->setLeft($this->getParentOffsetLeft());
		foreach ($this->getChildren() as $child) {
			$child->measureOffsets();
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
				$offset = $this->getDimensions()->getWidth() - $this->getChildrenWidth();
				foreach ($this->getChildren() as $childBox) {
					$childBox->getOffset()->setLeft($childBox->getOffset()->getLeft() + $offset);
				}
			} elseif ($textAlign === 'center') {
				$offset = $this->getDimensions()->getWidth() / 2 - $this->getChildrenWidth() / 2;
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

	/**
	 * Measure coordinates
	 * @return $this
	 */
	public function measureCoordinates()
	{
		$x = 0;
		$y = 0;
		if ($parent = $this->getParent()) {
			$x = $parent->getCoordinates()->getX();
			$y = $parent->getCoordinates()->getY();
		}
		$coordinates = $this->getCoordinates();
		$offset = $this->getOffset();
		$coordinates->setX($x + $offset->getLeft());
		$coordinates->setY($y + $offset->getTop());
		foreach ($this->getChildren() as $child) {
			$child->measureCoordinates();
		}
		return $this;
	}

}
