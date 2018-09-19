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
	 * Load html string
	 * @param string $html
	 * @return \YetiForcePDF\Html\Parser
	 */
	public function loadHtml(string $html): \YetiForcePDF\Html\Parser
	{
		$this->html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
		$this->domDocument = new \DOMDocument();
		$this->domDocument->loadHTML("<div>$this->html</div>", LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		return $this;
	}

	/**
	 * Get all elements as a flat array
	 * @param \YetiForcePDF\Html\Element $currentNode
	 * @param array                      $currentResult
	 * @return \YetiForcePDF\Html\Element[]
	 */
	protected function getAllElements(array $current = []): array
	{
		foreach ($current as $currentNode) {
			foreach ($this->getAllElements($currentNode->getChildren()) as $child) {
				$current[] = $child;
			}
		}
		return $current;
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
		foreach ($this->domDocument->documentElement->childNodes as $child) {
			$this->elements[] = (new \YetiForcePDF\Html\Element())
				->setDocument($this->document)
				->setElement($child)
				->init();
		}
		foreach ($this->getAllElements($this->elements) as $element) {
			$this->document->getCurrentPage()->getContentStream()->addRawContent($element->getInstructions());
		}

	}

}
