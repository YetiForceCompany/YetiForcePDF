<?php
declare(strict_types=1);
/**
 * LineBox class
 *
 * @package   YetiForcePDF\Render
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render;

use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;

/**
 * Class LineBox
 */
class LineBox extends Box
{

	/**
	 * Reflow elements and create render tree basing on dom tree
	 * @return $this
	 */
	public function reflow()
	{
		foreach ($this->getChildren() as $childBox) {
			$childBox->reflow();
		}

		return $this;
	}
}
