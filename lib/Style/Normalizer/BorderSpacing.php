<?php
declare(strict_types=1);
/**
 * BorderSpacing class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class BorderSpacing
 */
class BorderSpacing extends Normalizer
{

    public function normalize($ruleValue): array
    {
        if (is_string($ruleValue) && $ruleValue !== 'auto') {
            return ['border-spacing' => $this->getNumberValues($ruleValue)[0]];
        }
        // value is already parsed
        return ['border-spacing' => $ruleValue];
    }
}
