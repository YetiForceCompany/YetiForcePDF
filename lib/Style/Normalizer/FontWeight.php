<?php
declare(strict_types=1);
/**
 * FontWeight class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class FontWeight
 */
class FontWeight extends Normalizer
{
	public function normalize(string $ruleValue): array
	{
		return ['font-weight' => strtolower($ruleValue)];
	}
}
