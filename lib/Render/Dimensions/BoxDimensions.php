<?php
declare(strict_types=1);
/**
 * BoxDimensions class
 *
 * @package   YetiForcePDF\Render\Dimensions
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render\Dimensions;

use YetiForcePDF\Render\Box;

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
	 * @param \YetiForcePDF\Render\Box $box
	 * @return $this
	 */
	public function setBox(Box $box)
	{
		$this->box = $box;
		return $this;
	}

	/**
	 * Get box
	 * @return \YetiForcePDF\Render\Box
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
		if (!$box instanceof \YetiForcePDF\Render\LineBox) {
			$style = $box->getStyle();
			return $this->getWidth() - $style->getHorizontalBordersWidth() - $style->getHorizontalPaddingsWidth();
		}
		return $this->getWidth();
	}

	/**
	 * Get innerHeight
	 * @return float
	 */
	public function getInnerHeight(): float
	{
		$box = $this->getBox();
		if (!$box instanceof \YetiForcePDF\Render\LineBox) {
			$style = $box->getStyle();
			return $this->getHeight() - $style->getVerticalBordersWidth() - $style->getVerticalPaddingsWidth();
		}
		return $this->getWidth();
	}


	/**
	 * Get width with margins
	 * @return float
	 */
	public function getOuterWidth()
	{
		$box = $this->getBox();
		if (!$box instanceof \YetiForcePDF\Render\LineBox) {
			$rules = $this->getBox()->getStyle()->getRules();
			return $this->getWidth() + $rules['margin-left'] + $rules['margin-right'];
		}
		return $this->getWidth();
	}

	/**
	 * Get height with margins
	 * @return float
	 */
	public function getOuterHeight()
	{
		$box = $this->getBox();
		if (!$box instanceof \YetiForcePDF\Render\LineBox) {
			$rules = $this->getBox()->getStyle()->getRules();
			return $this->getHeight() + $rules['margin-top'] + $rules['margin-bottom'];
		}
		return $this->getHeight();
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
			if (!$parent instanceof \YetiForcePDF\Render\LineBox) {
				$parentStyle = $parent->getStyle();
				return $this->getBox()->getParent()->getDimensions()->getAvailableSpace() - $parentStyle->getHorizontalBordersWidth() - $parentStyle->getHorizontalPaddingsWidth();
			} else {
				return $this->getBox()->getParent()->getDimensions()->getAvailableSpace();
			}
		} else {
			return $this->document->getCurrentPage()->getDimensions()->getAvailableSpace();
		}
	}

	/**
	 * Set up available space
	 * @return $this
	 */
	public function setUpAvailableSpace()
	{
		$this->setAvailableSpace($this->computeAvailableSpace());
		return $this;
	}

}
