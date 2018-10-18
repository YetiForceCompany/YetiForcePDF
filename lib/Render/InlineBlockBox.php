<?php
declare(strict_types=1);
/**
 * InlineBlockBox class
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
 * Class InlineBlockBox
 */
class InlineBlockBox extends Box
{

	use ElementBoxTrait;

	/**
	 * Append block box element
	 * @param \DOMNode                           $childDomElement
	 * @param Element                            $element
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
	 * @return $this
	 */
	public function appendBlock($childDomElement, $element, $parentBlock)
	{
		$box = (new BlockBox())
			->setDocument($this->document)
			->setElement($element)
			->setStyle($element->parseStyle())//second phase with css inheritance
			->init();
		$this->appendChild($box);
		$box->buildTree($box);
		return $this;
	}

	/**
	 * Append inline block box element
	 * @param \DOMNode                           $childDomElement
	 * @param Element                            $element
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
	 * @return $this
	 */
	public function appendInlineBlock($childDomElement, $element, $parentBlock)
	{
		$box = (new InlineBlockBox())
			->setDocument($this->document)
			->setElement($element)
			->setStyle($element->parseStyle())//second phase with css inheritance
			->init();
		// if we add this child to parent box we loose parent inline styles if nested
		// so we need to wrap this box later and split lines at block element
		if (isset($currentChildren[0])) {
			$this->cloneParent($box);
		} else {
			$this->appendChild($box);
		}
		$box->buildTree($box);
		return $this;
	}

	/**
	 * Add inline child (and split text to individual characters)
	 * @param \DOMNode                           $childDomElement
	 * @param Element                            $element
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
	 * @return $this
	 */
	public function appendInline($childDomElement, $element, $parentBlock)
	{
		$childDomElements = [$childDomElement];
		if ($childDomElement instanceof \DOMText) {
			$childDomElements = [];
			$text = $childDomElement->textContent;
			$words = explode(' ', $text);
			$wordsCount = count($words);
			foreach ($words as $index => $word) {
				if ($index < $wordsCount - 1) {
					$word .= ' ';
				}
				$childDomElements[] = $childDomElement->ownerDocument->createTextNode($word);
			}
		}
		foreach ($childDomElements as $childDomElementText) {
			$element = (new Element())
				->setDocument($this->document)
				->setDOMElement($childDomElementText)
				->init();
			$box = (new InlineBox())
				->setDocument($this->document)
				->setElement($element)
				->setStyle($element->parseStyle())
				->init();
			$currentChildren = $this->getChildren();
			if (isset($currentChildren[0])) {
				$this->cloneParent($box);
			} else {
				$this->appendChild($box);
			}
			if ($childDomElementText instanceof \DOMText) {
				$box->setTextNode(true)->setText($childDomElementText->textContent);
			} else {
				$box->buildTree($parentBlock);
			}
		}
		return $this;
	}

	/**
	 * Build tree
	 * @param \YetiForcePDF\Render\BlockBox|null $parentBlock
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
				$display = $style->getRules('display');
				if ($display === 'block') {
					$this->appendBlock($childDomElement, $element, $parentBlock);
					continue;
				}
				if ($display === 'inline') {
					$this->appendInline($childDomElement, $element, $parentBlock);
					continue;
				}
				if ($display === 'inline-block') {
					$this->appendInlineBlock($childDomElement, $element, $parentBlock);
				}
			}
		}
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
