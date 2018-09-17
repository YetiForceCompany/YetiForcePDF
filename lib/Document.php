<?php
declare(strict_types=1);
/**
 * Document class
 *
 * @package   YetiPDF\Document
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
	 * Document constructor.
	 */
	public function __construct(string $defaultFormat = 'A4', string $defautlOrientation = \YetiPDF\Page::ORIENTATION_PORTRAIT)
	{
		$this->catalog = new \YetiPDF\Catalog($this->getActualId());
		$this->pagesObject = $this->catalog->addChild(new \YetiPDF\Pages($this->getActualId()));
		$this->currentPageObject = $this->pagesObject->addChild(new \YetiPDF\Page($this->getActualId()))->setFormat($defaultFormat)->setOrientation($defautlOrientation);
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
		$page = new \YetiPDF\Page($this->getActualId());
		if ($format === '') {
			$format = $this->defaultFormat;
		}
		if ($orientation === '') {
			$orientation = $this->defaultOrientation;
		}
		$page->setFormat($format)->setOrientation($orientation);
		return $this->catalog->pagesObject->addChild($page);
	}


	/**
	 * Get document header
	 * @return string
	 */
	protected function getDocumentHeader(): string
	{
		return "%PDF-1.7\n\n";
	}

	protected function getDocumentFooter(): string
	{
		return '%%EOF';
	}

	/**
	 * Render document content to pdf string
	 * @return string
	 */
	public function render(): string
	{
		$this->buffer = '';
		$this->buffer .= $this->getDocumentHeader();

		$this->buffer .= $this->getDocumentFooter();
		return $this->buffer;
	}

}
