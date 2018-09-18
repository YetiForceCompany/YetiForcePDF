<?php
declare(strict_types=1);
/**
 * Parser class
 *
 * @package   YetiPDF\Html
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Html;

/**
 * Class Parser
 */
class Parser
{
	/**
	 * @var \YetiPDF\Document
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
	 * @var \YetiPDF\Html\Element
	 */
	protected $rootElement;

	/**
	 * HtmlParser constructor.
	 * @param \YetiPDF\Document $document
	 */
	public function __construct(\YetiPDF\Document $document)
	{
		$this->document = $document;
	}

	/**
	 * Load html string
	 * @param string $html
	 * @return \YetiPDF\Html\Parser
	 */
	public function loadHtml(string $html): \YetiPDF\Html\Parser
	{
		$this->html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
		$this->domDocument = new \DOMDocument();
		$this->domDocument->loadHTML($this->html, LIBXML_HTML_NOIMPLIED);
		return $this;
	}

	/**
	 * Convert loaded html to pdf objects
	 * @return \YetiPDF\Html\Element|null
	 */
	public function parse()
	{
		if ($this->html === '') {
			return null;
		}
		$this->rootElement = new \YetiPDF\Html\Element($this->document, $this->domDocument->documentElement);
		$this->rootElement->parse();
		return $this->rootElement;
	}

}
