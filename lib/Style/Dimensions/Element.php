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
	 * Get available space inside container
	 * @return float
	 */
	public function getAvailableSpace()
	{
		if ($this->style->getElement()->isRoot()) {
			return $this->document->getCurrentPage()->getPageDimensions()->getInnerWidth();
		}
		$style = $this->style;
		$paddingWidth = $style->getRules('padding-left') + $style->getRules('padding-right');
		$borderWidth = $style->getRules('border-left-width') + $style->getRules('border-right-width');
		return $style->getParent()->getDimensions()->getAvailableSpace() - $paddingWidth - $borderWidth;
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
			$paddingWidth = $rules['padding-left'] + $rules['padding-right'];
			if ($rules['display'] === 'block') {
				$marginWidth = $rules['margin-left'] + $rules['margin-right'];
				$this->setWidth($parentDimensions->getInnerWidth() - $marginWidth);
				$innerWidth = $this->getWidth() - $paddingWidth - $borderWidth;
				$this->setInnerWidth($innerWidth);
			} else {
				$width = 0;
				foreach ($this->style->getChildren() as $child) {
					$childRules = $child->getRules();
					if ($child->getRules('display') !== 'block') {
						$width += $child->getDimensions()->getWidth();
						if (isset($previousChild)) {
							$width += max($childRules['margin-left'], $previousChild->getRules('margin-right'));
						}
						//var_dump($childRules['display'] . ' w' . $width . ' ' . $child->getElement()->getText() . ($child->getElement()->isTextNode() ? ' text' : ' html'));
					} else {
						$width = $child->getDimensions()->getWidth();
						break;
					}
					$previousChild = $child;
				}
				$width += $childRules['margin-right'];
				if ($rules['display'] === 'inline') {
					$paddingWidth = 0;
				}
				$this->setWidth($width + $borderWidth + $paddingWidth);
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
			$height = 0;
			foreach ($this->style->getChildren() as $index => $childStyle) {
				$childRules = $childStyle->getRules();
				$childDimensions = $childStyle->getDimensions();
				$height += $childStyle->getDimensions()->getHeight();
			}
			$borderHeight = $rules['border-top-width'] + $rules['border-bottom-width'];
			$this->setInnerHeight($height);
			if ($rules['display'] !== 'inline') {
				$height += (float)$rules['padding-bottom'] + (float)$rules['padding-top'];
			}
			$this->setHeight($height + $borderHeight);
		}
		//var_dump('h' . $this->getHeight() . ' ' . $this->style->getElement()->getText() . ' ' . $this->style->getRules('display') . ' ' . ($this->style->getElement()->isTextNode() ? 'text' : 'html'));
		return $this;
	}
}
