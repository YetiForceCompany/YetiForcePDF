<?php
declare(strict_types=1);
/**
 * Append table trait
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
 * Trait AppendTable
 */
trait AppendTableTrait
{

    /**
     * {@inheritdoc}
     */
    public function appendTableRowGroupBox($childDomElement, $element, $style, $parentBlock)
    {
        if ($this->getCurrentLineBox()) {
            $this->closeLine();
        }
        $box = (new TableRowGroupBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        $box->buildTree($box);
        return $box;
    }

    /**
     * {@inheritdoc}
     */
    public function appendTableRowBox($childDomElement, $element, $style, $parentBlock)
    {
        if ($this->getCurrentLineBox()) {
            $this->closeLine();
        }
        $box = (new TableRowBlockBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        $box->buildTree($box);
        return $box;
    }

}
