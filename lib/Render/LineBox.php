<?php
declare(strict_types=0);
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

/**
 * Class LineBox
 */
class LineBox extends Box
{

	/**
	 * Append child box - line box can have only inline boxes - not line boxes!
	 * @param InlineBox $box
	 * @return $this
	 */
	public function appendChild(InlineBox $box)
	{
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
	 * Will this box fit in line? (or need to create new one)
	 * @param \YetiForcePDF\Render\InlineBox $box
	 * @return bool
	 */
	public function willFit(InlineBox $box)
	{
		$availableSpace = $this->getDimensions()->getWidth() - $this->getChildrenWidth();
		return $availableSpace >= $box->getDimensions()->getOuterWidth();
	}

	/**
	 * Get children width
	 * @return float|int
	 */
	public function getChildrenWidth()
	{
		$width = 0;
		foreach ($this->getChildren() as $childBox) {
			$width += $childBox->getDimensions()->getOuterWidth();
		}
		return $width;
	}

	/**
	 * Reflow elements and create render tree basing on dom tree
	 * @return $this
	 */
	public function reflow()
	{
		foreach ($this->getChildren() as $childBox) {
			$childBox->reflow();
		}

		return $this;
	}
}
