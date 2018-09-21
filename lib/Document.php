<?php
declare(strict_types=1);
/**
 * Document class
 *
 * @package   YetiForcePDF
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF;

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
	 * @var \YetiForcePDF\Catalog $catalog
	 */
	protected $catalog;
	/**
	 * Pages dictionary
	 * @var \YetiForcePDF\Pages
	 */
	protected $pagesObject;
	/**
	 * Current page object
	 * @var \YetiForcePDF\Page
	 */
	protected $currentPageObject;
	/**
	 * @var string default page format
	 */
	protected $defaultFormat = 'A4';
	/**
	 * @var string default page orientation
	 */
	protected $defaultOrientation = \YetiForcePDF\Page::ORIENTATION_PORTRAIT;
	/**
	 * Default page margins
	 * @var array
	 */
	protected $defaultMargins = [
		'left' => 10,
		'top' => 10,
		'right' => 10,
		'bottom' => 10
	];
	/**
	 * All objects inside document
	 * @var \YetiForcePDF\Objects\PdfObject[]
	 */
	protected $objects = [];
	/**
	 * @var \YetiForcePDF\Html\Parser
	 */
	protected $htmlParser;
	/**
	 * Fonts
	 * @var array|null
	 */
	protected $fonts = [];
	/**
	 * Actual font id
	 * @var int
	 */
	protected $actualFontId = 0;


	/**
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		$this->catalog = (new \YetiForcePDF\Catalog())->setDocument($this)->init();
		$this->pagesObject = $this->catalog->addChild((new \YetiForcePDF\Pages())->setDocument($this)->init());
		$this->currentPageObject = $this->addPage($this->defaultFormat, $this->defaultOrientation);
		return $this;
	}

	/**
	 * Set default page format
	 * @param string $defaultFormat
	 * @return $this
	 */
	public function setDefaultFormat(string $defaultFormat)
	{
		$this->defaultFormat = $defaultFormat;
		return $this;
	}

	/**
	 * Set default page orientation
	 * @param string $defaultOrientation
	 * @return $this
	 */
	public function setDefaultOrientation(string $defaultOrientation)
	{
		$this->defaultOrientation = $defaultOrientation;
		return $this;
	}

	/**
	 * Set default page margins
	 * @param float $left
	 * @param float $top
	 * @param float $right
	 * @param float $bottom
	 * @return $this
	 */
	public function setDefaultMargins(float $left, float $top, float $right, float $bottom)
	{
		$this->defaultMargins = [
			'left' => $left,
			'top' => $top,
			'right' => $right,
			'bottom' => $bottom,
			'horizontal' => $left + $right,
			'vertical' => $top + $bottom
		];
		return $this;
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
	 * Get document font data/info
	 * @param string $family [optional]
	 * @return array
	 */
	public function getFonts(string $family = '')
	{
		if ($family) {
			return $this->fonts[$family];
		}
		return $this->fonts;
	}

	/**
	 * Set font
	 * @param string                     $fontName
	 * @param \YetiForcePDF\Objects\Font $fontInstance
	 * @return $this
	 */
	public function setFontInstance(string $fontName, \YetiForcePDF\Objects\Font $fontInstance)
	{
		if (empty($this->fonts[$fontName])) {
			$this->fonts[$fontName] = [];
		}
		$this->fonts[$fontName]['instance'] = $fontInstance;
		return $this;
	}

	/**
	 * Get font instance
	 * @param $fontName
	 * @return null|\YetiForcePDF\Objects\Font
	 */
	public function getFontInstance(string $fontName)
	{
		if (!empty($this->fonts[$fontName]['instance'])) {
			return $this->fonts[$fontName]['instance'];
		}
		return null;
	}

	/**
	 * Set font information
	 * @param string $fontName
	 * @param array  $info
	 * @return $this
	 */
	public function setFontInfo(string $fontName, array $info)
	{
		if (empty($this->fonts[$fontName])) {
			$this->fonts[$fontName] = $info;
			return $this;
		}
		$this->fonts[$fontName] = array_merge($this->fonts[$fontName], $info);
		return $this;
	}

	/**
	 * Get pages object
	 * @return \YetiForcePDF\Pages
	 */
	public function getPagesObject(): \YetiForcePDF\Pages
	{
		return $this->pagesObject;
	}

	/**
	 * Get default page format
	 * @return string
	 */
	public function getDefaultFormat()
	{
		return $this->defaultFormat;
	}

	/**
	 * Get default page orientation
	 * @return string
	 */
	public function getDefaultOrientation()
	{
		return $this->defaultOrientation;
	}

	/**
	 * Get default margins
	 * @return array
	 */
	public function getDefaultMargins()
	{
		return $this->defaultMargins;
	}

	/**
	 * Add page to the document
	 * @param string $format      - optional format 'A4' for example
	 * @param string $orientation - optional orientation 'P' or 'L'
	 * @return \YetiForcePDF\Page
	 */
	public function addPage(string $format = '', string $orientation = ''): \YetiForcePDF\Page
	{
		$page = (new \YetiForcePDF\Page())->setDocument($this)->init();
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
	 * @return \YetiForcePDF\Page
	 */
	public function getCurrentPage(): \YetiForcePDF\Page
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
	 * @param \YetiForcePDF\Objects\Basic\StreamObject $stream
	 * @return \YetiForcePDF\Document
	 */
	public function addObject(\YetiForcePDF\Objects\PdfObject $object): \YetiForcePDF\Document
	{
		$this->objects[] = $object;
		return $this;
	}

	/**
	 * Remove object from document
	 * @param \YetiForcePDF\Objects\PdfObject $object
	 * @return \YetiForcePDF\Document
	 */
	public function removeObject(\YetiForcePDF\Objects\PdfObject $object): \YetiForcePDF\Document
	{
		$this->objects = array_filter($this->objects, function ($currentObject) use ($object) {
			return $currentObject !== $object;
		});
		return $this;
	}

	/**
	 * Load html string
	 * @param string $html
	 * @return \YetiForcePDF\Document
	 */
	public function loadHtml(string $html): \YetiForcePDF\Document
	{
		$this->htmlParser = (new \YetiForcePDF\Html\Parser())->setDocument($this)->init();
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
	 * @return \YetiForcePDF\Objects\PdfObject[]
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
		$trailer = (new \YetiForcePDF\Objects\Trailer())
			->setDocument($this)
			->init();
		$trailer->setRootObject($this->catalog)->setSize(count($this->objects) - 1);
		foreach ($this->objects as $object) {
			if (in_array($object->getBasicType(), ['Dictionary', 'Stream', 'Trailer'])) {
				$this->buffer .= $object->render() . "\n";
			}
		}
		$this->buffer .= $this->getDocumentFooter();
		$this->removeObject($trailer);
		return $this->buffer;
	}

	/**
	 * Convert units from unit to pdf document units
	 * @param string $unit
	 * @param float  $size
	 * @return float
	 */
	public function convertUnits(string $unit, float $size, \YetiForcePDF\Html\Element $parentElement = null): float
	{
		if ($parentElement === null) {
			$parentElement = $this->htmlParser->getRootElement();
		}
		switch ($unit) {
			case 'px':
			case 'pt':
				return $size;
			case 'mm':
				return $size / (72 / 25.4);
			case 'cm':
				return $size / (72 / 2.54);
			case 'in':
				return $size / 72;
			case '%':
				return $parentSize / 100 * $size;
			case 'em':
			case 'rem':
				return $size * $parentSize;

		}

	}

}
