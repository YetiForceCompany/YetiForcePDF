<?php
declare(strict_types=1);
/**
 * Layout class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Style\Style;

/**
 * Class Layout
 */
class Layout extends \YetiForcePDF\Base
{
	/**
	 * @var Line[]
	 */
	protected $lines = [];

	/**
	 * @var Style
	 */
	protected $style;

	/**
	 * Get lines
	 * @return \YetiForcePDF\Layout\Line[]
	 */
	public function getLines()
	{
		return $this->lines;
	}

	/**
	 * Append line
	 * @param \YetiForcePDF\Layout\Line $line
	 * @return $this
	 */
	public function appendLine(Line $line)
	{
		$this->lines[] = $line;
		return $this;
	}

	/**
	 * Set style
	 * @param \YetiForcePDF\Style\Style $style
	 * @return $this
	 */
	public function setStyle(Style $style)
	{
		$this->style = $style;
		return $this;
	}


	public function getHeight()
	{

	}
}
