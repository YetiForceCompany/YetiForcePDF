<?php
declare(strict_types=1);
/**
 * TextAlign class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class TextAlign
 */
class TextAlign extends Normalizer
{
	public function normalize($ruleValue): array
	{
		$normalized = ['text-align' => $ruleValue];
		return $normalized;
	}
}
