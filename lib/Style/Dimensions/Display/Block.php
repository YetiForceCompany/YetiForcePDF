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
	 * Do we calculate width already?
	 * @var bool
	 */
	protected $widthCalculated = false;

	/**
	 * Is  width already calculated ?
	 * @return bool
	 */
	public function isWidthCalculated()
	{
		return $this->widthCalculated;
	}

	/**
	 * Calculate text dimensions
	 * @return $this
	 */
	public function calculateTextDimensions()
	{
		$text = $this->style->getElement()->getDOMElement()->textContent;
		$font = $this->style->getFont();
		$width = $font->getTextWidth($text);
		$height = $font->getTextHeight($text);
		$this->setWidth($width);
		$this->setHeight($height);
		$this->setInnerWidth($width);
		$this->setInnerHeight($height);
		return $this;
	}

	/**
	 * Calculate border-box dimensions
	 * @return $this
	 */
	protected function calculateWidth()
	{
		$rules = $this->style->getRules();
		$element = $this->style->getElement();
		if ($element->isRoot()) {
			return $this;
		}
		$parentDimensions = $this->document->getCurrentPage()->getPageDimensions();
		$parent = $this->style->getParent();
		if ($parent && $parent->getDimensions()) {
			$parentDimensions = $parent->getDimensions();
		}
		if ($rules['width'] !== 'auto') {
			$this->setWidth($rules['width'] + $rules['border-left-width'] + $rules['border-right-width']);
			$innerWidth = $rules['width'] - $rules['padding-left'] - $rules['padding-right'] - $rules['border-left-width'] - $rules['border-right-width'];
			$this->setInnerWidth($innerWidth);
		} else {
			$borderWidth = $rules['border-left-width'] + $rules['border-right-width'];
			$marginWidth = $rules['margin-left'] + $rules['margin-right'];
			$paddingWidth = $rules['padding-left'] + $rules['padding-right'];
			$parentBorderWidth = $parent->getRules('border-left-width') + $parent->getRules('border-right-width');
			$this->setWidth($parentDimensions->getInnerWidth() - $parentBorderWidth - $marginWidth);
			$innerWidth = $parentDimensions->getInnerWidth() - $paddingWidth - $borderWidth - $marginWidth;
			$this->setInnerWidth($innerWidth);
		}
		$this->widthCalculated = true;
		return $this;
	}

	/**
	 * Calculate border-box dimensions
	 * @return $this
	 */
	protected function calculateHeight()
	{
		$rules = $this->style->getRules();
		$element = $this->style->getElement();
		if ($element->isRoot()) {
			return $this;
		}

		if ($rules['height'] !== 'auto') {
			$this->setHeight($rules['height'] + $rules['border-top-width'] + $rules['border-bottom-width']);
			$innerHeight = $rules['height'] - $rules['padding-top'] - $rules['padding-bottom'] - $rules['border-top-width'] - $rules['border-bottom-width'];
			$this->setInnerWidth($innerHeight);
		} else {
			$borderHeight = $rules['border-top-width'] + $rules['border-bottom-width'];
			$height = 0;
			$maxInlineHeight = 0;
			foreach ($this->style->getChildren() as $index => $childStyle) {
				$childDisplay = $childStyle->getRules('display');
				$childDimensions = $childStyle->getDimensions();
				if ($childDisplay === 'block') {
					$height += $childDimensions->getHeight();
					// TODO add margins between inner elements
				} else {
					$maxInlineHeight = max($childDimensions->getHeight(), $maxInlineHeight);
				}
			}
			$this->setInnerHeight($height + $borderHeight);
			$height += (float)$rules['padding-bottom'] + (float)$rules['padding-top'];
			$this->setHeight($height + $borderHeight);
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
			if ($this->widthCalculated) {
				$this->calculateHeight();
			} else {
				$this->calculateWidth();
			}
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
