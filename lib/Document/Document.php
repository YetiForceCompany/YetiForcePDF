<?php
declare(strict_types=1);
/**
 * Document class
 *
 * @package   YetiPDF\Document
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Document;

/**
 * Class YetiPDF_Document
 */
class Document
{
	/**
	 * Main output buffer / content for pdf file
	 * @var string
	 */
	protected $buffer = '';
	/**
	 * @var \YetiPDF\Document\Catalog $catalog
	 */
	protected $catalog;
	/**
	 * All content goes here
	 * @var array
	 */
	protected $objects = [];
	/**
	 * @var int $pageIndex - current page in array
	 */
	protected $pageIndex = -1;
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
	public function __construct(string $defaultFormat = 'A4', string $defautlOrientation = \YetiPDF\Document\Page::ORIENTATION_PORTRAIT)
	{
		$this->catalog = new \YetiPDF\Document\Catalog();
		$this->defaultFormat = $defaultFormat;
		$this->defaultOrientation = $defautlOrientation;
		$this->addPage($defaultFormat, $defautlOrientation);
	}

	/**
	 * Add page to the document
	 * @param string $defaultFormat
	 * @param string $defaultOrientation
	 * @return \YetiPDF\Document\YetiPDFPage
	 */
	public function addPage(string $defaultFormat = 'A4', string $defaultOrientation = \YetiPDF\Document\Page::ORIENTATION_PORTRAIT): \YetiPDF\Document\Page
	{
		$this->objects[] = new \YetiPDF\Document\Page($defaultFormat, $defaultOrientation);
		$this->pageIndex++;
		return $this->body[$this->pageIndex];
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
	public function render()
	{
		$this->buffer = '';
		$this->buffer .= $this->getDocumentHeader();
		$this->buffer .= $this->getDocumentFooter();
		return $this->buffer;
	}

}
