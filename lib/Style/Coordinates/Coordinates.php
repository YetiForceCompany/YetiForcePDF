<?php
declare(strict_types=1);
/**
 * Coordinates class
 *
 * @package   YetiForcePDF\Style\Coordinates
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Coordinates;

/**
 * Class Coordinates
 */
class Coordinates extends \YetiForcePDF\Base
{

	/**
	 * @var \YetiForcePDF\Style\Style
	 */
	protected $style;
	/**
	 * Absolute X position inside pdf coordinate system
	 * @var float
	 */
	protected $absolutePdfX = 0;
	/**
	 * Absolute Y position inside pdf coordinate system
	 * @var float
	 */
	protected $absolutePdfY = 0;
	/**
	 * Absolute X position inside html coordinate system
	 * @var float
	 */
	protected $absoluteHtmlX = 0;
	/**
	 * Absolute Y position inside html coordinate system
	 * @var float
	 */
	protected $absoluteHtmlY = 0;
	/**
	 * @var \YetiForcePDF\Style\Coordinates\Offset
	 */
	protected $offset;

	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		parent::init();
		$this->offset = (new \YetiForcePDF\Style\Coordinates\Offset())
			->setDocument($this->document);
		if (isset($this->style)) {
			// page coordinates doesn't have style
			$this->offset->setStyle($this->style);
		}
		$this->offset->init();
		return $this;
	}

	/**
	 * Set style
	 * @param \YetiForcePDF\Style\Style $style
	 * @return \YetiForcePDF\Style\Coordinates
	 */
	public function setStyle(\YetiForcePDF\Style\Style $style): Coordinates
	{
		$this->style = $style;
		return $this;
	}

	/**
	 * Get style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getStyle(): \YetiForcePDF\Style\Style
	{
		return $this->style;
	}

	/**
	 * Set absolute pdf coordinates x position
	 * @param float $x
	 * @return \YetiForcePDF\Style\Coordinates
	 */
	public function setAbsolutePdfX(float $x): Coordinates
	{
		$this->absolutePdfX = $x;
		return $this;
	}

	/**
	 * Set absolute pdf coordinates y position
	 * @param float $y
	 * @return \YetiForcePDF\Style\Coordinates
	 */
	public function setAbsolutePdfY(float $y): Coordinates
	{
		$this->absolutePdfY = $y;
		return $this;
	}

	/**
	 * Set absolute html coordinates x position
	 * @param float $x
	 * @return \YetiForcePDF\Style\Coordinates
	 */
	public function setAbsoluteHtmlX(float $x): Coordinates
	{
		$this->absoluteHtmlX = $x;
		return $this;
	}

	/**
	 * Set absolute html coordinates y position
	 * @param float $y
	 * @return \YetiForcePDF\Style\Coordinates
	 */
	public function setAbsoluteHtmlY(float $y): Coordinates
	{
		$this->absoluteHtmlY = $y;
		return $this;
	}

	/**
	 *GSet absolute pdf coordinates x position
	 * @param float $x
	 * @return \YetiForcePDF\Style\Coordinates
	 */
	public function getAbsolutePdfX(): float
	{
		return $this->absolutePdfX;
	}

	/**
	 * Get absolute pdf coordinates y position
	 * @param float $y
	 * @return \YetiForcePDF\Style\Coordinates
	 */
	public function getAbsolutePdfY(): float
	{
		return $this->absolutePdfY;
	}

	/**
	 * Get absolute html coordinates x position
	 * @param float $x
	 * @return \YetiForcePDF\Style\Coordinates
	 */
	public function getAbsoluteHtmlX(): float
	{
		return $this->absoluteHtmlX;
	}

	/**
	 * Get absolute html coordinates y position
	 * @param float $y
	 * @return \YetiForcePDF\Style\Coordinates
	 */
	public function getAbsoluteHtmlY(): float
	{
		return $this->absoluteHtmlY;
	}

	/**
	 * Get offset from the parent element
	 * @return \YetiForcePDF\Style\Coordinates\Offset
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	/**
	 * Convert html coordinates to pdf
	 */
	protected function convertHtmlToPdfX()
	{
		$this->absolutePdfX = $this->absoluteHtmlX;

		//var_dump('converted y ' . $this->absoluteHtmlY . ' to ' . $this->absolutePdfY);
	}

	/**
	 * Convert html to pdf y
	 */
	protected function convertHtmlToPdfY()
	{
		$height = $this->style->getDimensions()->getHeight();
		$page = $this->document->getCurrentPage();
		$this->absolutePdfY = $page->getPageDimensions()->getHeight() - $this->absoluteHtmlY - $height;
	}

	/**
	 * Calculate coordinates
	 * @return $this
	 */
	public function calculate()
	{
		$style = $this->style;
		$element = $this->style->getElement();
		$offset = $this->getOffset();
		$rules = $this->style->getRules();
		if ($element->isRoot()) {
			$htmlX = $this->document->getCurrentPage()->getCoordinates()->getAbsoluteHtmlX();
			$htmlY = $this->document->getCurrentPage()->getCoordinates()->getAbsoluteHtmlY();
		} else {
			$htmlX = $style->getParent()->getCoordinates()->getAbsoluteHtmlX() + $offset->getLeft();
			$htmlY = $style->getParent()->getCoordinates()->getAbsoluteHtmlY() + $offset->getTop();
			if ($element->isTextNode()) {
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
			}
		}
		//var_dump($element->getDOMElement()->textContent . ' x:' . $htmlX . ' offset left:' . $offset->getLeft());
		$this->absoluteHtmlX = $htmlX;
		$this->absoluteHtmlY = $htmlY;
		$this->convertHtmlToPdfX();
		$this->convertHtmlToPdfY();
		return $this;
	}
}
