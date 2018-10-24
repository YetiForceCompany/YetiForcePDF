<?php
declare(strict_types=1);
/**
 * ElementBox class
 *
 * @package   YetiForcePDF\Render
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render;

use \YetiForcePDF\Render\Coordinates\Coordinates;
use \YetiForcePDF\Render\Coordinates\Offset;
use \YetiForcePDF\Render\Dimensions\BoxDimensions;
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
				if ($child->getTextContent() === '') {
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
				if ($display === 'block') {
					$this->appendBlock($childDomElement, $element, $style, $parentBlock);
					continue;
				}
				if ($display === 'inline') {
					$inline = $this->appendInline($childDomElement, $element, $style, $parentBlock);
					if ($childDomElement instanceof \DOMText) {
						$inline->setAnonymous(true)->appendText($childDomElement, null, null, $parentBlock);
						continue;
					}
					continue;
				}
				if ($display === 'inline-block') {
					$this->appendInlineBlock($childDomElement, $element, $style, $parentBlock);
				}
			}
		}
		$this->removeEmptyLines();
		return $this;
	}
}
