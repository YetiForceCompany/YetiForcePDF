<?php
declare(strict_types=1);
/**
 * Element class
 *
 * @package   YetiForcePDF\Style\Dimensions
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Dimensions;

/**
 * Class Element
 */
class Element extends Dimensions
{

	/**
	 * @var \YetiForcePDF\Style\Style
	 */
	protected $style;

	/**
	 * @var float
	 */
	protected $innerWidth = 0;
	/**
	 * @var float
	 */
	protected $innerHeight = 0;

	/**
	 * Set style
	 * @param \YetiForcePDF\Style\Style $style
	 * @return $this
	 */
	public function setStyle(\YetiForcePDF\Style\Style $style)
	{
		$this->style = $style;
		return $this;
	}

	/**
	 * Get innerWidth
	 * @return float
	 */
	public function getInnerWidth(): float
	{
		return $this->innerWidth;
	}

	/**
	 * Set innerWidth
	 * @param float $innerWidth
	 * @return $this
	 */
	public function setInnerWidth(float $innerWidth)
	{
		$this->innerWidth = $innerWidth;
		return $this;
	}

	/**
	 * Get innerHeight
	 * @return float
	 */
	public function getInnerHeight(): float
	{
		return $this->innerHeight;
	}

	/**
	 * Set innerHeight
	 * @param float $height
	 * @return $this
	 */
	public function setInnerHeight(float $innerHeight)
	{
		$this->innerHeight = $innerHeight;
		return $this;
	}

	/**
	 * Calculate text dimensions
	 * @return $this
	 */
	public function calculateTextWidth()
	{
		$text = $this->style->getElement()->getDOMElement()->textContent;
		$font = $this->style->getFont();
		$width = $font->getTextWidth($text);
		$this->setWidth($width);
		$this->setInnerWidth($width);
		//var_dump('w' . $this->getWidth() . ' ' . $this->style->getElement()->getText() . ' ' . $this->style->getRules('display') . ' ' . ($this->style->getElement()->isTextNode() ? 'text' : 'html'));
		return $this;
	}

	/**
	 * Calculate text dimensions
	 * @return $this
	 */
	public function calculateTextHeight()
	{
		$text = $this->style->getElement()->getDOMElement()->textContent;
		$font = $this->style->getFont();
		$height = $font->getTextHeight($text);
		$this->setHeight($height);
		$this->setInnerHeight($height);
		//var_dump('h' . $this->getHeight() . ' ' . $this->style->getElement()->getText() . ' ' . $this->style->getRules('display') . ' ' . ($this->style->getElement()->isTextNode() ? 'text' : 'html'));
		return $this;
	}


	/**
	 * Calculate border-box dimensions
	 * @return $this
	 */
	public function calculateWidth()
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
			if ($rules['display'] === 'block') {
				$marginWidth = $rules['margin-left'] + $rules['margin-right'];
				$paddingWidth = $rules['padding-left'] + $rules['padding-right'];
				$this->setWidth($parentDimensions->getInnerWidth() - $marginWidth);
				$innerWidth = $this->getWidth() - $paddingWidth - $borderWidth;
				$this->setInnerWidth($innerWidth);
			} else {
				$width = 0;
				foreach ($this->style->getChildren() as $child) {
					if ($child->getRules('display') === 'inline') {
						$width += $child->getDimensions()->getWidth();
					} else {
						$width = $child->getDimensions()->getWidth();
						break;
					}
				}
				$this->setWidth($width + $borderWidth);
				$this->setInnerWidth($width);
			}
		}
		//var_dump('w' . $this->getWidth() . ' ' . $this->style->getElement()->getText() . ' ' . $this->style->getRules('display') . ' ' . ($this->style->getElement()->isTextNode() ? 'text' : 'html'));
		return $this;
	}

	/**
	 * Calculate border-box dimensions
	 * @return $this
	 */
	public function calculateHeight()
	{
		$rules = $this->style->getRules();
		if ($this->style->getElement()->isTextNode()) {
			return $this->calculateTextHeight();
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
			$currentRow = 0;
			foreach ($children as $index => $childStyle) {
				$childRules = $childStyle->getRules();
				$childDimensions = $childStyle->getDimensions();
				$childElement = $childStyle->getElement();
				if ($childElement->getRow() > $currentRow) {
					$height += $childDimensions->getHeight();
					$marginTop = $childRules['margin-top'];
					if ($previousChildrenStyle) {
						$marginTop = max($marginTop, $previousChildrenStyle->getRules('margin-bottom'));
					}
					$inlineHeight += $currentInlineHeight;
					$currentInlineHeight = 0;
					$height += $marginTop;
					$currentRow++;
				} else {
					$marginTop = $childRules['margin-top'];
					if ($previousChildrenStyle) {
						$marginTop = max($marginTop, $previousChildrenStyle->getRules('margin-bottom'));
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
		//var_dump('h' . $this->getHeight() . ' ' . $this->style->getElement()->getText() . ' ' . $this->style->getRules('display') . ' ' . ($this->style->getElement()->isTextNode() ? 'text' : 'html'));
		return $this;
	}
}
