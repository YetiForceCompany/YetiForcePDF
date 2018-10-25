<?php
declare(strict_types=1);
/**
 * MaxWidth class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class MaxWidth
 */
class MaxWidth extends Normalizer
{
    public function normalize($ruleValue): array
    {
        if (is_string($ruleValue) && $ruleValue !== 'none') {
            return ['max-width' => $this->getNumberValues($ruleValue)[0]];
        }
        // value is already parsed
        return ['max-width' => $ruleValue];
    }
}
