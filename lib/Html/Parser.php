<?php
declare(strict_types=1);
/**
 * Parser class
 *
 * @package   YetiForcePDF\Html
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Html;

use \YetiForcePDF\Render\BlockBox;

/**
 * Class Parser
 */
class Parser extends \YetiForcePDF\Base
{
	/**
	 * @var \DOMDocument
	 */
	protected $domDocument;
	/**
	 * @var string
	 */
	protected $html = '';
	/**
	 * @var \YetiForcePDF\Html\Element[]
	 */
	protected $elements = [];
	/**
	 * Root element
	 * @var \YetiForcePDF\Html\Element
	 */
	protected $rootElement;
	/**
	 * @var BlockBox
	 */
	protected $box;

	/**
	 * Cleanup html
	 * @param string $html
	 * @param string $fromEncoding
	 * @return string
	 */
	protected function cleanUpHtml(string $html, string $fromEncoding = '')
	{
		if (!$fromEncoding) {
			$fromEncoding = mb_detect_encoding($html);
		}
		$html = mb_convert_encoding($html, 'UTF-8', $fromEncoding);
		$html = preg_replace('/[\n\r\t]+/', ' ', $html);
		$html = preg_replace('/[ ]+/', ' ', $html);
		return $html;
	}

	/**
	 * Load html string
	 * @param string $html
	 * @param string $fromEncoding
	 * @return \YetiForcePDF\Html\Parser
	 */
	public function loadHtml(string $html, string $fromEncoding = ''): \YetiForcePDF\Html\Parser
	{
		$html = $this->cleanUpHtml($html, $fromEncoding);
		$this->html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
		$this->domDocument = new \DOMDocument();
		$this->domDocument->loadHTML('<div id="yetiforcepdf">' . $this->html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		return $this;
	}

	/**
	 * Get all elements as a flat array
	 * @param \YetiForcePDF\Html\Element $currentNode
	 * @param array                      $currentResult
	 * @return \YetiForcePDF\Html\Element[]
	 */
	protected function getAllElements(\YetiForcePDF\Html\Element $currentNode, array &$currentResult = []): array
	{
		$currentResult[] = $currentNode;
		foreach ($currentNode->getChildren() as $child) {
			$this->getAllElements($child, $currentResult);
		}
		return $currentResult;
	}

	/**
	 * Get root element
	 * @return \YetiForcePDF\Html\Element
	 */
	public function getRootElement(): \YetiForcePDF\Html\Element
	{
		return $this->rootElement;
	}

	/**
	 * Prepare tree - divide each string into words (DOMText)
	 * @return $this
	 */
	public function prepareTree($domElement, $clonedElement)
	{
		if ($domElement->hasChildNodes()) {
			foreach ($domElement->childNodes as $childNode) {
				$clonedChild = $childNode->cloneNode();
				if ($childNode->nodeName === '#text') {
					$chars = preg_split('/ /u', $childNode->textContent, 0, PREG_SPLIT_NO_EMPTY);
					$count = count($chars);
					foreach ($chars as $index => $char) {
						if ($index + 1 !== $count) {
							$char .= ' ';
						}
						$textNode = $domElement->ownerDocument->createTextNode($char);
						$clonedElement->appendChild($textNode);
					}
				} elseif ($childNode instanceof \DOMElement) {
					$clonedChild = $this->prepareTree($childNode, $clonedChild);
					$clonedElement->appendChild($clonedChild);
				}
			}
		}
		return $clonedElement;
	}

	/**
	 * Convert loaded html to pdf objects
	 */
	public function parse()
	{
		if ($this->html === '') {
			return null;
		}
		$this->elements = [];
		$this->box = (new BlockBox())
			->setDocument($this->document)
			->setRoot(true)
			->init();
		$tree = $this->prepareTree($this->domDocument->documentElement, $this->domDocument->documentElement->cloneNode());
		$this->rootElement = (new \YetiForcePDF\Html\Element())
			->setDocument($this->document)
			->setDOMElement($tree);
		// root element must be defined before initialisation
		$this->document->setRootElement($this->rootElement);
		$this->rootElement->init();
		$this->box->setElement($this->rootElement);
		$this->box->setStyle($this->rootElement->parseStyle());
		$this->box->buildTree();
		$this->box->layout();
		$children = [];
		$this->box->getAllChildren($children);
		foreach ($children as $box) {
			if (!$box instanceof \YetiForcePDF\Render\LineBox) {
				$this->document->getCurrentPage()->getContentStream()->addRawContent($box->getInstructions());
			}
		}
	}
}
