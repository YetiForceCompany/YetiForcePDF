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
class Parser
{
	/**
	 * @var \YetiForcePDF\Document
	 */
	protected $document;
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
	 * HtmlParser constructor.
	 * @param \YetiForcePDF\Document $document
	 */
	public function __construct(\YetiForcePDF\Document $document)
	{
		$this->document = $document;
	}

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
	 * Convert loaded html to pdf objects
	 * @return \YetiForcePDF\Html\Element|null
	 */
	public function parse()
	{
		if ($this->html === '') {
			return null;
		}
		$this->rootElement = new \YetiForcePDF\Html\Element($this->document, $this->domDocument->documentElement);
		$this->rootElement->parse();
		return $this->rootElement;
	}

}
