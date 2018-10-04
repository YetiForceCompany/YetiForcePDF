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
	 * @param \YetiForcePDF\Render\Box $parent
	 * @return $this
	 */
	public function setParent(Box $parent)
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
	 * @param \YetiForcePDF\Render\Box $next
	 * @return $this
	 */
	public function setNext(Box $next)
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
	 * @param \YetiForcePDF\Render\Box $previous
	 * @return $this
	 */
	public function setPrevious(Box $previous)
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

}
