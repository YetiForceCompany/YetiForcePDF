<?php
declare(strict_types=1);
/**
 * MarginBottom class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class MarginBottom
 */
class MarginBottom extends Normalizer
{
    public function normalize($ruleValue): array
    {
        if (is_string($ruleValue)) {
            return ['margin-bottom' => $this->getNumberValues($ruleValue)[0]];
        }
        // value is already parsed
        return ['margin-bottom' => $ruleValue];
    }
}
