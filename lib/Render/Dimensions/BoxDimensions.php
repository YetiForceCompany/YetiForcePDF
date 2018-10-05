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
	 * Get available space inside container
	 * @return float
	 */
	public function getAvailableSpace()
	{
		if ($this->box->getElement()->isRoot()) {
			return $this->document->getCurrentPage()->getPageDimensions()->getInnerWidth();
		}
		$style = $this->box->getStyle();
		$paddingWidth = $style->getRules('padding-left') + $style->getRules('padding-right');
		$borderWidth = $style->getRules('border-left-width') + $style->getRules('border-right-width');
		return $this->box->getParent()->getDimensions()->getAvailableSpace() - $paddingWidth - $borderWidth;
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

	public function calculateWidth()
	{

	}

	public function calculateHeight()
	{

	}

	/**
	 * Calculate block box dimensions
	 * @return $this
	 */
	public function calculate()
	{
		$this->calculateWidth();
		$this->calculateHeight();
		return $this;
	}

}
