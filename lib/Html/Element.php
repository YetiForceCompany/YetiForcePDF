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
	 * @var Box
	 */
	protected $box;
	/**
	 * @var Box
	 */
	protected $parentBox;
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
		return $this;
	}

	/**
	 * Set box for this element (element is always inside box)
	 * @param \YetiForcePDF\Html\Box $box
	 * @return $this
	 */
	public function setBox($box)
	{
		$this->box = $box;
		return $this;
	}

	/**
	 * Get box
	 * @return \YetiForcePDF\Html\Box
	 */
	public function getBox()
	{
		return $this->box;
	}

	/**
	 * Set parent box (only for style computation! elsewhere use getParent method!)
	 * @param $box
	 * @return $this
	 */
	public function setParentBox($box)
	{
		$this->parentBox = $box;
		return $this;
	}

	/**
	 * Get parent Element
	 * @return Element|null
	 */
	public function getParentBox()
	{
		return $this->parentBox;
	}

	/**
	 * Get parent element (from parent box)
	 * @return mixed
	 */
	public function getParent()
	{
		if ($parentBox = $this->box->getParent()) {
			return $parentBox->getElement();
		}
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
		$this->addChild($element);
		$element->init();
		return $element;
	}

	/**
	 * Set dom element (only for parsing dom tree - domElement should not be used anywhere else)
	 * @param $element
	 * @return \YetiForcePDF\Html\Element
	 */
	public function setDOMElement($element): Element
	{
		$this->domElement = $element;
		return $this;
	}

	/**
	 * Get dom element
	 * @return \DOMElement
	 */
	public function getDOMElement()
	{
		return $this->domElement;
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
	public function parseStyle(): \YetiForcePDF\Style\Style
	{
		$styleStr = null;
		if ($this->domElement instanceof \DOMElement && $this->domElement->hasAttribute('style')) {
			$styleStr = $this->domElement->getAttribute('style');
		}
		$parentStyle = null;
		if ($parentBox = $this->getParentBox()) {
			$parentStyle = $parentBox->getStyle();
		}
		$style = (new \YetiForcePDF\Style\Style())
			->setDocument($this->document)
			->setElement($this)
			->setContent($styleStr);
		if ($parentStyle) {
			$style->setParentStyle($parentStyle);
		}
		return $style->init();
	}

	/**
	 * Get element style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getStyle(): \YetiForcePDF\Style\Style
	{
		return $this->style;
	}

}
