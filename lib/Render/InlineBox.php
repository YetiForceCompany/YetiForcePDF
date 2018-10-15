<?php
declare(strict_types=1);
/**
 * InlineBox class
 *
 * @package   YetiForcePDF\Render
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render;

use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;
use \YetiForcePDF\Render\Coordinates\Coordinates;
use \YetiForcePDF\Render\Coordinates\Offset;
use \YetiForcePDF\Render\Dimensions\BoxDimensions;

/**
 * Class InlineBox
 */
class InlineBox extends Box
{

	/**
	 * @var Element
	 */
	protected $element;
	/**
	 * @var Style
	 */
	protected $style;
	/**
	 * @var string
	 */
	protected $text;

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
	 * Set style
	 * @param \YetiForcePDF\Style\Style $style
	 * @return $this
	 */
	public function setStyle(Style $style)
	{
		$this->style = $style;
		return $this;
	}

	/**
	 * Get style
	 * @return Style
	 */
	public function getStyle()
	{
		return $this->style;
	}

	/**
	 * Get closest parent block
	 * @return \YetiForcePDF\Render\Box
	 */
	public function getParentBlock()
	{
		return $this->getParent()->getParent();
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
	 * Go up to Line box and clone and wrap element
	 * @param $box
	 */
	public function cloneParent($box)
	{
		if ($parent = $this->getParent()) {
			$clone = clone $this;
			$clone->appendChild($box);
			if (!$parent instanceof LineBox) {
				$parent->cloneParent($clone);
			} else {
				$parent->appendChild($clone);
			}
		}
	}

	/**
	 * Segregate
	 * @param $parentBlock
	 * @return $this
	 */
	public function buildTree($parentBlock = null)
	{
		$domElement = $this->getElement()->getDOMElement();
		if ($domElement->hasChildNodes()) {
			foreach ($domElement->childNodes as $childDomElement) {
				$element = (new Element())
					->setDocument($this->document)
					->setDOMElement($childDomElement)
					->init();
				$style = $element->parseStyle();
				if ($style->getRules('display') === 'block') {
					if ($parentBlock->getCurrentLineBox()) {
						$parentBlock->closeLine();
					}
					$box = (new BlockBox())
						->setDocument($this->document)
						->setElement($childDomElement)
						->setStyle($element->parseStyle())//second phase with css inheritance
						->init();
					// if we add this child to parent box we loose parent inline styles if nested
					// so we need to wrap this box later and split lines at block element
					$this->appendChild($box);
					$box->buildTree($parentBlock);
					continue;
				}
				// childDomElement is an inline element
				$box = (new InlineBox())
					->setDocument($this->document)
					->setElement($element)
					->setStyle($element->parseStyle())
					->init();
				$currentChildren = $this->getChildren();
				if (isset($currentChildren[0])) { // faster than count
					$this->cloneParent($box);
				} else {
					$this->appendChild($box);
				}
				if ($childDomElement instanceof \DOMText) {
					$box->setTextNode(true)->setText($childDomElement->textContent);
				} else {
					$box->buildTree($parentBlock);
				}
			}
		}
		return $this;
	}

	/**
	 * Measure width
	 * @return $this
	 */
	public function measureWidth()
	{
		$width = 0;
		foreach ($this->getChildren() as $child) {
			$child->measureWidth();
			$width += $child->getDimensions()->getOuterWidth();
		}
		if ($this->isTextNode()) {
			$width = $this->getStyle()->getFont()->getTextWidth($this->getText());
		}
		$this->getDimensions()->setWidth($width);
		return $this;
	}

	/**
	 * Reflow
	 * @return $this
	 */
	public function reflow()
	{
		$this->measureWidth();
		return $this;
	}

	public function __clone()
	{
		$this->element = clone $this->element;
		$this->style = clone $this->style;
		$this->offset = clone $this->offset;
		$this->dimensions = clone $this->dimensions;
		$this->coordinates = clone $this->coordinates;
		$this->children = [];
	}
}
