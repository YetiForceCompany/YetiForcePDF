<?php
declare(strict_types=1);
/**
 * TableWrapperBlockBox class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;
use \YetiForcePDF\Layout\Coordinates\Coordinates;
use \YetiForcePDF\Layout\Coordinates\Offset;
use \YetiForcePDF\Layout\Dimensions\BoxDimensions;

/**
 * Class TableWrapperBlockBox
 */
class TableWrapperBlockBox extends InlineBlockBox
{

    /**
     * Append table box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox $parentBlock
     * @return $this
     */
    public function appendTableBox($childDomElement, $element, $style, $parentBlock)
    {
        $cleanStyle = (new \YetiForcePDF\Style\Style())->setDocument($this->document);
        $box = (new TableBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($cleanStyle)
            ->init();
        $cleanStyle->setRule('display', 'block');
        $this->appendChild($box);
        $box->getStyle()->init();
        $box->buildTree($box);
        return $box;
    }

}
