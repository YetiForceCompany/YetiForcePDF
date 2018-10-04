<?php
declare(strict_types=1);
/**
 * Line class
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
 * Class Line
 */
class Line extends \YetiForcePDF\Base
{
	/**
	 * @var Style[]
	 */
	protected $styles = [];

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
	 * Is this line has just one element with display:block ?
	 * @return bool
	 */
	public function isOneBlock()
	{
		return count($this->styles) === 1 && $this->styles[0]->getRules('display') === 'block';
	}

	/**
	 * Get inner width (children sum width with margins)
	 * @return float
	 */
	public function getInnerWidth()
	{
		if ($this->isOneBlock()) {
			return $this->styles[0]->getDimensions()->getAvailableSpace();
		}
		$width = 0;
		foreach ($this->styles as $style) {
			$style->getDimensions()->calculateWidth();
			$width += $style->getDimensions()->getWidth() + $style->getRules('margin-left') + $style->getRules('margin-right');
		}
		return $width;
	}

	public function getInnerHeight()
	{
		if ($this->isOneBlock()) {
			return $this->styles[0]->getDimensions()->getHeight();
		}
		$height = 0;
		foreach ($this->styles as $style) {
			$height += $style->getDimensions()->getHeight();
		}
		return $height;
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
