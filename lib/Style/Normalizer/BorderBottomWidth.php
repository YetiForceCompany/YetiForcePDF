<?php
declare(strict_types=1);
/**
 * BorderBottomWidth class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class BorderBottomWidth
 */
class BorderBottomWidth extends Normalizer
{
	public function normalize($ruleValue): array
	{
		if (is_string($ruleValue)) {
			$matches = [];
			preg_match('/([0-9]+)([a-z]+)/', $ruleValue, $matches);
			$originalSize = (float)$matches[1];
			$originalUnit = $matches[2];
			$size = $this->style->convertUnits($originalUnit, $originalSize);
		} else {
			// if it is number it was calculated already
			$size = $ruleValue;
		}
		$normalized = [
			'border-bottom-width' => $size,
		];
		return $normalized;
	}
}
