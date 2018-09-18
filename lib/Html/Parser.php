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
	 * Root element style
	 * @var \YetiPDF\Html\Style
	 */
	protected $rootStyle;

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
	 * @return \YetiPDF\Parser
	 */
	public function loadHtml(string $html): \YetiPDF\Parser
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
		$root = $this->domDocument->documentElement;
		if ($root->hasAttribute('style')) {
			$this->rootStyle = new \YetiPDF\Html\Style($root->getAttribute('style'));
		}
		return $objects;
	}

}
