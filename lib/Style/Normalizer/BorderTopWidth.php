<?php
declare(strict_types=1);
/**
 * BorderTopWidth class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class BorderTopWidth
 */
class BorderTopWidth extends Normalizer
{
    public function normalize($ruleValue): array
    {
        if (is_string($ruleValue)) {
            return ['border-top-width' => $this->getNumberValues($ruleValue)[0]];
        }
        return ['border-top-width' => $ruleValue];
    }
}
