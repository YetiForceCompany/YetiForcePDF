<?php
declare(strict_types=1);
/**
 * Overflow class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class Overflow
 */
class Overflow extends Normalizer
{
	public function normalize($ruleValue): array
	{
		if (in_array($ruleValue, ['visible', 'hidden'])) {
			return ['overflow' => $ruleValue];
		}
		return ['overflow' => 'visible'];
	}
}
