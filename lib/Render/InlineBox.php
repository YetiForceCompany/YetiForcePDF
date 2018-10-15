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
class InlineBox extends BlockBox
{

	/**
	 * Get closest parent block
	 * @return \YetiForcePDF\Render\Box
	 */
	public function getParentBlock()
	{
		return $this->getParent()->getParent();
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
				if ($parentBlock->getCurrentLineBox()) {
					$currentLineBox = $parentBlock->getCurrentLineBox();
				} else {
					$currentLineBox = $parentBlock->getNewLineBox();
				}
				$currentLineBox->appendChild($box);
				$box->buildTree($parentBlock);
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
