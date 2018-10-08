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
			preg_match_all('/([0-9]+)([a-z]+)/', $ruleValue, $matches);
			$originalSize = $this->style->getFont()->getSize() * (float)$matches[1][0];
			$originalUnit = $matches[2][0];
			return ['line-height' => $this->document->convertUnits($originalUnit, $originalSize)];
		}
		// value is already parsed
		return ['line-height' => $ruleValue];
	}
}
