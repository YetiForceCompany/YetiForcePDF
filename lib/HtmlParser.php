<?php
declare(strict_types=1);
/**
 * HtmlParser class
 *
 * @package   YetiPDF
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF;

/**
 * Class HtmlParser
 */
class HtmlParser
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
	 * @return \YetiPDF\HtmlParser
	 */
	public function loadHtml(string $html): \YetiPDF\HtmlParser
	{
		$this->html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
		$this->domDocument = new \DOMDocument();
		$this->domDocument->loadHTML($this->html);
		return $this;
	}

	/**
	 * Convert loaded html to pdf objects
	 * @return array
	 */
	public function convertToObjects(): array
	{
		$objects = [];
		if ($this->html === '') {
			return $objects;
		}

		return $objects;
	}

}
