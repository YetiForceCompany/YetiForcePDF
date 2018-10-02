<?php
declare(strict_types=1);
/**
 * Offset class
 *
 * @package   YetiForcePDF\Style\Coordinates
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Coordinates;

/**
 * Class Offset
 */
class Offset extends \YetiForcePDF\Base
{
	/**
	 * @var \YetiForcePDF\Style\Style
	 */
	protected $style;
	/**
	 * Offset top
	 * @var float
	 */
	protected $top = 0;
	/**
	 * Offset left
	 * @var int
	 */
	protected $left = 0;

	/**
	 * Get offset top
	 * @return float
	 */
	public function getTop()
	{
		return $this->top;
	}

	/**
	 * Set offset top
	 * @param float $top
	 * @return $this
	 */
	public function setTop(float $top)
	{
		$this->top = $top;
		return $this;
	}

	/**
	 * Add offset top
	 * @param float $add
	 * @return $this
	 */
	public function addTop(float $add)
	{
		$this->top += $add;
		return $this;
	}

	/**
	 * Get offset left
	 * @return float
	 */
	public function getLeft()
	{
		return $this->left;
	}

	/**
	 * Set offset left
	 * @param float $left
	 * @return $this
	 */
	public function setLeft(float $left)
	{
		$this->left = $left;
		return $this;
	}

	/**
	 * Add offset left
	 * @param float $add
	 * @return $this
	 */
	public function addLeft(float $add)
	{
		$this->left += $add;
		return $this;
	}

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
	 * Get style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getStyle()
	{
		return $this->style;
	}

	/**
	 * Calculate element offsets
	 * @return $this
	 */
	public function calculate()
	{
		$element = $this->style->getElement();
		$rules = $this->style->getRules();
		if ($element->isRoot()) {
			$this->left = $this->document->getCurrentPage()->getCoordinates()->getAbsoluteHtmlX();
			$this->top = $this->document->getCurrentPage()->getCoordinates()->getAbsoluteHtmlY();
		} else {
			$parent = $this->style->getParent();
			$parentLeft = $parent->getRules('padding-left') + $parent->getRules('border-left-width');
			$this->left = $parentLeft;
			$this->top = $parent->getRules('padding-top') + $parent->getRules('border-top-width');
			$margin = ['top' => $rules['margin-top'], 'left' => $rules['margin-left']];
			if ($previous = $this->style->getPrevious()) {
				$previousDisplay = $previous->getRules('display');
				$left = $previous->getOffset()->getLeft() + $previous->getDimensions()->getWidth();
				$width = $this->style->getDimensions()->getWidth();
				$horizontalSpacing = max($margin['left'], $previous->getRules('margin-right')) + $rules['margin-right'];
				$willFit = ($left + $width + $horizontalSpacing - $parentLeft) <= $parent->getDimensions()->getInnerWidth();
				if ($previousDisplay !== 'block' && $rules['display'] !== 'block' && $willFit) {
					if (!$element->areRowColSet()) {
						$element->setColumn($previous->getElement()->getColumn() + 1);
						$element->setRow($previous->getElement()->getRow());
					}
					$this->left = $left;
					$margin['left'] = max($margin['left'], $previous->getRules('margin-right'));
					$this->top = $previous->getOffset()->getTop() - $previous->getRules('margin-top');
					//var_dump($previous->getOffset()->getLeft() . '+' . $previous->getDimensions()->getWidth() . $previous->getElement()->getText());
				} else {
					if (!$element->areRowColSet()) {
						$element->setColumn(0);
						$element->setRow($previous->getElement()->getRow() + 1);
					}
					$this->top = $previous->getOffset()->getTop() + $previous->getDimensions()->getHeight();
					$margin['top'] = max($margin['top'], $previous->getRules('margin-bottom'));
					$this->left = $parentLeft;
					$margin['left'] = $rules['margin-left'];
				}
			}
			$element->finishRowCol();
			$this->left += $margin['left'];
			$this->top += $margin['top'];
		}
		//var_dump($element->getDOMElement()->textContent . ' left:' . $this->left);
		return $this;
	}
}
