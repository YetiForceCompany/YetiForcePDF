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
	 * Calculate content-box dimensions
	 * @return $this;
	 */
	protected function calculateContentBox()
	{
		$rules = $this->style->getRules();
		$element = $this->style->getElement();
		$parentDimensions = $this->document->getCurrentPage()->getPageDimensions();
		if ($rules['width'] !== 'auto') {
			$this->setWidth($rules['width']);
			$innerWidth = $rules['width'] - $rules['padding-left'] - $rules['padding-right'];
			$this->setInnerWidth($innerWidth);
		} else {
			$this->setWidth($parentDimensions->getInnerWidth() - $rules['margin-left'] - $rules['margin-right']);
			$innerWidth = $parentDimensions->getInnerWidth() - $rules['padding-left'] - $rules['padding-right'] - $rules['margin-left'] - $rules['margin-right'];
			$this->setInnerWidth($innerWidth);
		}

		if ($rules['height'] !== 'auto') {
			$this->setHeight($rules['height']);
			$innerHeight = $rules['height'] - $rules['padding-top'] - $rules['padding-bottom'];
			$this->setInnerHeight($innerHeight);
		} else {
			$height = 0;
			foreach ($element->getChildren() as $child) {
				$childDimensions = $child->getStyle()->getDimensions();
				$height = max($childDimensions->getHeight(), $height);
			}
			$this->setInnerHeight($height);
			$height += $rules['padding-bottom'] + $rules['padding-top'];
			$this->setHeight($height);
		}
		return $this;
	}

	/**
	 * Calculate border-box dimensions
	 * @return $this
	 */
	protected function calculateBorderBox()
	{
		$rules = $this->style->getRules();
		$element = $this->style->getElement();
		if ($element->isRoot()) {
			return $this;
		}
		$pageDimensions = $this->document->getCurrentPage()->getPageDimensions();
		// TODO set up parent dimensions - not page - because we could be inside other element
		if ($rules['width'] !== 'auto') {
			$this->setWidth($rules['width'] + $rules['border-left-width'] + $rules['border-right-width']);
			$innerWidth = $rules['width'] - $rules['padding-left'] - $rules['padding-right'] - $rules['border-left-width'] - $rules['border-right-width'];
			$this->setInnerWidth($innerWidth);
		} else {
			$this->setWidth($pageDimensions->getInnerWidth() - $rules['margin-left'] - $rules['margin-right']);
			$innerWidth = $pageDimensions->getInnerWidth() - $rules['padding-left'] - $rules['padding-right'] - $rules['border-left-width'] - $rules['border-right-width'] - $rules['margin-left'] - $rules['margin-right'];
			$this->setInnerWidth($innerWidth);
		}

		if ($rules['height'] !== 'auto') {
			$this->setHeight($rules['height'] + $rules['border-top-width'] + $rules['border-bottom-width']);
			$innerHeight = $rules['height'] - $rules['padding-top'] - $rules['padding-bottom'] - $rules['border-top-width'] - $rules['border-bottom-width'];
			$this->setInnerWidth($innerHeight);
		} else {
			$height = 0;
			$maxInlineHeight = 0;
			foreach ($element->getChildren() as $index => $child) {
				$childStyle = $child->getStyle();
				$childDisplay = $childStyle->getRules('display');
				$childPreviousStyle = $childStyle->getPrevious();
				if ($childPreviousStyle) {
					$childPreviousDisplay = $childPreviousStyle->getRules('display');
				} else {
					$childPreviousDisplay = 'block';
				}
				$childDimensions = $child->getStyle()->getDimensions();
				if ($childDisplay === 'block' || $childPreviousDisplay === 'block') {
					$height += $childDimensions->getHeight();
				} else {
					$maxInlineHeight = max($childDimensions->getHeight(), $maxInlineHeight);
				}
				//var_dump($index . ' : ' . $childDimensions->getHeight() . ' : ' . $height . ' : ' . $child->getDOMElement()->textContent);
			}
			$this->setInnerHeight($height);
			$height += $rules['padding-bottom'] + $rules['padding-top'];
			$this->setHeight($height);
		}
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
