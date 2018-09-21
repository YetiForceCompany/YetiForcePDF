<?php
declare(strict_types=1);
/**
 * Font class
 *
 * @package   YetiForcePDF\Objects
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Objects;

/**
 * Class Font
 */
class Font extends \YetiForcePDF\Objects\Resource
{
	protected $fontDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fonts' . DIRECTORY_SEPARATOR;
	protected $fontFiles = [
		'Lato' => 'lato.php',
		'Lato-Bold' => 'latob.php',
		'Lato-BoldItalic' => 'latobi.php',
		'Lato-Italic' => 'latoi.php',

		'PTSerif' => 'pt_serif.php',
		'PTSerif-Bold' => 'pt_serifb.php',
		'PTSerif-BoldItalic' => 'pt_serifbi.php',
		'PTSerif-Italic' => 'pt_serifi.php',

		'PTMono' => 'pt_mono.php',
	];
	/**
	 * Which type of dictionary (Page, Catalog, Font etc...)
	 * @var string
	 */
	protected $resourceType = 'Font';
	/**
	 * Object name
	 * @var string
	 */
	protected $resourceName = 'Font';
	/**
	 * Base font type aka font family
	 * @var string
	 */
	protected $family = 'Lato';
	/**
	 * Font number
	 * @var string
	 */
	protected $fontNumber = 'F1';
	/**
	 * Font size
	 * @var float
	 */
	protected $size = 12;
	/**
	 * Font info
	 * @var array
	 */
	protected $fontInfo = [];
	/**
	 * @var
	 */
	protected $fontDescriptor;
	/**
	 * @var \YetiForcePDF\Objects\Basic\StreamObject
	 */
	protected $dataStream;
	/**
	 * @var \YetiForcePDF\Objects\FontEncoding
	 */
	protected $encoding;

	/**
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		$this->loadFontsInfo();
		$alreadyExists = $this->document->getFontInstance($this->family);
		if (!$alreadyExists) {
			parent::init();
			$this->document->setFontInstance($this->family, $this);
			$this->fontNumber = 'F' . $this->document->getActualFontId();
			$this->fontInfo = $this->document->getFonts($this->family);
			$this->loadFontData();
			$this->fontDescriptor = (new \YetiForcePDF\Objects\FontDescriptor())
				->setDocument($this->document)
				->setFont($this)
				->init();
			$this->encoding = (new \YetiForcePDF\Objects\FontEncoding())
				->setDocument($this->document)
				->init();
			foreach ($this->document->getObjects('Page') as $page) {
				$page->synchronizeFonts();
			}
			return $this;
		}
		$this->setAddToDocument(false);
		parent::init();
		return $alreadyExists;
	}

	/**
	 * Set font number
	 * @param string $number
	 * @return \YetiForcePDF\Objects\Font
	 */
	public function setNumber(string $number): \YetiForcePDF\Objects\Font
	{
		$this->fontNumber = $number;
		return $this;
	}

	/**
	 * Set font name
	 * @param string $base
	 * @return $this
	 */
	public function setFamily(string $name)
	{
		$this->family = $name;
		return $this;
	}

	/**
	 * Get font name
	 * @return string
	 */
	public function getFamily(): string
	{
		return $this->family;
	}

	/**
	 * Get full font name
	 * @return string
	 */
	public function getFullName()
	{
		return 'GCCBBY+' . $this->family;
	}

	/**
	 * Get info
	 * @param string $name
	 * @return array
	 */
	public function getInfo(string $name = '')
	{
		if ($name) {
			return $this->fontInfo[$name];
		}
		return $this->fontInfo;
	}

	/**
	 * Set Font size
	 * @param float $size
	 * @return $this
	 */
	public function setSize(float $size)
	{
		$this->size = $size;
		return $this;
	}

	/**
	 * Get font size
	 * @return float
	 */
	public function getSize(): float
	{
		return $this->size;
	}

	/**
	 * Get font number
	 * @return string
	 */
	public function getNumber(): string
	{
		return $this->fontNumber;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getReference(): string
	{
		return $this->getRawId() . ' R';
	}

	/**
	 * Get data stream
	 * @return \YetiForcePDF\Objects\Basic\StreamObject
	 */
	public function getDataStream()
	{
		return $this->dataStream;
	}

	/**
	 * Load fonts information if not exists already
	 */
	protected function loadFontsInfo()
	{
		if (empty($this->document->getFonts())) {
			foreach ($this->fontFiles as $name => $path) {
				$fontInfo = require($this->fontDir . $path);
				$this->document->setFontInfo($name, $fontInfo);
			}
		}
	}

	/**
	 * Load font data
	 * @return $this
	 */
	protected function loadFontData()
	{
		$dataFileName = $this->fontDir . $this->fontInfo['file'];
		$fontData = file_get_contents($dataFileName);
		$this->fontInfo['fontData'] = $fontData;
		$this->fontInfo['dataStream'] = $this->dataStream = (new \YetiForcePDF\Objects\Basic\StreamObject())
			->setDocument($this->document)
			->init();
		$this->dataStream->addRawContent($fontData)->setFilter('FlateDecode');
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return implode("\n", [$this->getRawId() . " obj",
			"<<",
			"  /Type /Font",
			"  /Subtype /" . $this->fontInfo['type'],
			"  /BaseFont /" . $this->getFullName(),
			"  /FontDescriptor " . $this->fontDescriptor->getReference(),
			'  /Widths [' . implode(',', $this->getInfo('cw')) . ']',
			'  /Encoding ' . $this->encoding->getReference(),
			">>",
			"endobj"]);
	}
}
