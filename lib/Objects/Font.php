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
		'PTSerif-BoldItalic' => 'PTSerif-BoldItalic.ttf',
		'Lato-BlackItalic' => 'Lato-BlackItalic.ttf',
		'Lato-Black' => 'Lato-Black.ttf',
		'Lato-BoldItalic' => 'Lato-BoldItalic.ttf',
		'Lato-Bold' => 'Lato-Bold.ttf',
		'Lato-Italic' => 'Lato-Italic.ttf',
		'Lato-Regular' => 'Lato-Regular.ttf',
		'Lato-LightItalic' => 'Lato-LightItalic.ttf',
		'Lato-Light' => 'Lato-Light.ttf',
		'Lato-HairlineItalic' => 'Lato-HairlineItalic.ttf',
		'Lato-Hairline' => 'Lato-Hairline.ttf',
		'PTSerif-Bold' => 'PTSerif-Bold.ttf',
		'PTSerif-Italic' => 'PTSerif-Italic.ttf',
		'PTSerif-Regular' => 'PTSerif-Regular.ttf',
		'PTMono' => 'PTMono.ttf',
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
	protected $family = 'Lato-Regular';
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
	 * Font data
	 * @var \FontLib\Font
	 */
	protected $fontData;
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
	 * Info needed to write in pdf
	 * @var array
	 */
	protected $outputInfo = [];

	/**
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		$alreadyExists = $this->document->getFontInstance($this->family);
		if (!$alreadyExists) {
			parent::init();
			$this->document->setFontInstance($this->family, $this);
			$this->fontNumber = 'F' . $this->document->getActualFontId();
			$this->fontData = $this->loadFontData();
			$this->document->setFontData($this->family, $this->fontData);
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
	 * Get font data
	 * @return \FontLib\Font
	 */
	public function getData()
	{
		return $this->fontData;
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
	 * Load font
	 * @return \FontLib\TrueType\File|null
	 * @throws \FontLib\Exception\FontNotFoundException
	 */
	protected function loadFontData()
	{
		$font = \FontLib\Font::load($this->fontDir . $this->fontFiles[$this->family]);
		$head = $font->getData('head');
		$hhea = $font->getData('hhea');
		$post = $font->getData('post');
		$descriptor = $this->outputInfo['descriptor'] = [];
		$descriptor['Flags'] = $head['flags'];
		$descriptor['FontBBox'] = '[' . implode(' ', [
				$font->normalizeFUnit($head['xMin']),
				$font->normalizeFUnit($head['yMin']),
				$font->normalizeFUnit($head['xMax']),
				$font->normalizeFUnit($head['yMax']),
			]) . ']';
		$descriptor['Ascent'] = $hhea['ascent'];
		$descriptor['Descent'] = $hhea['descent'];
		$descriptor['StemV'] = 80; // adobe doesn't know either why 80
		$descriptor['ItalicAngle'] = $post['italicAngle'];
		// at the end get capHeight
		$os2Table = $font->getTable()['OS/2'];
		// capHeight offset 66- start of the additional fields +20 offset to capHeight
		$os2Table->seek($os2Table->offset + 66 + 20);
		$descriptor['CapHeight'] = $os2Table->readUInt16();
		return $font;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return implode("\n", [$this->getRawId() . " obj",
			"<<",
			"  /Type /Font",
			"  /Subtype /" . $this->fontData['type'],
			"  /BaseFont /" . $this->getFullName(),
			"  /FontDescriptor " . $this->fontDescriptor->getReference(),
			'  /Widths [' . implode(',', $this->getInfo('cw')) . ']',
			'  /Encoding ' . $this->encoding->getReference(),
			">>",
			"endobj"]);
	}
}
