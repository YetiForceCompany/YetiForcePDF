<?php
declare(strict_types=1);
/**
 * TextBox class
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
 * Class TextBox
 */
class TextBox extends ElementBox implements BoxInterface
{

    /**
     * @var string
     */
    protected $text;

    /*
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->style = (new \YetiForcePDF\Style\Style())
            ->setDocument($this->document)
            ->setBox($this)
            ->init();
        return $this;
    }

    /**
     * Set text
     * @param string $text
     * @return $this
     */
    public function setText(string $text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Measure width
     * @return $this
     */
    public function measureWidth()
    {
        $this->getDimensions()->setWidth($this->getStyle()->getFont()->getTextWidth($this->getText()));
        return $this;
    }

    /**
     * Measure height
     * @return $this
     */
    public function measureHeight()
    {
        $this->getDimensions()->setHeight($this->getStyle()->getFont()->getTextHeight($this->getText()));
        return $this;
    }

    /**
     * Position
     * @return $this
     */
    public function measureOffset()
    {
        $this->getOffset()->setLeft('0');
        $this->getOffset()->setTop('0');
        return $this;
    }

    /**
     * Position
     * @return $this
     */
    public function measurePosition()
    {
        $parent = $this->getParent();
        $this->getCoordinates()->setX(bcadd($parent->getCoordinates()->getX(), $this->getOffset()->getLeft(), 4));
        $this->getCoordinates()->setY(bcadd($parent->getCoordinates()->getY(), $this->getOffset()->getTop(), 4));
        return $this;
    }

    public function __clone()
    {
        $this->style = clone $this->style;
        $this->offset = clone $this->offset;
        $this->dimensions = clone $this->dimensions;
        $this->coordinates = clone $this->coordinates;
        $this->children = [];
    }

    /**
     * Filter text
     * Filter the text, this is applied to all text just before being inserted into the pdf document
     * it escapes the various things that need to be escaped, and so on
     *
     * @return string
     */
    protected function filterText($text)
    {
        $text = trim(preg_replace('/[\n\r\t\s]+/', ' ', mb_convert_encoding($text, 'UTF-8')));
        $text = preg_replace('/\s+/', ' ', $text);
        $text = mb_convert_encoding($text, 'UTF-16');
        return strtr($text, [')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r']);
    }

    /**
     * Get element PDF instructions to use in content stream
     * @return string
     */
    public function getInstructions(): string
    {
        $style = $this->getStyle();
        $rules = $style->getRules();
        $font = $style->getFont();
        $fontStr = '/' . $font->getNumber() . ' ' . $font->getSize() . ' Tf';
        $coordinates = $this->getCoordinates();
        $pdfX = $coordinates->getPdfX();
        $pdfY = $coordinates->getPdfY();
        $htmlX = $coordinates->getX();
        $htmlY = $coordinates->getY();
        $baseLine = $style->getFont()->getDescender();
        $baseLineY = bcsub($pdfY, $baseLine, 4);
        $textWidth = $style->getFont()->getTextWidth($this->getText());
        $textHeight = $style->getFont()->getTextHeight();
        $textContent = '(' . $this->filterText($this->getText()) . ')';
        $element = [
            'q',
            "1 0 0 1 $pdfX $baseLineY cm % html x:$htmlX y:$htmlY",
            "{$rules['color'][0]} {$rules['color'][1]} {$rules['color'][2]} rg",
            'BT',
            $fontStr,
            "$textContent Tj",
            'ET',
            'Q'
        ];
        $this->drawTextOutline = false;
        if ($this->drawTextOutline) {
            $element = array_merge($element, [
                'q',
                '1 w',
                '1 0 0 RG',
                "1 0 0 1 $pdfX $pdfY cm",
                "0 0 $textWidth $textHeight re",
                'S',
                'Q'
            ]);
        }
        return implode("\n", $element);
    }
}
