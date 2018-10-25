<?php
declare(strict_types=1);
/**
 * PaddingRight class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class PaddingRight
 */
class PaddingRight extends Normalizer
{
    public function normalize($ruleValue): array
    {
        if (is_string($ruleValue)) {
            return ['padding-right' => $this->getNumberValues($ruleValue)[0]];
        }
        // value is already parsed
        return ['padding-right' => $ruleValue];
    }
}
