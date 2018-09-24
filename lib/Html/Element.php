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
	 * return array containing codepoints (UTF-8 character values) for the
	 * string passed in.
	 *
	 * based on the excellent TCPDF code by Nicola Asuni and the
	 * RFC for UTF-8 at http://www.faqs.org/rfcs/rfc3629.html
	 *
	 * @access private
	 * @author Orion Richardson
	 * @since  January 5, 2008
	 *
	 * @param string $text UTF-8 string to process
	 *
	 * @return array UTF-8 codepoints array for the string
	 */
	function utf8toCodePointsArray(&$text)
	{
		$length = mb_strlen($text, '8bit'); // http://www.php.net/manual/en/function.mb-strlen.php#77040
		$unicode = []; // array containing unicode values
		$bytes = []; // array containing single character byte sequences
		$numbytes = 1; // number of octets needed to represent the UTF-8 character

		for ($i = 0; $i < $length; $i++) {
			$c = ord($text[$i]); // get one string character at time
			if (count($bytes) === 0) { // get starting octect
				if ($c <= 0x7F) {
					$unicode[] = $c; // use the character "as is" because is ASCII
					$numbytes = 1;
				} elseif (($c >> 0x05) === 0x06) { // 2 bytes character (0x06 = 110 BIN)
					$bytes[] = ($c - 0xC0) << 0x06;
					$numbytes = 2;
				} elseif (($c >> 0x04) === 0x0E) { // 3 bytes character (0x0E = 1110 BIN)
					$bytes[] = ($c - 0xE0) << 0x0C;
					$numbytes = 3;
				} elseif (($c >> 0x03) === 0x1E) { // 4 bytes character (0x1E = 11110 BIN)
					$bytes[] = ($c - 0xF0) << 0x12;
					$numbytes = 4;
				} else {
					// use replacement character for other invalid sequences
					$unicode[] = 0xFFFD;
					$bytes = [];
					$numbytes = 1;
				}
			} elseif (($c >> 0x06) === 0x02) { // bytes 2, 3 and 4 must start with 0x02 = 10 BIN
				$bytes[] = $c - 0x80;
				if (count($bytes) === $numbytes) {
					// compose UTF-8 bytes to a single unicode value
					$c = $bytes[0];
					for ($j = 1; $j < $numbytes; $j++) {
						$c += ($bytes[$j] << (($numbytes - $j - 1) * 0x06));
					}
					if ((($c >= 0xD800) AND ($c <= 0xDFFF)) OR ($c >= 0x10FFFF)) {
						// The definition of UTF-8 prohibits encoding character numbers between
						// U+D800 and U+DFFF, which are reserved for use with the UTF-16
						// encoding form (as surrogate pairs) and do not directly represent
						// characters.
						$unicode[] = 0xFFFD; // use replacement character
					} else {
						$unicode[] = $c; // add char to array
					}
					// reset data for next char
					$bytes = [];
					$numbytes = 1;
				}
			} else {
				// use replacement character for other invalid sequences
				$unicode[] = 0xFFFD;
				$bytes = [];
				$numbytes = 1;
			}
		}

		return $unicode;
	}

	/**
	 * convert UTF-8 to UTF-16 with an additional byte order marker
	 * at the front if required.
	 *
	 * based on the excellent TCPDF code by Nicola Asuni and the
	 * RFC for UTF-8 at http://www.faqs.org/rfcs/rfc3629.html
	 *
	 * @access private
	 * @author Orion Richardson
	 * @since  January 5, 2008
	 *
	 * @param string  $text UTF-8 string to process
	 * @param boolean $bom  whether to add the byte order marker
	 *
	 * @return string UTF-16 result string
	 */
	function utf8toUtf16BE(&$text, $bom = false)
	{
		$out = $bom ? "\xFE\xFF" : '';

		$unicode = $this->utf8toCodePointsArray($text);
		foreach ($unicode as $c) {
			if ($c === 0xFFFD) {
				$out .= "\xFF\xFD"; // replacement character
			} elseif ($c < 0x10000) {
				$out .= chr($c >> 0x08) . chr($c & 0xFF);
			} else {
				$c -= 0x10000;
				$w1 = 0xD800 | ($c >> 0x10);
				$w2 = 0xDC00 | ($c & 0x3FF);
				$out .= chr($w1 >> 0x08) . chr($w1 & 0xFF) . chr($w2 >> 0x08) . chr($w2 & 0xFF);
			}
		}

		return $out;
	}

	/**
	 * filter the text, this is applied to all text just before being inserted into the pdf document
	 * it escapes the various things that need to be escaped, and so on
	 *
	 * @access private
	 *
	 * @param      $text
	 * @param bool $bom
	 * @param bool $convert_encoding
	 * @return string
	 */
	function filterText($text, $bom = false, $convert_encoding = false)
	{
		/**if ($convert_encoding) {
		 * $cf = $this->currentFont;
		 * if (isset($this->fonts[$cf]) && $this->fonts[$cf]['isUnicode']) {
		 * $text = $this->utf8toUtf16BE($text, $bom);
		 * } else {
		 * //$text = html_entity_decode($text, ENT_QUOTES);
		 * $text = mb_convert_encoding($text, self::$targetEncoding, 'UTF-8');
		 * }
		 * } else if ($bom) {
		 */
		$text = mb_convert_encoding($text, 'UTF-16');
		//}

		// the chr(13) substitution fixes a bug seen in TCPDF (bug #1421290)
		return strtr($text, [')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r']);
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
			$textContent = '(' . $this->filterText($this->getDOMElement()->textContent) . ')';
			$element = [
				"BT $pdfX $pdfY Td $fontStr [$textContent] TJ ET",
				//$fontStr,
				//"1 0 0 1 $pdfX $pdfY Tm",
				//"$textContent Tj",
				//'ET',
			];
		} else {
			$element = [
				'q',
				'1 w', //border
				'0 0 0 RG',
				"1 0 0 1 $pdfX $pdfY cm",
				"0 0 $width $height re",
				'S',
				'Q',
			];
		}
		return implode("\n", $element);
	}
}
