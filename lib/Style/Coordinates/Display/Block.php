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
	 * Do we have X calculated already?
	 * @var bool
	 */
	protected $xCalculated = false;

	/**
	 * Calculate X coordinates
	 * @return $this
	 */
	public function calculateX()
	{
		$style = $this->style;
		$element = $this->style->getElement();
		$offset = $this->getOffset();
		$htmlX = 0;
		if ($element->isRoot()) {
			$pageCoord = $this->document->getCurrentPage()->getCoordinates();
			$htmlX = $pageCoord->getAbsoluteHtmlX();
		} else {
			if ($parent = $style->getParent()) {
				$parentRules = $parent->getRules();
				$parentCoordinates = $parent->getCoordinates();
				$htmlX += $parentCoordinates->getAbsoluteHtmlX();
			}
			//if (!$element->isTextNode()) {
			$htmlX += $offset->getLeft();
			//}
		}
		//var_dump($element->getDOMElement()->textContent . ' x:' . $htmlX . ' offset left:' . $offset->getLeft());
		$this->absoluteHtmlX = $htmlX;
		$this->convertHtmlToPdf();
		$this->xCalculated = true;
		return $this;
	}

	/**
	 * Calculate Y coordinates
	 * @return $this
	 */
	public function calculateY()
	{
		$style = $this->style;
		$element = $this->style->getElement();
		$offset = $this->getOffset();
		$htmlY = 0;
		if ($element->isRoot()) {
			$pageCoord = $this->document->getCurrentPage()->getCoordinates();
			$htmlY = $pageCoord->getAbsoluteHtmlY();
		} else {
			if ($parent = $style->getParent()) {
				$parentRules = $parent->getRules();
				$parentCoordinates = $parent->getCoordinates();
				$htmlY += $parentCoordinates->getAbsoluteHtmlY();
			}
			//if (!$element->isTextNode()) {
			$htmlY += $offset->getTop();
			//}
		}
		//var_dump(($element->isTextNode() ? '[text]' : '[html]') . ' ' . $element->getDOMElement()->textContent . ' y:' . $htmlY . ' offset top:' . $offset->getTop());
		$this->absoluteHtmlY = $htmlY;
		$this->convertHtmlToPdf();
		return $this;
	}

	/**
	 * Calculate
	 * @return $this
	 */
	public function calculate()
	{
		if ($this->xCalculated) {
			$this->getOffset()->calculateTop();
			$this->calculateY();
		} else {
			$this->getOffset()->calculateLeft();
			$this->calculateX();
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
