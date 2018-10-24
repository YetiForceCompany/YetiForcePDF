<?php
declare(strict_types=1);
/**
 * BoxDimensions class
 *
 * @package   YetiForcePDF\Layout\Dimensions
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout\Dimensions;

use YetiForcePDF\Layout\Box;
use \YetiForcePDF\Layout\LineBox;

/**
 * Class BoxDimensions
 */
class BoxDimensions extends Dimensions
{

	/**
	 * @var Box
	 */
	protected $box;

	/**
	 * Set box
	 * @param \YetiForcePDF\Layout\Box $box
	 * @return $this
	 */
	public function setBox(Box $box)
	{
		$this->box = $box;
		return $this;
	}

	/**
	 * Get box
	 * @return \YetiForcePDF\Layout\Box
	 */
	public function getBox()
	{
		return $this->box;
	}

	/**
	 * Get innerWidth
	 * @return float
	 */
	public function getInnerWidth(): float
	{
		$box = $this->getBox();
		$style = $box->getStyle();
		return $this->getWidth() - $style->getHorizontalBordersWidth() - $style->getHorizontalPaddingsWidth();
	}

	/**
	 * Get innerHeight
	 * @return float
	 */
	public function getInnerHeight(): float
	{
		$box = $this->getBox();
		$style = $box->getStyle();
		return $this->getHeight() - $style->getVerticalBordersWidth() - $style->getVerticalPaddingsWidth();
	}


	/**
	 * Get width with margins
	 * @return float
	 */
	public function getOuterWidth()
	{
		if (!$this->getBox() instanceof LineBox) {
			$rules = $this->getBox()->getStyle()->getRules();
			$childrenWidth = 0;
			// if some of the children overflows
			foreach ($this->getBox()->getChildren() as $child) {
				$childrenWidth += $child->getDimensions()->getOuterWidth();
			}
			return max($this->getWidth() + $rules['margin-left'] + $rules['margin-right'], $childrenWidth);
		} else {
			return $this->getBox()->getChildrenWidth();
		}
	}

	/**
	 * Get height with margins
	 * @return float
	 */
	public function getOuterHeight()
	{
		$rules = $this->getBox()->getStyle()->getRules();
		return $this->getHeight() + $rules['margin-top'] + $rules['margin-bottom'];
	}

	/**
	 * Get text width
	 * @param string $text
	 * @return float
	 */
	public function getTextWidth($text)
	{
		$font = $this->box->getStyle()->getFont();
		return $font->getTextWidth($text);
	}

	/**
	 * Get text height
	 * @param string $text
	 * @return float
	 */
	public function getTextHeight($text)
	{
		$font = $this->box->getStyle()->getFont();
		return $font->getTextHeight($text);
	}

	/**
	 * Compute available space (basing on parent available space and parent border and padding)
	 * @return float
	 */
	public function computeAvailableSpace()
	{
		if ($parent = $this->getBox()->getParent()) {
			$parentStyle = $parent->getStyle();
			if ($parent->getDimensions()->getWidth() === null) {
				return $this->getBox()->getParent()->getDimensions()->computeAvailableSpace() - $parentStyle->getHorizontalBordersWidth() - $parentStyle->getHorizontalPaddingsWidth();
			} else {
				return $this->getBox()->getParent()->getDimensions()->getInnerWidth();
			}
		} else {
			return $this->document->getCurrentPage()->getDimensions()->getWidth();
		}
	}

}
