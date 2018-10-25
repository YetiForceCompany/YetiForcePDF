<?php
declare(strict_types=1);
/**
 * LineHeight class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class LineHeight
 */
class LineHeight extends Normalizer
{
    public function normalize($ruleValue): array
    {
        if (is_string($ruleValue)) {
            return ['line-height' => $this->getNumberValues($ruleValue)[0]];
        }
        // value is already parsed
        return ['line-height' => $ruleValue];
    }
}
