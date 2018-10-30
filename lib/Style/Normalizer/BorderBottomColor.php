<?php
declare(strict_types=1);
/**
 * BorderBottomColor class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class BorderBottomColor
 */
class BorderBottomColor extends Normalizer
{
    public function normalize($ruleValue): array
    {
        if ($this->normalized === null) {
            return $this->normalized = \YetiForcePDF\Style\Color::toRGBA($ruleValue, true);
        }
        return $this->normalized;
    }
}
