<?php
declare(strict_types=1);
/**
 * Element class
 *
 * @package   YetiPDF\Html
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Html;

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
	 * @var \YetiPDF\Document
	 */
	protected $document;
	/**
	 * @var \DOMElement
	 */
	protected $domElement;
	/**
	 * @var \YetiPDF\Html\Style
	 */
	protected $style;
	/**
	 * @var null|\YetiPDF\Html\Element
	 */
	protected $parent;
	/**
	 * @var \YetiPDF\Html\Element
	 */
	protected $children = [];

	/**
	 * Element constructor.
	 * @param \YetiPDF\Document    $document
	 * @param \DOMElement|\DOMText $element
	 */
	public function __construct(\YetiPDF\Document $document, $element, Element $parent = null)
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
	 * @return \YetiPDF\Html\Style
	 */
	public function parseStyle(): \YetiPDF\Html\Style
	{
		$styleStr = null;
		$parentStyle = null;
		if ($this->parent !== null) {
			$parentStyle = $this->parent->getStyle();
		}
		if ($this->domElement instanceof \DOMElement && $this->domElement->hasAttribute('style')) {
			$styleStr = $this->domElement->getAttribute('style');
		}
		return new \YetiPDF\Html\Style($this->document, $styleStr, $parentStyle);
	}

	/**
	 * Get element style
	 * @return \YetiPDF\Html\Style
	 */
	public function getStyle(): \YetiPDF\Html\Style
	{
		return $this->style;
	}

	/**
	 * Add child element
	 * @param \YetiPDF\Html\Element $child
	 * @return \YetiPDF\Html\Element
	 */
	public function addChild(\YetiPDF\Html\Element $child): \YetiPDF\Html\Element
	{
		$this->children[] = $child;
		return $this;
	}


	public function parse()
	{
		$textStream = new \YetiPDF\Objects\TextStream($this->document);
		$text = '';
		foreach ($this->children as $child) {
			$childDomElement = $child->getDomElement();
			if ($childDomElement instanceof \DOMText) {
				$text .= $childDomElement->textContent;
			}
		}
		$textStream->setFont($this->style->getFont());
		$textStream->setText($text);
		$textStream->setFontSize(12);
		$textStream->setX(10);
		$textStream->setY(10);
		$this->document->getCurrentPage()->addContentStream($textStream);
	}
}
