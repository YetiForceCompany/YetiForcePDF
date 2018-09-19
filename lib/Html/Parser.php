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
	 * @var \YetiForcePDF\Html\Element
	 */
	protected $rootElement;

	/**
	 * Load html string
	 * @param string $html
	 * @return \YetiForcePDF\Html\Parser
	 */
	public function loadHtml(string $html): \YetiForcePDF\Html\Parser
	{
		$this->html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
		$this->domDocument = new \DOMDocument();
		$this->domDocument->loadHTML($this->html, LIBXML_HTML_NOIMPLIED);
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
	 * Convert loaded html to pdf objects
	 */
	public function parse()
	{
		if ($this->html === '') {
			return null;
		}
		$this->rootElement = (new \YetiForcePDF\Html\Element())->setDocument($this->document)->setElement($this->domDocument->documentElement)->init();
		foreach ($this->getAllElements($this->rootElement) as $element) {
			$this->document->getCurrentPage()->getContentStream()->addRawContent($element->getInstructions());
		}
	}

}
