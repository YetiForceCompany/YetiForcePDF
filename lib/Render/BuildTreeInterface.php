<?php
declare(strict_types=1);
/**
 * BuildTree interface
 *
 * @package   YetiForcePDF\Render
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render;

use \YetiForcePDF\Render\Coordinates\Coordinates;
use \YetiForcePDF\Render\Coordinates\Offset;
use \YetiForcePDF\Render\Dimensions\BoxDimensions;
use \YetiForcePDF\Html\Element;
use YetiForcePDF\Style\Style;

/**
 * Interface BuildTreeInterface
 */
interface BuildTreeInterface
{


	/**
	 * Build tree from dom tree
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
	 * @return $this
	 */
	public function buildTree($parentBlock = null);
}
