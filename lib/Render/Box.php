<?php
declare(strict_types=0);
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
use \YetiForcePDF\Render\Dimensions\Dimensions;
use \YetiForcePDF\Render\Dimensions\BoxDimensions;


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
	 * {@inheritdoc}
	 */
	public function init()
	{
		parent::init();
		$this->dimensions = (new Dimensions())
			->setDocument($this->document)
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
	 * Append child box - line box can have only inline/block boxes - not line boxes!
	 * @param Box $box
	 * @return $this
	 */
	public function appendChild(Box $box)
	{
		if ($this instanceof LineBox && $box instanceof LineBox) {
			throw new \InvalidArgumentException('LineBox cannot append another LineBox child.');
		}
		$box->setParent($this);
		$childrenCount = count($this->children);
		if ($childrenCount > 0) {
			$previous = $this->children[$childrenCount - 1];
			$box->setPrevious($previous);
			$previous->setNext($box);
		}
		$this->children[] = $box;
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
		$this->children = array_filter($this->children, function ($currentChild) use ($child) {
			return $currentChild !== $child;
		});
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
	 * @param array $allChildren
	 * @return Box[]
	 */
	public function getAllChildren($allChildren = [])
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
	 * Measure all children widths and some heights if we can
	 * @return $this
	 */
	public function measureBoxPhaseOne()
	{
		// first measure current element if we can
		$dimensions = $this->getDimensions();
		if ($this->getElement()->isTextNode()) {
			$dimensions->setWidth($dimensions->getTextWidth($this->getElement()->getText()));
			$dimensions->setHeight($dimensions->getTextHeight($this->getElement()->getText()));
		} elseif (!$this->getElement()->hasChildren() && $this->getStyle()->getRules('display') !== 'block') {
			// empty inline element so we can measure it :  0 + border + padding
			$style = $this->getStyle();
			$dimensions->setWidth($style->getHorizontalBordersWidth() + $style->getHorizontalPaddingsWidth());
			$dimensions->setHeight($style->getVerticalBordersWidth() + $style->getVerticalPaddingsWidth());
		} elseif ($this->getStyle()->getRules('display') === 'block') {
			if ($parent = $this->getParent()) {
				// it is block element so we can take width from parent - border - padding
				$dimensions->setWidth($parent->getDimensions()->getInnerWidth());
			} else {
				// if there is no parent - root element get width from page width - margins
				$dimensions->setWidth($this->document->getCurrentPage()->getPageDimensions()->getWidth());
			}
			// we can't measure height right now because it depends on child elements heights
		} else {
			// now we can measure children widths and some heights
			foreach ($this->getChildren() as $boxChildren) {
				$boxChildren->measurePhaseOne();
			}
		}
		return $this;
	}

	/**
	 * Measure phase one
	 * @return $this
	 */
	public function measurePhaseOne()
	{
		if ($this instanceof LineBox) {
			// line take all available space inside block - parent is always block
			$dimensions = $this->getDimensions();
			$parent = $this->getParent();
			$dimensions->setWidth($parent->getDimensions()->getInnerWidth());
			foreach ($this->getChildren() as $boxChild) {
				$boxChild->measureBoxPhaseOne();
			}
		} else {
			$this->measureBoxPhaseOne();
		}
		return $this;
	}

	/**
	 * Get new line box
	 * @return \YetiForcePDF\Render\LineBox
	 */
	public function getNewLineBox()
	{
		$newLineBox = (new LineBox())->setDocument($this->document)->init();
		$newLineBox->getDimensions()->setWidth($this->getDimensions()->getInnerWidth());
		return $newLineBox;
	}

	/**
	 * Close line box
	 * @param \YetiForcePDF\Render\LineBox|null $lineBox
	 * @param bool                              $createNew
	 * @return \YetiForcePDF\Render\LineBox
	 */
	protected function closeLine(LineBox $lineBox, bool $createNew = true)
	{
		$this->appendChild($lineBox);
		if ($createNew) {
			return $this->getNewLineBox();
		}
		return null;
	}

	/**
	 * Segregate elements
	 * @return $this
	 */
	public function segregateBox()
	{
		$lineBox = null;
		foreach ($this->getElement()->getChildren() as $childElement) {
			// make render box from the dom element
			if ($childElement->getStyle()->getRules('display') === 'block') {
				if ($lineBox !== null) { // faster than count()
					// finish line and add to current children boxes as line box
					$lineBox = $this->closeLine($lineBox, false);
				}
				$box = (new BlockBox())
					->setDocument($this->document)
					->setElement($childElement)
					->init();
				$this->appendChild($box);
				$box->reflow();
				continue;
			}
			// inline boxes
			$box = (new InlineBox())
				->setDocument($this->document)
				->setElement($childElement)
				->init();
			$lineBox = $this->getNewLineBox();
			$lineBox->appendChild($box);
			$box->reflow();
		}
		if ($lineBox !== null) {
			$this->closeLine($lineBox, false);
		}
		return $this;
	}

	/**
	 * Segregate boxes in lines
	 * @return $this
	 */
	public function segregateLine()
	{
		foreach ($this->getChildren() as $boxChildren) {
			$boxChildren->segregateBox();
		}
		return $this;
	}

	/**
	 * Segregate elements
	 * @return $this
	 */
	public function segregate()
	{
		if ($this instanceof LineBox) {
			$this->segregateLine();
		} else {
			$this->segregateBox();
		}
		return $this;
	}

	/**
	 * Split lines into more lines if elements want fit
	 * @return $this
	 */
	public function splitLines()
	{
		if (!$this instanceof LineBox) {
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
	 * Reflow elements and create render tree basing on dom tree
	 * @return $this
	 */
	public function reflow()
	{
		// first phase is to convert all elements to boxes and put it inside line boxes if needed
		// later if we have widths specified we will split elements in line boxes making dividing line
		$this->segregate();
		// we have all boxes created and segregated, now we can measure widths of this elements
		$this->measurePhaseOne();
		// all boxes that can be measured were measured whoa!
		// now we can split lineBoxes into more lines basing on its width and children widths
		$this->splitLines();
		return $this;
	}

}
