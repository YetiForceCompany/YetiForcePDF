<?php
declare(strict_types=1);
/**
 * Block class
 *
 * @package   YetiForcePDF\Style\Dimensions\Display
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Dimensions\Display;

/**
 * Class Block
 */
class Block extends \YetiForcePDF\Style\Dimensions\Element
{

	/**
	 * Calculate text dimensions
	 * @return $this
	 */
	public function calculateTextDimensions()
	{
		// FIXME for now we are using parent dimensions but when we know font height we will calculate it, we should start from bottom to up with dimensions
		$parentDimensions = $this->style->getParent()->getDimensions();
		$this->setWidth($parentDimensions->getWidth());
		$this->setHeight($parentDimensions->getHeight());
		return $this;
	}

	/**
	 * Calculate content-box dimensions
	 */
	protected function calculateContentBox()
	{
		$rules = $this->style->getRules();
		$pageDimensions = $this->document->getCurrentPage()->getPageDimensions();
		if ($rules['width'] !== 'auto') {
			$this->setWidth($rules['width']);
			$innerWidth = $rules['width'] - $rules['padding-left'] - $rules['padding-right'];
			$this->setInnerWidth($innerWidth);
		} else {
			$this->setWidth($pageDimensions->getInnerWidth());
			$innerWidth = $pageDimensions->getInnerWidth() - $rules['padding-left'] - $rules['padding-right'];
			$this->setInnerWidth($innerWidth);
		}

		if ($rules['height'] !== 'auto') {
			$this->setHeight($rules['height']);
			$innerHeight = $rules['height'] - $rules['padding-top'] - $rules['padding-bottom'];
			$this->setInnerHeight($innerHeight);
		} else {
			// TODO get max children height
		}
	}

	/**
	 * Calculate border-box dimensions
	 */
	protected function calculateBorderBox()
	{
		$rules = $this->style->getRules();
		$pageDimensions = $this->document->getCurrentPage()->getPageDimensions();
		if ($rules['width'] !== 'auto') {
			$this->setWidth($rules['width'] + $rules['border-left-width'] + $rules['border-right-width']);
			$innerWidth = $rules['width'] - $rules['padding-left'] - $rules['padding-right'] - $rules['border-left-width'] - $rules['border-right-width'];
			$this->setInnerWidth($innerWidth);
		} else {
			$this->setWidth($pageDimensions->getInnerWidth());
			$innerWidth = $pageDimensions->getInnerWidth() - $rules['padding-left'] - $rules['padding-right'] - $rules['border-left-width'] - $rules['border-right-width'];
			$this->setInnerWidth($innerWidth);
		}

		if ($rules['height'] !== 'auto') {
			$this->setHeight($rules['height'] + $rules['border-top-width'] + $rules['border-bottom-width']);
			$innerHeight = $rules['height'] - $rules['padding-top'] - $rules['padding-bottom'] - $rules['border-top-width'] - $rules['border-bottom-width'];
			$this->setInnerWidth($innerHeight);
		} else {
			// TODO get max children height
		}
	}

	/**
	 * Calculate element dimensions
	 * @return $this
	 */
	public function calculateElementDimensions()
	{
		$rules = $this->style->getRules();
		if ($rules['box-sizing'] === 'content-box') {
			$this->calculateContentBox();
		} elseif ($rules['box-sizing'] === 'border-box') {
			$this->calculateBorderBox();
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

	/**
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		parent::init();
		$this->calculate();
		return $this;
	}
}
