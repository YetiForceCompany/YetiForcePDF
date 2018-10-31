<?php
declare(strict_types=1);
/**
 * ElementBox class
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
 * Class ElementBox
 */
class ElementBox extends Box
{
    /**
     * @var Element
     */
    protected $element;

    /**
     * Get element
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Set element
     * @param Element $element
     * @return $this
     */
    public function setElement(Element $element)
    {
        $this->element = $element;
        $element->setBox($this);
        return $this;
    }

    /**
     * Remove empty lines
     * @return $this
     */
    public function removeEmptyLines()
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof LineBox) {
                $textContent = $child->getTextContent();
                if ($textContent === '' || $textContent === ' ') {
                    $this->removeChild($child);
                }
            } else {
                $child->removeEmptyLines();
            }
        }
        return $this;
    }

    /**
     * Build tree
     * @param $parentBlock
     * @return $this
     */
    public function buildTree($parentBlock = null)
    {
        $domElement = $this->getElement()->getDOMElement();
        if ($domElement->hasChildNodes()) {
            foreach ($domElement->childNodes as $childDomElement) {
                if ($childDomElement instanceof \DOMComment) {
                    continue;
                }
                $styleStr = '';
                if ($childDomElement instanceof \DOMElement && $childDomElement->hasAttribute('style')) {
                    $styleStr = $childDomElement->getAttribute('style');
                }
                $element = (new Element())
                    ->setDocument($this->document)
                    ->setDOMElement($childDomElement)
                    ->init();
                // for now only basic style is used - from current element only (with defaults)
                $style = (new \YetiForcePDF\Style\Style())
                    ->setDocument($this->document)
                    ->setElement($element)
                    ->setContent($styleStr)
                    ->parseInline();
                $display = $style->getRules('display');
                switch ($display) {
                    case 'block':
                        $this->appendBlockBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'table':
                        $tableWrapper = $this->appendTableWrapperBlockBox($childDomElement, $element, $style, $parentBlock);
                        $tableWrapper->appendTableBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'table-row':
                        $rowGroup = $this->appendTableRowGroupBox($childDomElement, $element, $style, $parentBlock);
                        $rowGroup->appendTableRowBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'table-cell':
                        $this->appendTableCellBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'inline':
                        $inline = $this->appendInlineBox($childDomElement, $element, $style, $parentBlock);
                        if (isset($inline) && $childDomElement instanceof \DOMText) {
                            $inline->setAnonymous(true)->appendText($childDomElement, null, null, $parentBlock);
                        }
                        break;
                    case 'inline-block':
                        $this->appendInlineBlockBox($childDomElement, $element, $style, $parentBlock);
                        break;
                }
            }
        }
        $this->removeEmptyLines();
        return $this;
    }

    /**
     * Add border instructions
     * @param array $element
     * @param string $pdfX
     * @param string $pdfY
     * @param string $width
     * @param string $height
     * @return array
     */
    protected function addBorderInstructions(array $element, string $pdfX, string $pdfY, string $width, string $height)
    {
        $rules = $this->style->getRules();
        $x1 = '0';
        $x2 = $width;
        $y1 = $height;
        $y2 = '0';
        $element[] = '% start border';
        if ($rules['border-top-width'] && $rules['border-top-style'] !== 'none') {
            $path = implode(" l\n", [
                implode(' ', [$x2, $y1]),
                implode(' ', [bcsub($x2, $rules['border-right-width'], 4), bcsub((string)$y1, $rules['border-top-width'], 4)]),
                implode(' ', [bcadd($x1, $rules['border-left-width'], 4), bcsub((string)$y1, $rules['border-top-width'], 4)]),
                implode(' ', [$x1, $y1])
            ]);
            $borderTop = [
                'q',
                "{$rules['border-top-color'][0]} {$rules['border-top-color'][1]} {$rules['border-top-color'][2]} rg",
                "1 0 0 1 $pdfX $pdfY cm",
                "$x1 $y1 m", // move to start point
                $path . ' l h',
                'f',
                'Q'
            ];
            $element = array_merge($element, $borderTop);
        }
        if ($rules['border-right-width'] && $rules['border-right-style'] !== 'none') {
            $path = implode(" l\n", [
                implode(' ', [$x2, $y2]),
                implode(' ', [bcsub($x2, $rules['border-right-width'], 4), bcadd((string)$y2, $rules['border-bottom-width'], 4)]),
                implode(' ', [bcsub($x2, $rules['border-right-width'], 4), bcsub((string)$y1, $rules['border-top-width'], 4)]),
                implode(' ', [$x2, $y1]),
            ]);
            $borderTop = [
                'q',
                "1 0 0 1 $pdfX $pdfY cm",
                "{$rules['border-right-color'][0]} {$rules['border-right-color'][1]} {$rules['border-right-color'][2]} rg",
                "$x2 $y1 m",
                $path . ' l h',
                'f',
                'Q'
            ];
            $element = array_merge($element, $borderTop);
        }
        if ($rules['border-bottom-width'] && $rules['border-bottom-style'] !== 'none') {
            $path = implode(" l\n", [
                implode(' ', [$x2, $y2]),
                implode(' ', [bcsub($x2, $rules['border-right-width'], 4), bcadd($y2, $rules['border-bottom-width'], 4)]),
                implode(' ', [bcadd($x1, $rules['border-left-width'], 4), bcadd($y2, $rules['border-bottom-width'], 4)]),
                implode(' ', [$x1, $y2]),
            ]);
            $borderTop = [
                'q',
                "1 0 0 1 $pdfX $pdfY cm",
                "{$rules['border-bottom-color'][0]} {$rules['border-bottom-color'][1]} {$rules['border-bottom-color'][2]} rg",
                "$x1 $y2 m",
                $path . ' l h',
                'f',
                'Q'
            ];
            $element = array_merge($element, $borderTop);
        }
        if ($rules['border-left-width'] && $rules['border-left-style'] !== 'none') {
            $path = implode(" l\n", [
                implode(' ', [bcadd($x1, $rules['border-left-width'], 4), bcsub($y1, $rules['border-top-width'], 4)]),
                implode(' ', [bcadd($x1, $rules['border-left-width'], 4), bcadd($y2, $rules['border-bottom-width'], 4)]),
                implode(' ', [$x1, $y2]),
                implode(' ', [$x1, $y1]),
            ]);
            $borderTop = [
                'q',
                "1 0 0 1 $pdfX $pdfY cm",
                "{$rules['border-left-color'][0]} {$rules['border-left-color'][1]} {$rules['border-left-color'][2]} rg",
                "$x1 $y1 m",
                $path . ' l h',
                'f',
                'Q'
            ];
            $element = array_merge($element, $borderTop);
        }
        $element[] = '% end border';
        return $element;
    }
}
