<?php
declare(strict_types=1);
/**
 * Offset class
 *
 * @package   YetiForcePDF\Style\Coordinates
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Coordinates;

/**
 * Class Offset
 */
class Offset extends \YetiForcePDF\Base
{
	/**
	 * @var \YetiForcePDF\Style\Style
	 */
	protected $style;
	/**
	 * Offset top
	 * @var float
	 */
	protected $top;
	/**
	 * Offset left
	 * @var int
	 */
	protected $left = 0;

	/**
	 * Get offset top
	 * @return float
	 */
	public function getTop()
	{
		return $this->top;
	}

	/**
	 * Set offset top
	 * @param float $top
	 * @return $this
	 */
	public function setTop(float $top)
	{
		$this->top = $top;
		return $this;
	}

	/**
	 * Add offset top
	 * @param float $add
	 * @return $this
	 */
	public function addTop(float $add)
	{
		$this->top += $add;
		return $this;
	}

	/**
	 * Get offset left
	 * @return float
	 */
	public function getLeft()
	{
		return $this->left;
	}

	/**
	 * Set offset left
	 * @param float $left
	 * @return $this
	 */
	public function setLeft(float $left)
	{
		$this->left = $left;
		return $this;
	}

	/**
	 * Add offset left
	 * @param float $add
	 * @return $this
	 */
	public function addLeft(float $add)
	{
		$this->left += $add;
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
	 * Get style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getStyle()
	{
		return $this->style;
	}

}
