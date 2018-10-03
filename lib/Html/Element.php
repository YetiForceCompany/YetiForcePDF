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
	 * @var bool Is this element just line? (wrapper for other elements)
	 */
	protected $line = false;
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
	 * Element row - needed to calculate parent height
	 * If element was moved to the next line it will have row>0
	 * @var int
	 */
	protected $row = 0;
	/**
	 * Element column = element position in row
	 * @var int
	 */
	protected $column = 0;
	/**
	 * PDF graphic / text stream instructions
	 * @var string[]
	 */
	protected $instructions = [];
	/**
	 * Just for debugging purposes
	 * @var bool
	 */
	protected $drawTextOutline = false;

	/**
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		parent::init();
		$this->elementId = uniqid();
		$this->name = $this->domElement->tagName;
		if ($this->style === null) {
			$this->style = $this->parseStyle();
		}
		if ($this->domElement->hasChildNodes() && $this->style->getRules('display') !== 'none') {
			$children = [];
			// basing on dom children elements automatically setup nested instances of Element (pdf)
			foreach ($this->domElement->childNodes as $index => $childNode) {
				// if element already exists for this domNode use it (in case current element is moved and initialised again)
				$childElement = $children[] = $this->elementForNode($childNode)
					->setDocument($this->document)
					->setElement($childNode)
					->setParent($this)// setParent will remove this element from previous parent if exists
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
	 * Get or create element for DOMNode
	 * @param \DOMNode $domNode
	 * @return \YetiForcePDF\Html\Element
	 */
	public function elementForNode(\DOMNode $domNode)
	{
		foreach ($this->document->getRootElement()->getAllChildren() as $child) {
			if ($child->getDOMElement() === $domNode) {
				return $child;
			}
		}
		return new Element();
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
	 * Remove child
	 * @param \YetiForcePDF\Html\Element $child
	 * @return $this
	 */
	public function removeChild(Element $child)
	{
		$this->children = array_filter($this->children, function ($current) use ($child) {
			return $current !== $child;
		});
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
	 * Is element just a line? (wrapper for other elements in layout)
	 * @return bool
	 */
	public function isLine()
	{
		return $this->line;
	}

	/**
	 * Set this element as line (wrapper for other elements in layout)
	 * @param bool $line
	 */
	public function setLine(bool $line)
	{
		$this->line = $line;
	}

	/**
	 * Create line element with children
	 * @param Element[] $children
	 * @return Element|false
	 */
	public function createLine(array $children)
	{
		if (empty($children)) {
			return false;
		}
		return $this->wrapElements($children)->setLine(true);
	}

	/**
	 * Get lines (or line) of elements
	 * @param int|null $lineIndex
	 * @return Element[]|Element
	 */
	public function getLines(int $lineIndex = null)
	{
		$lines = [];
		foreach ($this->getChildren() as $child) {
			if ($child->isLine()) {
				$lines[] = $child;
			}
		}
		if ($lineIndex !== null) {
			return $lines[$lineIndex];
		}
		return $lines;
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
		if (!in_array($child, $this->children)) {
			$this->children[] = $child;
		}
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
	 * Get dom document
	 * @return \DOMDocument
	 */
	public function getDomDocument()
	{
		return $this->getDOMElement()->ownerDocument;
	}

	/**
	 * Wrap elements
	 * @param Element[] $elements
	 * @return \YetiForcePDF\Html\Element
	 *
	 * @throws \InvalidArgumentException
	 */
	public function wrapElements(array $elements)
	{
		if (empty($elements)) {
			return false;
		}
		foreach ($elements as $element) {
			if (!in_array($element, $this->children)) {
				throw new \InvalidArgumentException('Only children elements could be wrapped.');
			}
		}
		$previous = $elements[0]->getPrevious();
		$next = $elements[count($elements) - 1]->getNext();
		$domDocument = $this->getDomDocument();
		$domElement = $domDocument->createElement('div');
		if ($next) {
			$this->getDOMElement()->insertBefore($domElement, $next->getDOMElement());
		} else {
			$this->getDOMElement()->appendChild($domElement);
		}
		foreach ($elements as $element) {
			$domElement->appendChild($this->getDOMElement()->removeChild($element->getDOMElement()));
		}
		$wrapper = (new Element())
			->setDocument($this->document)
			->setParent($this)
			->setElement($domElement);
		if ($previous) {
			$wrapper->setPrevious($previous);
		}
		if ($next) {
			$wrapper->setNext($next);
		}
		$wrapper->init();
		$wrapper->getStyle()->initDimensions()->initCoordinates()->calculateWidths();
		return $wrapper;
	}

	/**
	 * Get text content
	 * @param bool $currentNodeOnly - do not retrieve children text nodes concatenated
	 * @return string
	 */
	public function getText(bool $currentNodeOnly = true)
	{
		if (!$currentNodeOnly) {
			return $this->domElement->textContent;
		}
		$childrenText = '';
		foreach ($this->getChildren() as $child) {
			if (!$child->isTextNode()) {
				$childrenText .= $child->getText(false);
			}
		}
		return mb_substr($this->domElement->textContent, 0, mb_strlen($this->domElement->textContent) - mb_strlen($childrenText));
	}

	/**
	 * Filter text
	 * Filter the text, this is applied to all text just before being inserted into the pdf document
	 * it escapes the various things that need to be escaped, and so on
	 *
	 * @return string
	 */
	protected function filterText($text)
	{
		$text = trim(preg_replace('/[\n\r\t\s]+/', ' ', mb_convert_encoding($text, 'UTF-8')));
		$text = preg_replace('/\s+/', ' ', $text);
		$text = mb_convert_encoding($text, 'UTF-16');
		return strtr($text, [')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r']);
	}

	/**
	 * Add border instructions
	 * @param array $element
	 * @param float $pdfX
	 * @param float $pdfY
	 * @param float $width
	 * @param float $height
	 * @return array
	 */
	protected function addBorderInstructions(array $element, float $pdfX, float $pdfY, float $width, float $height)
	{
		$rules = $this->style->getRules();
		$x1 = 0;
		$x2 = $width;
		$y1 = $height;
		$y2 = 0;
		$element[] = '% start border';
		if ($rules['border-top-width'] && $rules['border-top-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x2, $y1]),
				implode(' ', [$x2 - $rules['border-right-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x1 + $rules['border-left-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x1, $y1])
			]);
			$borderTop = [
				'q',
				"{$rules['border-top-color'][0]} {$rules['border-top-color'][1]} {$rules['border-top-color'][2]} rg",
				"1 0 0 1 $pdfX $pdfY cm",
				"$x1 $y1 m", // move to start point
				$path . ' l h',
				'F',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		if ($rules['border-right-width'] && $rules['border-right-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x2, $y2]),
				implode(' ', [$x2 - $rules['border-right-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x2 - $rules['border-right-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x2, $y1]),
			]);
			$borderTop = [
				'q',
				"1 0 0 1 $pdfX $pdfY cm",
				"{$rules['border-right-color'][0]} {$rules['border-right-color'][1]} {$rules['border-right-color'][2]} rg",
				"$x2 $y1 m",
				$path . ' l h',
				'F',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		if ($rules['border-bottom-width'] && $rules['border-bottom-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x2, $y2]),
				implode(' ', [$x2 - $rules['border-right-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x1 + $rules['border-left-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x1, $y2]),
			]);
			$borderTop = [
				'q',
				"1 0 0 1 $pdfX $pdfY cm",
				"{$rules['border-bottom-color'][0]} {$rules['border-bottom-color'][1]} {$rules['border-bottom-color'][2]} rg",
				"$x1 $y2 m",
				$path . ' l h',
				'F',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		if ($rules['border-left-width'] && $rules['border-left-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x1 + $rules['border-left-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x1 + $rules['border-left-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x1, $y2]),
				implode(' ', [$x1, $y1]),
			]);
			$borderTop = [
				'q',
				"1 0 0 1 $pdfX $pdfY cm",
				"{$rules['border-left-color'][0]} {$rules['border-left-color'][1]} {$rules['border-left-color'][2]} rg",
				"$x1 $y1 m",
				$path . ' l h',
				'F',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		$element[] = '% end border';
		return $element;
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
		$textWidth = $this->style->getFont()->getTextWidth($this->getDOMElement()->textContent);
		$textHeight = $this->style->getFont()->getTextHeight();
		$baseLine = $this->style->getFont()->getDescender();
		$baseLineY = $pdfY - $baseLine;
		if ($this->isTextNode()) {
			$textContent = '(' . $this->filterText($this->getDOMElement()->textContent) . ')';
			$element = [
				'q',
				"1 0 0 1 $pdfX $baseLineY cm % html x:$htmlX y:$htmlY",
				'BT',
				$fontStr,
				"$textContent Tj",
				'ET',
				'Q'
			];
			if ($this->drawTextOutline) {
				$element = array_merge($element, [
					'q',
					'1 w',
					'1 0 0 RG',
					"1 0 0 1 $pdfX $pdfY cm",
					"0 0 $textWidth $textHeight re",
					'S',
					'Q'
				]);
			}
		} else {
			$element = [];
			$element = $this->addBorderInstructions($element, $pdfX, $pdfY, $width, $height);
		}
		return implode("\n", $element);
	}
}
