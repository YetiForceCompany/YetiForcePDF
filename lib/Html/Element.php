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
class Element extends \YetiForcePDF\Base
{
	/**
	 * Unique internal element id
	 * @var string
	 */
	protected $elementId;
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
	 * @var \YetiForcePDF\Style\Style
	 */
	protected $style;
	/**
	 * @var null|\YetiForcePDF\Html\Element
	 */
	protected $parent;
	/**
	 * Is this root element?
	 * @var bool
	 */
	protected $root = false;
	/**
	 * Is this text node or element ?
	 * @var bool
	 */
	protected $textNode = false;
	/**
	 * @var \YetiForcePDF\Html\Element[]
	 */
	protected $children = [];
	/**
	 * @var \YetiForcePDF\Html\Element
	 */
	protected $previous;
	/**
	 * @var \YetiForcePDF\Html\Element
	 */
	protected $next;
	/**
	 * PDF graphic / text stream instructions
	 * @var string[]
	 */
	protected $instructions = [];

	/**
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		$this->elementId = uniqid();
		$this->name = $this->domElement->tagName;
		$this->style = $this->parseStyle();
		if ($this->domElement->hasChildNodes() && $this->style->getRules()['display'] !== 'none') {
			$children = [];
			foreach ($this->domElement->childNodes as $index => $childNode) {
				$childElement = $children[] = (new Element())
					->setDocument($this->document)
					->setElement($childNode)
					->setParent($this)
					->setTextNode($childNode instanceof \DOMText);
				$this->addChild($childElement);
			}
			foreach ($children as $index => $child) {
				if ($index > 0) {
					$child->setPrevious($children[$index - 1]);
				}
				if ($index + 1 < count($children) - 1) {
					$child->setNext($children[$index + 2]);
				}
				$child->init();
			}
		}
		return $this;
	}

	/**
	 * Set element
	 * @param $element
	 * @return \YetiForcePDF\Html\Element
	 */
	public function setElement($element): Element
	{
		$this->domElement = $element;
		$this->domElement->normalize();
		return $this;
	}

	/**
	 * Set parent element
	 * @param \YetiForcePDF\Html\Element $parent
	 * @return \YetiForcePDF\Html\Element
	 */
	public function setParent(Element $parent): \YetiForcePDF\Html\Element
	{
		$this->parent = $parent;
		return $this;
	}

	/**
	 * Get parent element
	 * @return null|\YetiForcePDF\Html\Element
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Set previous sibling element
	 * @param \YetiForcePDF\Html\Element $previous
	 * @return $this
	 */
	public function setPrevious(Element $previous)
	{
		$this->previous = $previous;
		return $this;
	}

	/**
	 * Get previous sibling element
	 * @return \YetiForcePDF\Html\Element|null
	 */
	public function getPrevious()
	{
		return $this->previous;
	}

	/**
	 * Set next sibling element
	 * @param \YetiForcePDF\Html\Element $next
	 * @return $this
	 */
	public function setNext(Element $next)
	{
		$this->next = $next;
		return $this;
	}

	/**
	 * Get next sibling element
	 * @return \YetiForcePDF\Html\Element|null
	 */
	public function getNext()
	{
		return $this->next;
	}

	/**
	 * Set root - is this root element?
	 * @param bool $isRoot
	 * @return \YetiForcePDF\Html\Element
	 */
	public function setRoot(bool $isRoot): \YetiForcePDF\Html\Element
	{
		$this->root = $isRoot;
		return $this;
	}

	/**
	 * Set text node status
	 * @param bool $isTextNode
	 * @return \YetiForcePDF\Html\Element
	 */
	public function setTextNode(bool $isTextNode = false): \YetiForcePDF\Html\Element
	{
		$this->textNode = $isTextNode;
		return $this;
	}

	/**
	 * Is this text node? or element
	 * @return bool
	 */
	public function isTextNode(): bool
	{
		return $this->textNode;
	}

	/**
	 * Is this root element?
	 * @return bool
	 */
	public function isRoot(): bool
	{
		return $this->root;
	}

	/**
	 * Get element internal unique id
	 * @return string
	 */
	public function getElementId(): string
	{
		return $this->elementId;
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
	 * @return \YetiForcePDF\Style\Style
	 */
	protected function parseStyle(): \YetiForcePDF\Style\Style
	{
		$styleStr = null;
		$parentStyle = null;
		if ($this->parent !== null) {
			$parentStyle = $this->parent->getStyle();
		}
		if ($this->domElement instanceof \DOMElement && $this->domElement->hasAttribute('style')) {
			$styleStr = $this->domElement->getAttribute('style');
		}
		return (new \YetiForcePDF\Style\Style())
			->setDocument($this->document)
			->setElement($this)
			->setContent($styleStr)
			->setParent($parentStyle)
			->init();
	}

	/**
	 * Get element style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getStyle(): \YetiForcePDF\Style\Style
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
		$font = $this->style->getFont();
		$fontStr = '/' . $font->getNumber() . ' ' . $font->getSize() . ' Tf';
		$coordinates = $this->style->getCoordinates();
		$pdfX = $coordinates->getAbsolutePdfX();
		$pdfY = $coordinates->getAbsolutePdfY();
		$htmlX = $coordinates->getAbsoluteHtmlX();
		$htmlY = $coordinates->getAbsoluteHtmlY();
		$dimensions = $this->style->getDimensions();
		$width = $dimensions->getWidth();
		$height = $dimensions->getHeight();
		if ($this->isTextNode()) {
			$textContent = $this->getDOMElement()->textContent;
			$element = [
				'BT',
				$fontStr,
				"1 0 0 1 $pdfX $pdfY Tm % html $htmlX $htmlY",
				"($textContent) Tj",
				'ET',
			];
		} else {
			$element = [
				'q',
				'1 w', //border
				'0 0 0 RG',
				"1 0 0 1 $pdfX $pdfY cm % html $htmlX $htmlY",
				"0 0 $width $height re",
				'S',
				'Q',
			];
		}
		return implode("\n", $element);
	}
}
