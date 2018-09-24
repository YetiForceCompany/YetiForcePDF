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
	 * @var float
	 */
	protected $unitsPerEm = 1000;
	protected $widths = [];
	protected $toUnicodeStream;
	protected $descendantFonts;
	protected $cidFont;
	protected $cidDictionary;
	protected $cidToGid;
	protected $cidSystemInfo;
	protected $charMap = [];
	protected $fontType0;
	protected $fontCid;

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
			/*$this->encoding = (new \YetiForcePDF\Objects\FontEncoding())
				->setDocument($this->document)
				->init();*/
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
		return $this->fontData->getFontPostscriptName();
	}

	/**
	 * Get output info
	 * @return array
	 */
	public function getOutputInfo()
	{
		return $this->outputInfo;
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

	public function normalizeUnit($value, $base = 1000)
	{
		return floor($value * ($base / $this->unitsPerEm));
	}

	protected function setUpUnicode($charMapUnicode)
	{
		$stream = implode("\n", [
			'/CIDInit /ProcSet findresource begin',
			'12 dict begin',
			'begincmap',
			'/CIDSystemInfo',
			'<</Registry (Adobe)',
			'/Ordering (UCS)',
			'/Supplement 0',
			'>> def',
			'/CMapName /Adobe-Identity-UCS def',
			'/CMapType 2 def',
			'1 begincodespacerange',
			'<0000> <FFFF>',
			'endcodespacerange',
			'1 beginbfrange',
			'<0000> <FFFF> <0000>',
			'endbfrange',
			'endcmap',
			'CMapName currentdict /CMap defineresource pop',
			'end',
			'end',
		]);
		$this->toUnicode->addRawContent($stream);
	}

	/**
	 * Get Type0 font - main one
	 * @return Font
	 */
	public function getType0Font()
	{
		return $this->fontType0;
	}


	/**
	 * Load font
	 * @return \FontLib\TrueType\File|null
	 * @throws \FontLib\Exception\FontNotFoundException
	 */
	protected function loadFontData()
	{
		$fileName = $this->fontDir . $this->fontFiles[$this->family];
		$fileContent = file_get_contents($fileName);
		$font = \FontLib\Font::load($fileName);
		$font->parse();
		$head = $font->getData('head');
		$hhea = $font->getData('hhea');
		$hmtx = $font->getData('hmtx');
		$post = $font->getData('post');
		$names = $post['names'];
		if (isset($head['unitsPerEm'])) {
			$this->unitsPerEm = $head['unitsPerEm'];
		}
		$this->outputInfo['descriptor'] = [];
		$this->outputInfo['descriptor']['FontBBox'] = '[' . implode(' ', [
				$this->normalizeUnit($head['xMin']),
				$this->normalizeUnit($head['yMin']),
				$this->normalizeUnit($head['xMax']),
				$this->normalizeUnit($head['yMax']),
			]) . ']';
		$this->outputInfo['descriptor']['Ascent'] = $this->normalizeUnit($hhea['ascent']);
		$this->outputInfo['descriptor']['Descent'] = $this->normalizeUnit($hhea['descent']);
		$this->outputInfo['descriptor']['MissingWidth'] = 500;
		$this->outputInfo['descriptor']['StemV'] = 80;
		if ($post['usWeightClass'] > 400) {
			$this->outputInfo['descriptor']['StemV'] = 120;
		}
		$this->outputInfo['descriptor']['ItalicAngle'] = $post['italicAngle'];
		$flags = 0;
		if ($this->outputInfo['descriptor']['ItalicAngle'] !== 0) {
			$flags += 2 ** 6;
		}
		if ($post['isFixedPitch'] === true) {
			$flags += 1;
		}
		$flags += 2 ** 5;
		$this->outputInfo['descriptor']['Flags'] = $flags;
		$this->outputInfo['font'] = [];
		$widths = [];
		$this->widths = [];
		$this->charMap = [];
		foreach ($font->getData("cmap", "subtables") as $subtable) {
			if ($subtable['platformID'] === 0) {
				$this->charMap = $subtable['glyphIndexArray'];
				unset($this->charMap[0xFFFF]);
			}
		}
		$charMapUnicode = [];
		$cidToGid = str_pad('', 256 * 256 * 2, "\x00");
		foreach ($this->charMap as $c => $glyph) {
			// Set values in CID to GID map
			if ($c >= 0 && $c < 0xFFFF && $glyph) {
				$cidToGid[$c * 2] = chr($glyph >> 8);
				$cidToGid[$c * 2 + 1] = chr($glyph & 0xFF);
			}
			$widths[] = $c . ' [' . $font->normalizeFUnit(isset($hmtx[$glyph]) ? $hmtx[$glyph][0] : $hmtx[0][0]) . ']';
		}
		$this->cidToGid = (new \YetiForcePDF\Objects\Basic\StreamObject())
			->setDocument($this->document)
			->init();
		$this->cidToGid->addRawContent($cidToGid)->setFilter('FlateDecode');
		//var_dump($this->widths['A']);
		$this->outputInfo['font']['Widths'] = $widths;
		$this->outputInfo['font']['FirstChar'] = 0;
		$this->outputInfo['font']['LastChar'] = count($widths) - 1;
		// at the end get capHeight
		/*$os2Table = $font->getTable()['OS/2'];
		// capHeight offset 66- start of the additional fields +20 offset to capHeight
		$os2Table->seek($os2Table->offset + 66 + 20);
		$this->outputInfo['descriptor']['CapHeight'] = $this->normalizeUnit($os2Table->readUInt16());
		*/
		$this->fontType0 = (new \YetiForcePDF\Objects\Basic\DictionaryObject())
			->setDocument($this->document)
			->init();
		$this->cidSystemInfo = (new \YetiForcePDF\Objects\Basic\DictionaryObject())
			->setDocument($this->document)
			->init();
		$this->cidSystemInfo->addValue('Registry', '(Adobe)')
			->addValue('Ordering', '(UCS)')
			->addValue('Supplement', '0');
		$this->dataStream = (new \YetiForcePDF\Objects\Basic\StreamObject())
			->setDocument($this->document)
			->setFilter('FlateDecode')
			->init()
			->addRawContent($fileContent);
		$this->toUnicode = (new \YetiForcePDF\Objects\Basic\StreamObject())
			->setDocument($this->document)
			->init();
		$this->setUpUnicode($charMapUnicode);
		$this->fontType0->setDictionaryType('Font')
			->addValue('SubType', '/Type0')
			->addValue('BaseFont', '/' . $font->getFontPostscriptName())
			->addValue('Encoding', '/Identity-H')
			->addValue('DescendantFonts', '[' . $this->getReference() . ']')
			->addValue('ToUnicode', $this->toUnicode->getReference());
		return $font;
	}

	/**
	 * {@inheritdoc}
	 */
	public
	function render(): string
	{
		return implode("\n", [$this->getRawId() . " obj",
			"<<",
			"  /Type /Font",
			"  /Subtype /CIDFontType2",
			"  /BaseFont /" . $this->getFullName(),
			"  /FontDescriptor " . $this->fontDescriptor->getReference(),
			//'  /FirstChar ' . $this->outputInfo['font']['FirstChar'],
			//'  /LastChar ' . $this->outputInfo['font']['LastChar'],
			'  /DW 500',
			'  /W [' . implode(' ', $this->outputInfo['font']['Widths']) . ' ]',
			'  /CIDSystemInfo ' . $this->cidSystemInfo->getReference(),
			'  /CIDToGIDMap ' . $this->cidToGid->getReference(),
			">>",
			"endobj"]);
	}
}
