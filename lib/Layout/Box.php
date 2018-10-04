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

use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;

/**
 * Class LineBox
 */
class Box extends \YetiForcePDF\Base
{

	/**
	 * @var Style[]
	 */
	protected $styles = [];
	/**
	 * @var float
	 */
	protected $childrenWidth = 0;
	/**
	 * @var float
	 */
	protected $childrenHeight = 0;
	/**
	 * @var float
	 */
	protected $left = 0;
	/**
	 * @var float
	 */
	protected $top = 0;

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
	 * Set left position
	 * @param float $left
	 * @return $this
	 */
	public function setLeftPosition(float $left)
	{
		$this->left = $left;
		return $this;
	}

	/**
	 * Set top position
	 * @param float $top
	 * @return $this
	 */
	public function setTopPosition(float $top)
	{
		$this->top = $top;
		return $this;
	}

	/**
	 * Set styles
	 * @param array $styles
	 * @return $this
	 */
	public function setStyles(array $styles)
	{
		$this->styles = $styles;
		return $this;
	}

	/**
	 * Get styles
	 * @return \YetiForcePDF\Style\Style[]
	 */
	public function getStyles()
	{
		return $this->styles;
	}

	/**
	 * Get elements
	 * @return Element[]
	 */
	public function getElements()
	{
		$elements = [];
		foreach ($this->styles as $style) {
			$elements[] = $style->getElement();
		}
		return $elements;
	}

	/**
	 * Set elements
	 * @param Element[] $elements
	 * @return $this
	 */
	public function setElements(array $elements)
	{
		$this->styles = [];
		foreach ($elements as $element) {
			$this->styles[] = $element->getStyle();
		}
		return $this;
	}

}
