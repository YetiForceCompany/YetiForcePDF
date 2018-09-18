<?php
declare(strict_types=1);
/**
 * Document class
 *
 * @package   YetiPDF
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF;

/**
 * Class Document
 */
class Document
{

	/**
	 * Actual id auto incremented
	 * @var int
	 */
	protected $actualId = 0;
	/**
	 * Main output buffer / content for pdf file
	 * @var string
	 */
	protected $buffer = '';
	/**
	 * Main entry point - root element
	 * @var \YetiPDF\Catalog $catalog
	 */
	protected $catalog;
	/**
	 * Pages dictionary
	 * @var \YetiPDF\Pages
	 */
	protected $pagesObject;
	/**
	 * Current page object
	 * @var \YetiPDF\Page
	 */
	protected $currentPageObject;
	/**
	 * @var string default page format
	 */
	protected $defaultFormat;
	/**
	 * @var string default page orientation
	 */
	protected $defaultOrientation;
	/**
	 * All objects inside document
	 * @var \YetiPDF\Objects\PdfObject[]
	 */
	protected $objects = [];
	/**
	 * @var \YetiPDF\Html\Parser
	 */
	protected $htmlParser;
	/**
	 * Actual font id
	 * @var int
	 */
	protected $actualFontId = 0;

	/**
	 * Document constructor.
	 */
	public function __construct(string $defaultFormat = 'A4', string $defautlOrientation = \YetiPDF\Page::ORIENTATION_PORTRAIT)
	{
		$this->catalog = new \YetiPDF\Catalog($this);
		$this->pagesObject = $this->catalog->addChild(new \YetiPDF\Pages($this));
		$this->currentPageObject = $this->addPage($defaultFormat, $defautlOrientation);
		$this->defaultFormat = $defaultFormat;
		$this->defaultOrientation = $defautlOrientation;
	}

	/**
	 * Get actual id for newly created object
	 * @return int
	 */
	public function getActualId()
	{
		return ++$this->actualId;
	}

	/**
	 * Get actual id for newly created font
	 * @return int
	 */
	public function getActualFontId(): int
	{
		return ++$this->actualFontId;
	}

	/**
	 * Get pages object
	 * @return \YetiPDF\Pages
	 */
	public function getPagesObject(): \YetiPDF\Pages
	{
		return $this->pagesObject;
	}

	/**
	 * Add page to the document
	 * @param string $format      - optional format 'A4' for example
	 * @param string $orientation - optional orientation 'P' or 'L'
	 * @return \YetiPDF\Page
	 */
	public function addPage(string $format = '', string $orientation = ''): \YetiPDF\Page
	{
		$page = new \YetiPDF\Page($this);
		if ($format === '') {
			$format = $this->defaultFormat;
		}
		if ($orientation === '') {
			$orientation = $this->defaultOrientation;
		}
		$page->setFormat($format)->setOrientation($orientation);
		$this->currentPageObject = $page;
		return $page;
	}

	/**
	 * Get current page
	 * @return \YetiPDF\Page
	 */
	public function getCurrentPage(): \YetiPDF\Page
	{
		return $this->currentPageObject;
	}

	/**
	 * Get document header
	 * @return string
	 */
	protected function getDocumentHeader(): string
	{
		return "%PDF-1.7\n\n";
	}

	/**
	 * Get document footer
	 * @return string
	 */
	protected function getDocumentFooter(): string
	{
		return '%%EOF';
	}

	/**
	 * Add object to document
	 * @param \YetiPDF\Objects\Basic\StreamObject $stream
	 * @return \YetiPDF\Document
	 */
	public function addObject(\YetiPDF\Objects\PdfObject $object): \YetiPDF\Document
	{
		$this->objects[] = $object;
		return $this;
	}

	/**
	 * Remove object from document
	 * @param \YetiPDF\Objects\PdfObject $object
	 * @return \YetiPDF\Document
	 */
	public function removeObject(\YetiPDF\Objects\PdfObject $object): \YetiPDF\Document
	{
		$this->objects = array_filter($this->objects, function ($currentObject) use ($object) {
			return $currentObject !== $object;
		});
		return $this;
	}

	/**
	 * Load html string
	 * @param string $html
	 * @return \YetiPDF\Document
	 */
	public function loadHtml(string $html): \YetiPDF\Document
	{
		$this->htmlParser = new \YetiPDF\Html\Parser($this);
		$this->htmlParser->loadHtml($html);
		return $this;
	}

	/**
	 * Count objects
	 * @param string $name - object name
	 * @return int
	 */
	public function countObjects(string $name = ''): int
	{
		if ($name === '') {
			return count($this->objects);
		}
		$typeCount = 0;
		foreach ($this->objects as $object) {
			if ($object->getName() === $name) {
				$typeCount++;
			}
		}
		return $typeCount;
	}

	/**
	 * Get objects
	 * @param string $name - object name
	 * @return \YetiPDF\Objects\PdfObject[]
	 */
	public function getObjects(string $name = ''): array
	{
		if ($name === '') {
			return $this->objects;
		}
		return array_filter($this->objects, function ($currentObject) use ($name) {
			return $currentObject->getName() === $name;
		});
	}

	/**
	 * Render document content to pdf string
	 * @return string
	 */
	public function render(): string
	{
		$this->buffer = '';
		$this->buffer .= $this->getDocumentHeader();
		$this->htmlParser->parse();
		$trailer = new \YetiPDF\Objects\Trailer($this);
		$trailer->setRootObject($this->catalog);
		foreach ($this->objects as $object) {
			if (in_array($object->getBasicType(), ['Dictionary', 'Stream', 'Trailer'])) {
				$this->buffer .= $object->render() . "\n";
			}
		}
		$this->buffer .= $this->getDocumentFooter();
		$this->removeObject($trailer);
		return $this->buffer;
	}

}
