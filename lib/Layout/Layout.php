<?php
declare(strict_types=1);
/**
 * Layout class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Style\Style;

/**
 * Class Layout
 */
class Layout extends \YetiForcePDF\Base
{
	/**
	 * @var Line[]
	 */
	protected $lines = [];

	/**
	 * @var Style
	 */
	protected $style;

	/**
	 * Get lines
	 * @return \YetiForcePDF\Layout\Line[]
	 */
	public function getLines()
	{
		return $this->lines;
	}

	/**
	 * Append line
	 * @param \YetiForcePDF\Layout\Line $line
	 * @return $this
	 */
	public function appendLine(Line $line)
	{
		$this->lines[] = $line;
		return $this;
	}

	/**
	 * Set style
	 * @param \YetiForcePDF\Style\Style $style
	 * @return $this
	 */
	public function setStyle(Style $style)
	{
		$this->style = $style;
		return $this;
	}

	/**
	 * Get inner width
	 * @return int
	 */
	public function getInnerWidth()
	{
		$width = 0;
		foreach ($this->getLines() as $line) {
			$width = max($width, $line->getInnerWidth());
		}
		return $width;
	}

	/**
	 * Arrange elements inside lines
	 * @return $this
	 */
	public function reflow()
	{
		$currentChildren = [];
		$currentWidth = 0;
		foreach ($this->style->getChildren() as $child) {
			$availableSpace = $this->style->getDimensions()->getAvailableSpace();
			$childWidth = $child->getDimensions()->getWidth() + $child->getRules('margin-left') + $child->getRules('margin-right');
			$break = $currentWidth + $childWidth > $availableSpace;
			if ($child->getRules('display') === 'block' || $break) {
				$this->appendLine((new Line())->setDocument($this->document)->setStyles($currentChildren)->init());
				$currentChildren = [$child];
				$currentWidth = 0;
			} else {
				// we can add one more element to current line
				$currentChildren[] = $child;
				$currentWidth += $childWidth;
			}
			$child->getLayout()->reflow();
		}
		// finish lines because there is no more children
		$this->appendLine((new Line())->setDocument($this->document)->setStyles($currentChildren)->init());
		return $this;
	}
}
