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
	 * Calculate border-box dimensions
	 * @return $this
	 */
	public function calculateWidth(array $withRules = null)
	{
		if ($this->style->getElement()->isTextNode()) {
			return $this->calculateTextWidth();
		}
		$rules = $this->style->getRules();
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
		if ($withRules !== null) {
			foreach ($this->style->getChildren($withRules) as $child) {
				$child->getDimensions()->calculateWidth($withRules);
			}
		}
		//var_dump('w' . $this->getWidth() . ' ' . $element->getText());
		return $this;
	}

	/**
	 * Calculate border-box dimensions
	 * @return $this
	 */
	public function calculateHeight(array $withRules = null)
	{
		$rules = $this->style->getRules();
		if ($this->style->getElement()->isTextNode()) {
			return $this->calculateTextHeight();
		}
		if ($withRules !== null) {
			foreach ($this->style->getChildren($withRules) as $child) {
				$child->getDimensions()->calculateHeight($withRules);
			}
		}
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
			if ($previousChildrenStyle) {
				$height += $previousChildrenStyle->getRules('margin-bottom');
			}
			$this->setInnerHeight($height + $borderHeight);
			$height += (float)$rules['padding-bottom'] + (float)$rules['padding-top'];
			$this->setHeight($height + $borderHeight);
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
