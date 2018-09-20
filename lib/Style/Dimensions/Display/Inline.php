<?php
declare(strict_types=1);
/**
 * Inline class
 *
 * @package   YetiForcePDF\Style\Dimensions\Display
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Dimensions\Display;

/**
 * Class Inline
 */
class Inline extends \YetiForcePDF\Style\Dimensions\Element
{
	/**
	 * Calculate text dimensions
	 * @return $this
	 */
	public function calculateTextDimensions()
	{
		return $this;
	}

	/**
	 * Calculate element dimensions
	 * @return $this
	 */
	public function calculateElementDimensions()
	{
		$rules = $this->style->getRules();

		if ($rules['box-sizing'] === 'content-box') {
			if ($rules['width'] !== 'auto') {
				$this->setWidth($rules['width']);
				$innerWidth = $rules['width'] - $rules['padding-left'] - $rules['padding-right'];
				$this->setInnerWidth($innerWidth);
			}
			if ($rules['height'] !== 'auto') {
				$this->setHeight($rules['height']);
				$innerHeight = $rules['height'] - $rules['padding-top'] - $rules['padding-bottom'];
				$this->setInnerHeight($innerHeight);
			}
		} elseif ($rules['box-sizing'] === 'border-box') {
			if ($rules['width'] !== 'auto') {
				$this->setWidth($rules['width'] + $rules['border-left-width'] + $rules['border-right-width']);
				$innerWidth = $rules['width'] - $rules['padding-left'] - $rules['padding-right'] - $rules['border-left-width'] - $rules['border-right-width'];
				$this->setInnerWidth($innerWidth);
			}
			if ($rules['height'] !== 'auto') {
				$this->setHeight($rules['height'] + $rules['border-top-width'] + $rules['border-bottom-width']);
				$innerHeight = $rules['height'] - $rules['padding-top'] - $rules['padding-bottom'] - $rules['border-top-width'] - $rules['border-bottom-width'];
				$this->setInnerWidth($innerHeight);
			}
		}

		return $this;
	}

	/**
	 * Calculate dimensions
	 * @return $this
	 */
	public function calculate()
	{
		if ($this->style->getElement()->isTextNode()) {
			$this->calculateTextDimensions();
		} else {
			$this->calculateElementDimensions();
		}
		return $this;
	}
}
