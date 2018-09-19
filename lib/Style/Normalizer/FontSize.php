<?php
declare(strict_types=1);
/**
 * FontSize class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class FontSize
 */
class FontSize extends Normalizer
{
	public function normalize(string $ruleValue): array
	{
		$matches = [];
		preg_match_all('/([0-9]+)([a-z]+)/', $ruleValue, $matches);
		$originalSize = $matches[1];
		$originalUnit = $matches[2];
		$normalized = ['font-size' => $this->document->convertUnits($originalUnit, $originalSize)];
		return $normalized;
	}
}
