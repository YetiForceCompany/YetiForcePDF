<?php
declare(strict_types=1);
/**
 * LineBox class
 *
 * @package   YetiForcePDF\Render
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render;

use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;


/**
 * Class LineBox
 */
class Box extends \YetiForcePDF\Base
{

	/**
	 * @var Box[]
	 */
	protected $boxes = [];
	/**
	 * @var Element
	 */
	protected $element;
	/**
	 * @var Style
	 */
	protected $style;
	/**
	 * @var float
	 */
	protected $childrenWidth = 0;
	/**
	 * @var float
	 */
	protected $childrenHeight = 0;
	/*
	 * @var
	 */
	protected $dimensions;
	protected $coordinates;


	/**
	 * Set children width
	 * @param float $width
	 * @return $this
	 */
	public function setChildrenWidth(float $width)
	{
		$this->childrenWidth = $width;
		return $this;
	}

	/**
	 * Set children height
	 * @param float $height
	 * @return $this
	 */
	public function setChildrenHeight(float $height)
	{
		$this->childrenHeight = $height;
		return $this;
	}

	/**
	 * Get height
	 * @return float
	 */
	public function getHeight()
	{
		return $this->childrenHeight;
	}


	/**
	 * Get style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getStyle()
	{
		return $this->style;
	}

	/**
	 * Set style
	 * @param \YetiForcePDF\Style\Style $style
	 * @return $this
	 */
	public function setStyle(Style $style)
	{
		$this->style = $style;
		$this->element = $style->getElement();
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
		$this->style = $element->getStyle();
		return $this;
	}

	/**
	 * Append box
	 * @param \YetiForcePDF\Render\Box $box
	 * @return $this
	 */
	public function appendBox(Box $box)
	{
		$this->boxes[] = $box;
		return $this;
	}

	/**
	 * Get dimensions
	 * @return \YetiForcePDF\Render\Dimensions\Element
	 */
	public function getDimensions()
	{
		return $this->dimensions;
	}

	/**
	 * Get coordinates
	 * @return \YetiForcePDF\Render\Coordinates\Coordinates
	 */
	public function getCoordinates(): \YetiForcePDF\Render\Coordinates\Coordinates
	{
		return $this->coordinates;
	}

	/**
	 * Shorthand for offset
	 * @return \YetiForcePDF\Render\Coordinates\Offset
	 */
	public function getOffset(): \YetiForcePDF\Render\Coordinates\Offset
	{
		return $this->getCoordinates()->getOffset();
	}

}
