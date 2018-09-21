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
	 * Calculate coordinates
	 */
	public function calculate()
	{
		$style = $this->style;
		$rules = $style->getRules();
		$htmlX = 0;
		$htmlY = 0;
		if ($this->style->getElement()->isRoot()) {
			$pageCoord = $this->document->getCurrentPage()->getCoordinates();
			$htmlX = $pageCoord->getAbsoluteHtmlX();
			$htmlY = $pageCoord->getAbsoluteHtmlY();
		}
		if ($parent = $style->getParent()) {
			$htmlX += $parent->getCoordinates()->getAbsoluteHtmlX();
			$htmlY += $parent->getCoordinates()->getAbsoluteHtmlY();
			$htmlX += $parent->getRules()['padding-left'];
			$htmlY += $parent->getRules()['padding-top'];
		}
		if ($previous = $style->getPrevious()) {
			$htmlY += $previous->getDimensions()->getHeight() + max($rules['margin-top'], $previous->getRules()['margin-bottom']);
		}
		$htmlX += $rules['margin-left'];
		if ($rules['box-sizing'] === 'border-box') {
			$htmlX += $rules['border-left-width'];
			$htmlY += $rules['border-top-width'];
		}
		$this->absoluteHtmlX = $htmlX;
		$this->absoluteHtmlY = $htmlY;
		$this->convertHtmlToPdf();
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
