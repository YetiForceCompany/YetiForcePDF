<?php

declare(strict_types=1);
/**
 * ImageBox class.
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License v3
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use YetiForcePDF\Math;

/**
 * Class ImageBox.
 */
class ImageBox extends InlineBlockBox
{
	/**
	 * Measure width.
	 *
	 * @return $this
	 */
	public function measureWidth(bool $afterPageDividing = false)
	{
		$style = $this->getStyle();
		if ($style->getRules('width') === 'auto') {
			$img = $style->getBackgroundImageStream();
			$width = $img->getWidth();
			if (Math::comp($width, $this->getParent()->getDimensions()->computeAvailableSpace()) > 0) {
				$width = $this->getParent()->getDimensions()->computeAvailableSpace();
			}
			if ($style->getRules('height') !== 'auto') {
				Math::setAccurate(true);
				$ratio = $img->getRatio();
				$width = Math::mul($ratio, $this->getDimensions()->getStyleHeight());
				Math::setAccurate(false);
			}
			$width = Math::add($width, $style->getHorizontalBordersWidth(), $style->getHorizontalPaddingsWidth());
			$this->getDimensions()->setWidth($width);
		} else {
			$this->applyStyleWidth();
		}
		return $this;
	}

	/**
	 * Measure height.
	 *
	 * @param bool $afterPageDividing
	 *
	 * @return $this
	 */
	public function measureHeight(bool $afterPageDividing = false)
	{
		$style = $this->getStyle();
		$img = $style->getBackgroundImageStream();
		Math::setAccurate(true);
		$ratio = $img->getRatio();
		$height = Math::div($this->getDimensions()->getInnerWidth(), $ratio);
		Math::setAccurate(false);
		$height = Math::add($height, $style->getVerticalBordersWidth(), $style->getVerticalPaddingsWidth());
		$this->getDimensions()->setHeight($height);
		return $this;
	}
}
