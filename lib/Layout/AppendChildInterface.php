<?php
declare(strict_types=1);
/**
 * AppendChild interface
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Layout\Coordinates\Coordinates;
use \YetiForcePDF\Layout\Coordinates\Offset;
use \YetiForcePDF\Layout\Dimensions\BoxDimensions;
use \YetiForcePDF\Html\Element;
use YetiForcePDF\Style\Style;

/**
 * Interface AppendChildInterface
 */
interface AppendChildInterface
{

    /**
     * Append block box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox $parentBlock
     * @return $this
     */
    public function appendBlock($childDomElement, $element, $style, $parentBlock);

    /**
     * Append table wrapper block box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox $parentBlock
     * @return $this
     */
    public function appendTableWrapperBlock($childDomElement, $element, $style, $parentBlock);

    /**
     * Append inline block box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox|null $parentBlock
     * @return $this
     */
    public function appendInlineBlock($childDomElement, $element, $style, $parentBlock);

    /**
     * Add inline child (and split text to individual characters)
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox|null $parentBlock
     * @return $this
     */
    public function appendInline($childDomElement, $element, $style, $parentBlock);

    /**
     * Build tree from dom tree
     * @param \YetiForcePDF\Layout\BlockBox|null $parentBlock
     * @return $this
     */
    public function buildTree($parentBlock = null);
}
