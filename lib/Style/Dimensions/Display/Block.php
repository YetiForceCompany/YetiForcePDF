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
		$parent = $this->style->getParent();
		if ($parent) {
			if ($parent->getDimensions() !== null) {
				$parentDimensions = $parent->getDimensions();
			} else {
				//var_dump($element->getText() . ' doesnt have parent dimensions parent:' . $parent->getElement()->getText());
			}
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
			$innerWidth = $this->getWidth() - $paddingWidth - $borderWidth;
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
			$currentInlineHeight = 0;
			$inlineHeight = 0;
			$previousChildrenStyle = null;
			$children = $this->style->getChildren();
			foreach ($children as $index => $childStyle) {
				$childRules = $childStyle->getRules();
				$childDimensions = $childStyle->getDimensions();
				if ($childRules['display'] === 'block') {
					$height += $childDimensions->getHeight();
					$marginTop = $childRules['margin-top'];
					if ($previousChildrenStyle) {
						$marginTop = max($marginTop, $previousChildrenStyle->getRules['margin-bottom']);
					}
					$inlineHeight += $currentInlineHeight;
					$currentInlineHeight = 0;
					$height += $marginTop;
				} else {
					$marginTop = $childRules['margin-top'];
					if ($previousChildrenStyle) {
						$marginTop = max($marginTop, $previousChildrenStyle->getRules['margin-bottom']);
					}
					$currentInlineHeight = max($childDimensions->getHeight(), $currentInlineHeight);
					$height += $marginTop;
				}
				$previousChildrenStyle = $childStyle;
			}
			$height += $currentInlineHeight + $inlineHeight;
			$height += $previousChildrenStyle->getRules('margin-bottom');
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
