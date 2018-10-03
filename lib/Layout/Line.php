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
