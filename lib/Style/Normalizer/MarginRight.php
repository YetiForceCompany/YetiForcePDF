<?php
declare(strict_types=1);
/**
 * MarginRight class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class MarginRight
 */
class MarginRight extends Normalizer
{
    public function normalize($ruleValue): array
    {
        if (is_string($ruleValue)) {
            return ['margin-right' => $this->getNumberValues($ruleValue)[0]];
        }
        // value is already parsed
        return ['margin-right' => $ruleValue];
    }
}
