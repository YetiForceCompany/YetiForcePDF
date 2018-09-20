<?php
declare(strict_types=1);
/**
 * Dimensions class
 *
 * @package   YetiForcePDF\Style
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style;

/**
 * Class Dimensions
 */
class Dimensions extends \YetiForcePDF\Base
{
	/**
	 * @var float
	 */
	protected $width = 0;
	/**
	 * @var float
	 */
	protected $height = 0;

	/**
	 * Get width
	 * @return float
	 */
	public function getWidth(): float
	{
		return $this->width;
	}

	/**
	 * Get height
	 * @return float
	 */
	public function getHeight(): float
	{
		return $this->height;
	}

	/**
	 * Set width
	 * @param float $width
	 * @return \YetiForcePDF\Style\Dimensions
	 */
	public function setWidth(float $width): Dimensions
	{
		$this->width = $width;
		return $this;
	}

	/**
	 * Set height
	 * @param float $height
	 * @return \YetiForcePDF\Style\Dimensions
	 */
	public function setHeight(float $height): Dimensions
	{
		$this->height = $height;
		return $this;
	}
}
