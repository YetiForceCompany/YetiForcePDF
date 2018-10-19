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

	public function __clone()
	{
		$this->width = null;
		$this->height = null;
	}


}
