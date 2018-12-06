<?php

declare(strict_types=1);
/**
 * FooterBox class.
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use YetiForcePDF\Html\Element;
use YetiForcePDF\Layout\Coordinates\Coordinates;
use YetiForcePDF\Layout\Coordinates\Offset;
use YetiForcePDF\Layout\Dimensions\BoxDimensions;
use YetiForcePDF\Math;
use YetiForcePDF\Style\Style;

/**
 * Class FooterBox.
 */
class FooterBox extends BlockBox
{
    /**
     * {@inheritdoc}
     */
    protected $absolute = true;

    /**
     * {@inheritdoc}
     */
    public function measureWidth()
    {
        if (!$this->isRenderable()) {
            return $this;
        }
        $horizontalMargins = $this->getStyle()->getHorizontalMarginsWidth();
        $pageWidth = $this->document->getCurrentPage()->getOuterDimensions()->getWidth();
        $width = Math::sub($pageWidth, $horizontalMargins);
        $this->getDimensions()->setWidth($width);
        $this->applyStyleWidth();
        foreach ($this->getChildren() as $child) {
            $child->measureWidth();
        }
        $this->divideLines();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function measureHeight()
    {
        if (!$this->isRenderable()) {
            return $this;
        }
        return parent::measureHeight();
    }

    /**
     * {@inheritdoc}
     */
    public function measureOffset()
    {
        if (!$this->isRenderable()) {
            return $this;
        }
        $top = Math::sub($this->document->getCurrentPage()->getDimensions()->getHeight(), $this->getDimensions()->getHeight());
        $top = Math::sub($top, $this->getStyle()->getRules('marin-bottom'));
        $left = '0';
        $left = Math::add($left, $this->getStyle()->getRules('margin-left'));
        $this->getOffset()->setTop($top);
        $this->getOffset()->setLeft($left);
        foreach ($this->getChildren() as $child) {
            $child->measureOffset();
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function measurePosition()
    {
        if (!$this->isRenderable()) {
            return $this;
        }
        $top = Math::sub($this->document->getCurrentPage()->getDimensions()->getHeight(), $this->getDimensions()->getHeight());
        $top = Math::sub($top, $this->getStyle()->getRules('marin-bottom'));
        $left = '0';
        $left = Math::add($left, $this->getStyle()->getRules('margin-left'));
        $this->getCoordinates()->setX($left)->setY($top);
        foreach ($this->getChildren() as $child) {
            $child->measurePosition();
        }
        return $this;
    }
}
