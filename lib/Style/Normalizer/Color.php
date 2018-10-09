<?php
declare(strict_types=1);
/**
 * Color class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class Color
 */
class Color extends Normalizer
{
	public function normalize($ruleValue): array
	{
		if (is_string($ruleValue) && $ruleValue !== 'transparent') {
			$color = \YetiForcePDF\Style\Color::toRGBA($ruleValue, true);
		} else {
			// if it is number - it was normalized already
			$color = $ruleValue;
		}
		return ['color' => $color];
	}
}
