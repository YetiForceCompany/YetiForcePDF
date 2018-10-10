<?php
declare(strict_types=1);
/**
 * Dimensions class
 *
 * @package   YetiForcePDF\Render\Dimensions
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render\Dimensions;

/**
 * Class Dimensions
 */
class Dimensions extends \YetiForcePDF\Base
{
	/**
	 * @var float
	 */
	protected $width;
	/**
	 * Height initially must be null to figure out it was calculated already or not
	 * @var float|null
	 */
	protected $height;
	/**
	 * Available horizontal space
	 * No matter if we are blockBox or inline we have some space horizontally to place elements
	 * This is the page width, parent BlockBox width and so on
	 * Available horizontal space is just a width limit
	 * It is used for elements that have width calculated (inline, inline-block, float)
	 * Each box (no matter what kind of box), should have this property
	 * @var float
	 */
	protected $availableSpace;

	/**
	 * Get width
	 * @return float
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Get height
	 * @return float|null
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Set width
	 * @param float $width
	 * @return $this
	 */
	public function setWidth(float $width)
	{
		$this->width = $width;
		return $this;
	}

	/**
	 * Set height
	 * @param float $height
	 * @return $this
	 */
	public function setHeight(float $height)
	{
		$this->height = $height;
		return $this;
	}

	/**
	 * Set available space
	 * @param float $width
	 * @return $this
	 */
	public function setAvailableSpace(float $width)
	{
		$this->availableSpace = $width;
		return $this;
	}

	/**
	 * Get available space
	 * @return float
	 */
	public function getAvailableSpace()
	{
		return $this->availableSpace;
	}


}
