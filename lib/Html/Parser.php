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
		$html = preg_replace('/[\n\r\t]+/', '', $html);
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
		$this->box->prepareTree($this->domDocument->documentElement);
		$this->rootElement = (new \YetiForcePDF\Html\Element())
			->setDocument($this->document)
			->setDOMElement($this->box->getDOMTree());
		// root element must be defined before initialisation
		$this->document->setRootElement($this->rootElement);
		$this->rootElement->init();
		$this->box->setStyle($this->rootElement->parseStyle());
		$this->box->getDimensions()->setUpAvailableSpace();
		$this->box->buildTree();
		$this->box->reflow();
		$children = [];
		$this->box->getAllChildren($children);
		foreach ($children as $box) {
			if (!$box instanceof \YetiForcePDF\Render\LineBox) {
				$this->document->getCurrentPage()->getContentStream()->addRawContent($box->getInstructions());
			}
		}
	}
}
