<?php
declare(strict_types=1);
/**
 * Display class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class Display
 */
class Display extends Normalizer
{
	public function normalize($ruleValue): array
	{
		$normalized = ['display' => $ruleValue];
		if ($this->element->isTextNode()) {
			$normalized = ['display' => 'inline'];
		}
		return $normalized;
	}
}
