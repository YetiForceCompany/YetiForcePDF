<?php
declare(strict_types=1);
/**
 * Coordinates class
 *
 * @package   YetiForcePDF\Render\Coordinates
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render\Coordinates;

use YetiForcePDF\Render\Box;

/**
 * Class Coordinates
 */
class Coordinates extends \YetiForcePDF\Base
{

	/**
	 * @var Box
	 */
	protected $box;
	/**
	 * Absolute X position inside html coordinate system
	 * @var float
	 */
	protected $htmlX = 0;
	/**
	 * Absolute Y position inside html coordinate system
	 * @var float
	 */
	protected $htmlY = 0;

	/**
	 * Set box
	 * @param \YetiForcePDF\Render\Box $box
	 * @return $this
	 */
	public function setBox(Box $box)
	{
		$this->box = $box;
		return $this;
	}

	/**
	 * Get box
	 * @return \YetiForcePDF\Render\Box
	 */
	public function getBox()
	{
		return $this->box;
	}

	/**
	 * Set absolute html coordinates x position
	 * @param float $x
	 * @return $this
	 */
	public function setX(float $x)
	{
		$this->htmlX = $x;
		return $this;
	}

	/**
	 * Get html X
	 * @return float
	 */
	public function getX()
	{
		return $this->htmlX;
	}

	/**
	 * Get html Y
	 * @return float
	 */
	public function getY()
	{
		return $this->htmlY;
	}

	/**
	 * Set absolute html coordinates y position
	 * @param float $y
	 * @return $this
	 */
	public function setY(float $y)
	{
		$this->htmlY = $y;
		return $this;
	}


	/**
	 * Get pdf X coodrinates
	 */
	public function getPdfX()
	{
		return $this->htmlX;
	}

	/**
	 * Convert html to pdf y
	 */
	public function getPdfY()
	{
		$height = $this->box->getDimensions()->getHeight();
		$page = $this->document->getCurrentPage();
		return $page->getOuterDimensions()->getHeight() - $this->htmlY - $height;
	}

}
