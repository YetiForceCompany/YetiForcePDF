<?php
declare(strict_types=1);
/**
 * BuildTree interface
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Layout\Coordinates\Coordinates;
use \YetiForcePDF\Layout\Coordinates\Offset;
use \YetiForcePDF\Layout\Dimensions\BoxDimensions;
use \YetiForcePDF\Html\Element;
use YetiForcePDF\Style\Style;

/**
 * Interface BuildTreeInterface
 */
interface BuildTreeInterface
{


	/**
	 * Build tree from dom tree
	 * @param \YetiForcePDF\Layout\BlockBox|null $parentBlock
	 * @return $this
	 */
	public function buildTree($parentBlock = null);
}
