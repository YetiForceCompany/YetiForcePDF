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
	 * Document constructor.
	 */
	public function __construct(string $defaultFormat = 'A4', string $defautlOrientation = \YetiPDF\Page::ORIENTATION_PORTRAIT)
	{
		$this->catalog = new \YetiPDF\Catalog($this);
		$this->addObject($this->catalog);
		$this->pagesObject = $this->catalog->addChild(new \YetiPDF\Pages($this));
		$this->addObject($this->pagesObject);
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
	 * Add page to the document
	 * @param string $format      - optional format 'A4' for example
	 * @param string $orientation - optional orientation 'P' or 'L'
	 * @return \YetiPDF\Page
	 */
	public function addPage(string $format = '', string $orientation = ''): \YetiPDF\Page
	{
		$page = new \YetiPDF\Page($this);
		$this->addObject($page);
		if ($format === '') {
			$format = $this->defaultFormat;
		}
		if ($orientation === '') {
			$orientation = $this->defaultOrientation;
		}
		$font = new \YetiPDF\Objects\Font($this);
		$this->addObject($font);
		$font->setNumber('F' . ($this->countObjects('Font') + 1));
		$page->setFormat($format)->setOrientation($orientation)->addResource($font);
		$this->currentPageObject = $this->pagesObject->addChild($page);
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
	 * Add text to current page
	 * @param string $text
	 * @param float  $fontSize
	 * @param float  $x
	 * @param float  $y
	 * @return \YetiPDF\Objects\TextStream
	 */
	public function addText(string $text, float $fontSize, float $x, float $y): \YetiPDF\Objects\TextStream
	{
		$textStream = new \YetiPDF\Objects\TextStream($this);
		$textStream->setText($text);
		$textStream->setFontSize($fontSize);
		$textStream->setX($x);
		$textStream->setY($y);
		$this->currentPageObject->addContentStream($textStream);
		return $textStream;
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
	 * Render document content to pdf string
	 * @return string
	 */
	public function render(): string
	{
		$this->buffer = '';
		$this->buffer .= $this->getDocumentHeader();
		foreach ($this->objects as $object) {
			$this->buffer .= $object->render() . "\n";
		}
		$trailer = new \YetiPDF\Objects\Trailer($this);
		$trailer->setRootObject($this->catalog);
		$this->buffer .= $trailer->render();
		$this->buffer .= $this->getDocumentFooter();
		return $this->buffer;
	}

}
