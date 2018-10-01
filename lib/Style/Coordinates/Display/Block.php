<?php
declare(strict_types=1);
/**
 * Block class
 *
 * @package   YetiForcePDF\Style\Coordinates\Display
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Coordinates\Display;

/**
 * Class Block
 */
class Block extends \YetiForcePDF\Style\Coordinates\Coordinates
{

	/**
	 * Calculate X coordinates
	 * @return $this
	 */
	public function calculateX($withRules = null)
	{
		$style = $this->style;
		$element = $this->style->getElement();
		$offset = $this->getOffset();
		if ($element->isRoot()) {
			$pageCoord = $this->document->getCurrentPage()->getCoordinates();
			$htmlX = $pageCoord->getAbsoluteHtmlX();
		} else {
			$htmlX = $style->getParent()->getCoordinates()->getAbsoluteHtmlX() + $offset->getLeft();
			/*if ($element->isTextNode()) {
				if ($rules['text-align'] !== 'left') {
					if ($rules['text-align'] === 'center') {
						$width = $style->getParent()->getDimensions()->getInnerWidth();
						$textWidth = $style->getFont()->getTextWidth($element->getText());
						$htmlX += ($width / 2) - ($textWidth / 2);
					}
					if ($rules['text-align'] === 'right') {
						$width = $style->getParent()->getDimensions()->getInnerWidth();
						$textWidth = $style->getFont()->getTextWidth($element->getText());
						$htmlX += $width - $textWidth;
					}
				}
			}*/
		}
		//var_dump($element->getDOMElement()->textContent . ' x:' . $htmlX . ' offset left:' . $offset->getLeft());
		$this->absoluteHtmlX = $htmlX;
		$this->convertHtmlToPdfX();
		if ($withRules !== null) {
			foreach ($this->style->getChildren($withRules) as $child) {
				$child->getCoordinates()->calculateX($withRules);
			}
		}
		return $this;
	}

	/**
	 * Calculate Y coordinates
	 * @return $this
	 */
	public function calculateY($withRules = null)
	{
		$style = $this->style;
		$element = $this->style->getElement();
		$offset = $this->getOffset();
		if ($element->isRoot()) {
			$pageCoord = $this->document->getCurrentPage()->getCoordinates();
			$htmlY = $pageCoord->getAbsoluteHtmlY();
		} else {
			$htmlY = $style->getParent()->getCoordinates()->getAbsoluteHtmlY() + $offset->getTop();
		}
		//var_dump(($element->isTextNode() ? '[text]' : '[html]') . ' ' . $element->getDOMElement()->textContent . ' y:' . $htmlY . ' offset top:' . $offset->getTop());
		$this->absoluteHtmlY = $htmlY;
		$this->convertHtmlToPdfY();
		if ($withRules !== null) {
			foreach ($this->style->getChildren($withRules) as $child) {
				$child->getCoordinates()->calculateY($withRules);
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
