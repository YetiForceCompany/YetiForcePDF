<?php
declare(strict_types=1);
/**
 * BoxDimensions class
 *
 * @package   YetiForcePDF\Html
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Html;

/**
 * Class BoxDimensions
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
	 * @var string
	 */
	protected $text;

	/**
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		parent::init();
		$this->elementId = uniqid();
		$this->name = $this->domElement->tagName;
		$this->style = $this->parseStyle();
		if ($this->domElement && $this->domElement->hasChildNodes() && $this->style->getRules('display') !== 'none') {
			$children = [];
			// basing on dom children elements automatically setup nested instances of BoxDimensions (pdf)
			foreach ($this->domElement->childNodes as $index => $childNode) {
				$isTextNode = $childNode instanceof \DOMText;
				// if element already exists for this domNode use it (in case current element is moved and initialised again)
				$childElement = $children[] = (new Element())
					->setDocument($this->document)
					->setElement($childNode)
					->setParent($this)// setParent will remove this element from previous parent if exists
					->setTextNode($isTextNode);
				$this->addChild($childElement);
				if ($isTextNode) {
					$childElement->setText($childNode->textContent);
				}
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
	 * Create and append text node to parent element
	 * @param string $text
	 * @return Element
	 */
	public function createTextNode(string $text)
	{
		$element = (new Element())
			->setDocument($this->document)
			->setParent($this)
			->setTextNode(true)
			->setText($text);
		if ($previous = $this->getLastChild()) {
			$element->setPrevious($previous);
			$previous->setNext($element);
		}
		$element->init();
		$this->addChild($element);
		return $element;
	}

	/**
	 * Set element
	 * @param $element
	 * @return \YetiForcePDF\Html\Element
	 */
	public function setElement($element): Element
	{
		$this->domElement = $element;
		return $this;
	}

	/**
	 * Remove child element
	 * @param \YetiForcePDF\Html\Element $child
	 * @return $this
	 */
	public function removeChild(Element $child)
	{
		$oldChildren = $this->children;
		$this->children = [];
		foreach ($oldChildren as $currentChild) {
			if ($currentChild !== $child) {
				$this->children[] = $currentChild;
			}
		}
		return $this;
	}

	/**
	 * Set parent element
	 * @param \YetiForcePDF\Html\Element $parent
	 * @return \YetiForcePDF\Html\Element
	 */
	public function setParent(Element $parent = null)
	{
		if ($this->getParent() !== null) {
			$this->getParent()->removeChild($this);
		}
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
	public function setPrevious(Element $previous = null)
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
	public function setNext(Element $next = null)
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
	public function setRoot(bool $isRoot): Element
	{
		$this->root = $isRoot;
		return $this;
	}

	/**
	 * Set text node status
	 * @param bool $isTextNode
	 * @return \YetiForcePDF\Html\Element
	 */
	public function setTextNode(bool $isTextNode = false): Element
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
	 * Parse element style
	 * @return \YetiForcePDF\Style\Style
	 */
	protected function parseStyle(): \YetiForcePDF\Style\Style
	{
		$styleStr = null;
		if ($this->domElement instanceof \DOMElement && $this->domElement->hasAttribute('style')) {
			$styleStr = $this->domElement->getAttribute('style');
		}
		return (new \YetiForcePDF\Style\Style())
			->setDocument($this->document)
			->setElement($this)
			->setContent($styleStr)
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
	 * Do element have children?
	 * @return bool
	 */
	public function hasChildren()
	{
		return count($this->children) > 0;
	}

	/**
	 * Get all children (recursive)
	 * @param Element[] $current
	 * @return Element[]
	 */
	public function getAllChildren(array $current = [])
	{
		foreach ($this->getChildren() as $child) {
			$current[] = $child;
			$child->getAllChildren($current);
		}
		return $current;
	}

	/**
	 * Get last children
	 * @return \YetiForcePDF\Html\Element|null
	 */
	public function getLastChild()
	{
		$children = $this->getChildren();
		if ($count = count($children)) {
			return $children[$count - 1];
		}
	}

}
