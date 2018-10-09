<?php
declare(strict_types=1);
/**
 * LineHeight class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class LineHeight
 */
class LineHeight extends Normalizer
{
	public function normalize($ruleValue): array
	{
		if (is_string($ruleValue)) {
			$matches = [];
			preg_match('/^([0-9\.]+)([a-z]+)?$/', $ruleValue, $matches);
			$originalSize = (float)$matches[1];
			if (isset($matches[2])) {
				$originalUnit = $matches[2];
			} else {
				$originalUnit = 'em';
			}
			return ['line-height' => $this->style->convertUnits($originalUnit, $originalSize)];
		}
		// value is already parsed
		return ['line-height' => $ruleValue];
	}
}
