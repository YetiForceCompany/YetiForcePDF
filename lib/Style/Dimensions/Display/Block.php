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
		$parentDimensions = $this->document->getCurrentPage()->getPageDimensions();
		$parentBorderWidth = 0;
		$parentPaddingWidth = 0;
		$parent = $this->style->getParent();
		if ($parent) {
			if ($parent->getDimensions() !== null) {
				$parentDimensions = $parent->getDimensions();
			} else {
				//var_dump($element->getText() . ' doesnt have parent dimensions parent:' . $parent->getElement()->getText());
			}
			$parentBorderWidth = $parent->getRules('border-left-width') + $parent->getRules('border-right-width');
			$parentPaddingWidth = $parent->getRules('padding-left') + $parent->getRules('padding-right');
			//var_dump($element->getText() . ': parent (' . $parent->getElement()->getText() . ($parent->getElement()->isTextNode() ? ' [text] ' : ' [html] ') . ') inner width:' . $parentDimensions->getInnerWidth() . ' pborder:' . $parentBorderWidth . ' ppadding:' . $parentPaddingWidth);
		}
		if ($rules['width'] !== 'auto') {
			$this->setWidth($rules['width'] + $rules['border-left-width'] + $rules['border-right-width']);
			$innerWidth = $rules['width'] - $rules['padding-left'] - $rules['padding-right'] - $rules['border-left-width'] - $rules['border-right-width'];
			$this->setInnerWidth($innerWidth);
		} else {
			$borderWidth = $rules['border-left-width'] + $rules['border-right-width'];
			$marginWidth = $rules['margin-left'] + $rules['margin-right'];
			$paddingWidth = $rules['padding-left'] + $rules['padding-right'];
			$this->setWidth($parentDimensions->getInnerWidth() - $marginWidth);
			$innerWidth = $this->getWidth() - $paddingWidth - $borderWidth - $marginWidth;
			$this->setInnerWidth($innerWidth);
		}
		$this->widthCalculated = true;
		//var_dump('w' . $this->getWidth() . ' ' . $element->getText());
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
		//$this->calculate();
		return $this;
	}
}
