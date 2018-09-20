<?php
declare(strict_types=1);
/**
 * ElementDimensions class
 *
 * @package   YetiForcePDF\Style
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style;

/**
 * Class ElementDimensions
 */
class ElementDimensions extends \YetiForcePDF\Style\Dimensions
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
	 * Initialisations
	 * @return $this
	 */
	public function init()
	{
		parent::init();
		$this->calculate();
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
	 * Get innerWidth
	 * @return float
	 */
	public function getInnerWidth(): float
	{
		return $this->innerWidth;
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
	public function calculateTextDimensions()
	{
		return $this;
	}

	/**
	 * Calculate element dimensions
	 * @return $this
	 */
	public function calculateElementDimensions()
	{
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
			$this->calculateElementDimensions();
		}
		return $this;
	}
}
