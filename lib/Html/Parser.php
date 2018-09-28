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
	 * Cleanup html
	 * @param string $html
	 * @return string
	 */
	protected function cleanUpHtml(string $html)
	{
		$html = mb_convert_encoding($html, 'UTF-8');
		$html = preg_replace('/[\n\r\t]+/', '', $html);
		$html = trim(preg_replace('/\s+/', ' ', $html), " \n\t\r");
		return $html;
	}

	/**
	 * Load html string
	 * @param string $html
	 * @return \YetiForcePDF\Html\Parser
	 */
	public function loadHtml(string $html): \YetiForcePDF\Html\Parser
	{
		$html = $this->cleanUpHtml($html);
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
		$this->rootElement = (new \YetiForcePDF\Html\Element())
			->setDocument($this->document)
			->setElement($this->domDocument->documentElement)
			->setRoot(true);
		// root element must be defined before initialisation
		$this->rootElement->init();
		$style = $this->rootElement->getStyle()->initDimensions()->initCoordinates();
		$style->calculateDimensions();
		$style->calculateCoordinates();
		//$style->calculateCoordinates();
		foreach ($this->getAllElements($this->rootElement) as $element) {
			$this->document->getCurrentPage()->getContentStream()->addRawContent($element->getInstructions());
		}

	}

}
