<?php
declare(strict_types=1);
/**
 * Element class
 *
 * @package   YetiForcePDF\Html
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Html;

/**
 * Class Element
 */
class Element
{
	/**
	 * DOMElement tagName
	 * @var string
	 */
	protected $name;
	/**
	 * @var \YetiForcePDF\Document
	 */
	protected $document;
	/**
	 * @var \DOMElement
	 */
	protected $domElement;
	/**
	 * @var \YetiForcePDF\Html\Style
	 */
	protected $style;
	/**
	 * @var null|\YetiForcePDF\Html\Element
	 */
	protected $parent;
	/**
	 * @var \YetiForcePDF\Html\Element[]
	 */
	protected $children = [];
	/**
	 * PDF graphic / text stream instructions
	 * @var string[]
	 */
	protected $instructions = [];

	/**
	 * Element constructor.
	 * @param \YetiForcePDF\Document $document
	 * @param \DOMElement|\DOMText   $element
	 */
	public function __construct(\YetiForcePDF\Document $document, $element, Element $parent = null)
	{
		$this->document = $document;
		$this->domElement = $element;
		$this->domElement->normalize();
		$this->parent = $parent;
		$this->name = $element->tagName;
		$this->style = $this->parseStyle();
		if ($this->domElement->hasChildNodes()) {
			foreach ($this->domElement->childNodes as $childNode) {
				$childElement = new Element($this->document, $childNode, $this);
				$this->addChild($childElement);
			}
		}
	}

	/**
	 * Get dom element
	 * @return \DOMElement|\DOMText
	 */
	public function getDOMElement()
	{
		return $this->domElement;
	}

	/**
	 * Parse element style
	 * @return \YetiForcePDF\Html\Style
	 */
	protected function parseStyle(): \YetiForcePDF\Html\Style
	{
		$styleStr = null;
		$parentStyle = null;
		if ($this->parent !== null) {
			$parentStyle = $this->parent->getStyle();
		}
		if ($this->domElement instanceof \DOMElement && $this->domElement->hasAttribute('style')) {
			$styleStr = $this->domElement->getAttribute('style');
		}
		return new \YetiForcePDF\Html\Style($this->document, $styleStr, $parentStyle);
	}

	/**
	 * Get element style
	 * @return \YetiForcePDF\Html\Style
	 */
	public function getStyle(): \YetiForcePDF\Html\Style
	{
		return $this->style;
	}

	/**
	 * Add child element
	 * @param \YetiForcePDF\Html\Element $child
	 * @return \YetiForcePDF\Html\Element
	 */
	public function addChild(\YetiForcePDF\Html\Element $child): \YetiForcePDF\Html\Element
	{
		$this->children[] = $child;
		return $this;
	}

	/**
	 * Get child elements
	 * @return \YetiForcePDF\Html\Element[]
	 */
	public function getChildren(): array
	{
		return $this->children;
	}

	/**
	 * Get element PDF instructions to use in content stream
	 * @return string
	 */
	public function getInstructions(): string
	{

	}
}
