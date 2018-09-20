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
		$rules = $this->style->getRules();
		$htmlX = 0;
		$htmlY = 0;
		$htmlX += $rules['margin-left'];
		$htmlY += $rules['margin-top'];
		if ($rules['box-sizing'] === 'content-box') {
			$htmlX += $rules['border-left-width'];
			$htmlY += $rules['border-top-width'];
		}
		if ($parent = $style->getParent()) {
			$htmlX += $parent->getCoordinates()->getAbsoluteHtmlX();
			$htmlY += $parent->getCoordinates()->getAbsoluteHtmlY();
			$htmlX += $parent->getRules()['padding-left'];
			$htmlY += $parent->getRules()['padding-top'];
			// TODO calculate left sibling elements and add to X basing on display property (block, inline) etc..
		}
		$this->absoluteHtmlX = $htmlX;
		$this->absoluteHtmlY = $htmlY;
		$this->convertHtmlToPdf();
	}

}
