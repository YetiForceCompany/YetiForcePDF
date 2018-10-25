<?php
declare(strict_types=1);
/**
 * BorderRightWidth class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class BorderRightWidth
 */
class BorderRightWidth extends Normalizer
{
    public function normalize($ruleValue): array
    {
        if (is_string($ruleValue)) {
            return ['border-right-width' => $this->getNumberValues($ruleValue)[0]];
        }
        return ['border-right-width' => $ruleValue];
    }
}
