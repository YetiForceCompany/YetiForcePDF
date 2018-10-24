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
	protected $fontFiles = [

		'NotoSansCondensed' => [
			'Thin' => 'NotoSans-CondensedThin.ttf',
			'ThinItalic' => 'NotoSans-CondensedThinItalic.ttf',
			'ExtraLight' => 'NotoSans-CondensedExtraLight.ttf',
			'ExtraLightItalic' => 'NotoSans-CondensedExtraLightItalic.ttf',
			'Light' => 'NotoSans-CondensedLight.ttf',
			'LightItalic' => 'NotoSans-CondensedLightItalic.ttf',
			'Regular' => 'NotoSans-Condensed.ttf',
			'Italic' => 'NotoSans-CondensedItalic.ttf',
			'Medium' => 'NotoSans-CondensedMedium.ttf',
			'MediumItalic' => 'NotoSans-CondensedMediumItalic.ttf',
			'SemiBold' => 'NotoSans-CondensedSemiBold.ttf',
			'SemiBoldItalic' => 'NotoSans-CondensedSemiBoldItalic.ttf',
			'Bold' => 'NotoSans-CondensedBold.ttf',
			'BoldItalic' => 'NotoSans-CondensedBoldItalic.ttf',
			'ExtraBold' => 'NotoSans-CondensedExtraBold.ttf',
			'ExtraBoldItalic' => 'NotoSans-CondensedExtraBoldItalic.ttf',
			'Black' => 'NotoSans-CondensedBlack.ttf',
			'BlackItalic' => 'NotoSans-CondensedBlackItalic.ttf',
		],
		'NotoSansSemiCondensed' => [
			'Thin' => 'NotoSans-SemiCondensedThin.ttf',
			'ThinItalic' => 'NotoSans-SemiCondensedThinItalic.ttf',
			'ExtraLight' => 'NotoSans-SemiCondensedExtraLight.ttf',
			'ExtraLightItalic' => 'NotoSans-SemiCondensedExtraLightItalic.ttf',
			'Light' => 'NotoSans-SemiCondensedLight.ttf',
			'LightItalic' => 'NotoSans-SemiCondensedLightItalic.ttf',
			'Regular' => 'NotoSans-SemiCondensed.ttf',
			'Italic' => 'NotoSans-SemiCondensedItalic.ttf',
			'Medium' => 'NotoSans-SemiCondensedMedium.ttf',
			'MediumItalic' => 'NotoSans-SemiCondensedMediumItalic.ttf',
			'SemiBold' => 'NotoSans-SemiCondensedSemiBold.ttf',
			'SemiBoldItalic' => 'NotoSans-SemiCondensedSemiBoldItalic.ttf',
			'Bold' => 'NotoSans-SemiCondensedBold.ttf',
			'BoldItalic' => 'NotoSans-SemiCondensedBoldItalic.ttf',
			'ExtraBold' => 'NotoSans-SemiCondensedExtraBold.ttf',
			'ExtraBoldItalic' => 'NotoSans-SemiCondensedExtraBoldItalic.ttf',
			'Black' => 'NotoSans-SemiCondensedBlack.ttf',
			'BlackItalic' => 'NotoSans-SemiCondensedBlackItalic.ttf',
		],
		'NotoSansExtraCondensed' => [
			'Thin' => 'NotoSans-ExtraCondensedThin.ttf',
			'ThinItalic' => 'NotoSans-ExtraCondensedThinItalic.ttf',
			'ExtraLight' => 'NotoSans-ExtraCondensedExtraLight.ttf',
			'ExtraLightItalic' => 'NotoSans-ExtraCondensedExtraLightItalic.ttf',
			'Light' => 'NotoSans-ExtraCondensedLight.ttf',
			'LightItalic' => 'NotoSans-ExtraCondensedLightItalic.ttf',
			'Regular' => 'NotoSans-ExtraCondensed.ttf',
			'Italic' => 'NotoSans-ExtraCondensedItalic.ttf',
			'Medium' => 'NotoSans-ExtraCondensedMedium.ttf',
			'MediumItalic' => 'NotoSans-ExtraCondensedMediumItalic.ttf',
			'SemiBold' => 'NotoSans-ExtraCondensedSemiBold.ttf',
			'SemiBoldItalic' => 'NotoSans-ExtraCondensedSemiBoldItalic.ttf',
			'Bold' => 'NotoSans-ExtraCondensedBold.ttf',
			'BoldItalic' => 'NotoSans-ExtraCondensedBoldItalic.ttf',
			'ExtraBold' => 'NotoSans-ExtraCondensedExtraBold.ttf',
			'ExtraBoldItalic' => 'NotoSans-ExtraCondensedExtraBoldItalic.ttf',
			'Black' => 'NotoSans-ExtraCondensedBlack.ttf',
			'BlackItalic' => 'NotoSans-ExtraCondensedBlackItalic.ttf',
		],
		'NotoSans' => [
			'Thin' => 'NotoSans-Thin.ttf',
			'ThinItalic' => 'NotoSans-ThinItalic.ttf',
			'ExtraLight' => 'NotoSans-ExtraLight.ttf',
			'ExtraLightItalic' => 'NotoSans-ExtraLightItalic.ttf',
			'Light' => 'NotoSans-Light.ttf',
			'LightItalic' => 'NotoSans-LightItalic.ttf',
			'Regular' => 'NotoMono-Regular.ttf',
			'Italic' => 'NotoSans-Italic.ttf',
			'Medium' => 'NotoSans-Medium.ttf',
			'MediumItalic' => 'NotoSans-MediumItalic.ttf',
			'SemiBold' => 'NotoSans-SemiBold.ttf',
			'SemiBoldItalic' => 'NotoSans-SemiBoldItalic.ttf',
			'Bold' => 'NotoSans-Bold.ttf',
			'BoldItalic' => 'NotoSans-BoldItalic.ttf',
			'ExtraBold' => 'NotoSans-ExtraBold.ttf',
			'ExtraBoldItalic' => 'NotoSans-ExtraBoldItalic.ttf',
			'Black' => 'NotoSans-Black.ttf',
			'BlackItalic' => 'NotoSans-BlackItalic.ttf',
		],

		'NotoSerifSemiCondensed' => [
			'Thin' => 'NotoSerif-SemiCondensedThin.ttf',
			'ThinItalic' => 'NotoSerif-SemiCondensedThinItalic.ttf',
			'ExtraLight' => 'NotoSerif-SemiCondensedExtraLight.ttf',
			'ExtraLightItalic' => 'NotoSerif-SemiCondensedExtraLightItalic.ttf',
			'Light' => 'NotoSerif-SemiCondensedLight.ttf',
			'LightItalic' => 'NotoSerif-SemiCondensedLightItalic.ttf',
			'Regular' => 'NotoSerif-SemiCondensed.ttf',
			'Italic' => 'NotoSerif-SemiCondensedItalic.ttf',
			'Medium' => 'NotoSerif-SemiCondensedMedium.ttf',
			'MediumItalic' => 'NotoSerif-SemiCondensedMediumItalic.ttf',
			'SemiBold' => 'NotoSerif-SemiCondensedSemiBold.ttf',
			'SemiBoldItalic' => 'NotoSerif-SemiCondensedSemiBoldItalic.ttf',
			'Bold' => 'NotoSerif-SemiCondensedBold.ttf',
			'BoldItalic' => 'NotoSerif-SemiCondensedBoldItalic.ttf',
			'ExtraBold' => 'NotoSerif-SemiCondensedExtraBold.ttf',
			'ExtraBoldItalic' => 'NotoSerif-SemiCondensedExtraBoldItalic.ttf',
			'Black' => 'NotoSerif-SemiCondensedBlack.ttf',
			'BlackItalic' => 'NotoSerif-SemiCondensedBlackItalic.ttf',
		],
		'NotoSerifExtraCondensed' => [
			'Thin' => 'NotoSerif-ExtraCondensedThin.ttf',
			'ThinItalic' => 'NotoSerif-ExtraCondensedThinItalic.ttf',
			'ExtraLight' => 'NotoSerif-ExtraCondensedExtraLight.ttf',
			'ExtraLightItalic' => 'NotoSerif-ExtraCondensedExtraLightItalic.ttf',
			'Light' => 'NotoSerif-ExtraCondensedLight.ttf',
			'LightItalic' => 'NotoSerif-ExtraCondensedLightItalic.ttf',
			'Regular' => 'NotoSerif-ExtraCondensed.ttf',
			'Italic' => 'NotoSerif-ExtraCondensedItalic.ttf',
			'Medium' => 'NotoSerif-ExtraCondensedMedium.ttf',
			'MediumItalic' => 'NotoSerif-ExtraCondensedMediumItalic.ttf',
			'SemiBold' => 'NotoSerif-ExtraCondensedSemiBold.ttf',
			'SemiBoldItalic' => 'NotoSerif-ExtraCondensedSemiBoldItalic.ttf',
			'Bold' => 'NotoSerif-ExtraCondensedBold.ttf',
			'BoldItalic' => 'NotoSerif-ExtraCondensedBoldItalic.ttf',
			'ExtraBold' => 'NotoSerif-ExtraCondensedExtraBold.ttf',
			'ExtraBoldItalic' => 'NotoSerif-ExtraCondensedExtraBoldItalic.ttf',
			'Black' => 'NotoSerif-ExtraCondensedBlack.ttf',
			'BlackItalic' => 'NotoSerif-ExtraCondensedBlackItalic.ttf',
		],
		'NotoSerifCondensed' => [
			'Thin' => 'NotoSerif-CondensedThin.ttf',
			'ThinItalic' => 'NotoSerif-CondensedThinItalic.ttf',
			'ExtraLight' => 'NotoSerif-CondensedExtraLight.ttf',
			'ExtraLightItalic' => 'NotoSerif-CondensedExtraLightItalic.ttf',
			'Light' => 'NotoSerif-CondensedLight.ttf',
			'LightItalic' => 'NotoSerif-CondensedLightItalic.ttf',
			'Regular' => 'NotoSerif-Condensed.ttf',
			'Italic' => 'NotoSerif-CondensedItalic.ttf',
			'Medium' => 'NotoSerif-CondensedMedium.ttf',
			'MediumItalic' => 'NotoSerif-CondensedMediumItalic.ttf',
			'SemiBold' => 'NotoSerif-CondensedSemiBold.ttf',
			'SemiBoldItalic' => 'NotoSerif-CondensedSemiBoldItalic.ttf',
			'Bold' => 'NotoSerif-CondensedBold.ttf',
			'BoldItalic' => 'NotoSerif-CondensedBoldItalic.ttf',
			'ExtraBold' => 'NotoSerif-CondensedExtraBold.ttf',
			'ExtraBoldItalic' => 'NotoSerif-CondensedExtraBoldItalic.ttf',
			'Black' => 'NotoSerif-CondensedBlack.ttf',
			'BlackItalic' => 'NotoSerif-CondensedBlackItalic.ttf',
		],
		'NotoSerif' => [
			'Thin' => 'NotoSerif-Thin.ttf',
			'ThinItalic' => 'NotoSerif-ThinItalic.ttf',
			'ExtraLight' => 'NotoSerif-ExtraLight.ttf',
			'ExtraLightItalic' => 'NotoSerif-ExtraLightItalic.ttf',
			'Light' => 'NotoSerif-Light.ttf',
			'LightItalic' => 'NotoSerif-LightItalic.ttf',
			'Regular' => 'NotoSerif-Regular.ttf',
			'Italic' => 'NotoSerif-Italic.ttf',
			'Medium' => 'NotoSerif-Medium.ttf',
			'MediumItalic' => 'NotoSerif-MediumItalic.ttf',
			'SemiBold' => 'NotoSerif-SemiBold.ttf',
			'SemiBoldItalic' => 'NotoSerif-SemiBoldItalic.ttf',
			'Bold' => 'NotoSerif-Bold.ttf',
			'BoldItalic' => 'NotoSerif-BoldItalic.ttf',
			'ExtraBold' => 'NotoSerif-ExtraBold.ttf',
			'ExtraBoldItalic' => 'NotoSerif-ExtraBoldItalic.ttf',
			'Black' => 'NotoSerif-Black.ttf',
			'BlackItalic' => 'NotoSerif-BlackItalic.ttf',
		],

		'SourceCodePro' => [
			'ExtraLight' => 'SourceCodePro-ExtraLight.ttf',
			'Light' => 'SourceCodePro-Light.ttf',
			'Regular' => 'SourceCodePro-Regular.ttf',
			'Medium' => 'SourceCodePro-Medium.ttf',
			'Semibold' => 'SourceCodePro-Semibold.ttf',
			'Bold' => 'SourceCodePro-Bold.ttf',
			'Black' => 'SourceCodePro-Black.ttf',
		],
		'SourceSerifPro' => [
			'Regular' => 'SourceSerifPro-Regular.ttf',
			'Semibold' => 'SourceSerifPro-Semibold.ttf',
			'Bold' => 'SourceSerifPro-Bold.ttf',
		],
		'SourceSansPro' => [
			'ExtraLight' => 'SourceSansPro-ExtraLight.ttf',
			'ExtraLightItalic' => 'SourceSansPro-ExtraLightItalic.ttf',
			'Light' => 'SourceSansPro-Light.ttf',
			'LightItalic' => 'SourceSansPro-LightItalic.ttf',
			'Regular' => 'SourceSansPro-Regular.ttf',
			'Italic' => 'SourceSansPro-Italic.ttf',
			'SemiBold' => 'SourceSansPro-SemiBold.ttf',
			'SemiBoldItalic' => 'SourceSansPro-SemiBoldItalic.ttf',
			'Bold' => 'SourceSansPro-Bold.ttf',
			'BoldItalic' => 'SourceSansPro-BoldItalic.ttf',
			'Black' => 'SourceSansPro-Black.ttf',
			'BlackItalic' => 'SourceSansPro-BlackItalic.ttf',
		],

		'PT Serif' => [
			'Regular' => 'PT_Serif-Regular.ttf',
			'Italic' => 'PT_Serif-Italic.ttf',
			'Bold' => 'PT_Serif-Bold.ttf',
			'BoldItalic' => 'PT_Serif-BoldItalic.ttf',
		],
		'PT Sans Narrow' => [
			'Narrow-Regular' => 'PT_Sans-Narrow-Regular.ttf',
			'Narrow-Bold' => 'PT_Sans-Narrow-Bold.ttf',
		],
		'PT Sans' => [
			'Regular' => 'PT_Sans-Regular.ttf',
			'Italic' => 'PT_Sans-Italic.ttf',
			'Bold' => 'PT_Sans-Bold.ttf',
			'BoldItalic' => 'PT_Sans-BoldItalic.ttf',
		],

		'PT_Mono' => [
			'Regular' => 'PT_Mono.ttf'
		],
	];
	protected $fontDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fonts' . DIRECTORY_SEPARATOR;
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
	protected $family = 'NotoSerif';
	/**
	 * Font weight
	 * @var string
	 */
	protected $weight = 'normal';
	/**
	 * Font style
	 * @var string
	 */
	protected $style = 'normal';
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
	 * Font height
	 * @var float
	 */
	protected $height = 0;
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
	 * Info needed to write in pdf
	 * @var array
	 */
	protected $outputInfo = [];
	/**
	 * @var float
	 */
	protected $unitsPerEm = 1000;
	/**
	 * Character widths
	 * @var int[]
	 */
	protected $widths = [];
	/**
	 * Cid to Gid characters map
	 * @var \YetiForcePDF\Objects\Basic\StreamObject
	 */
	protected $cidToGid;
	/**
	 * Cid system info
	 * @var \YetiForcePDF\Objects\Basic\DictionaryObject
	 */
	protected $cidSystemInfo;
	/**
	 * Character map (unicode)
	 * @var array
	 */
	protected $charMap = [];
	/**
	 * Unicode char map stream
	 * @var \YetiForcePDF\Objects\Basic\StreamObject
	 */
	protected $toUnicode;
	/**
	 * Main font that is used - first font - this file is just descendant font
	 * @var \YetiForcePDF\Objects\Basic\DictionaryObject
	 */
	protected $fontType0;
	/**
	 * From baseline to top of the font
	 * @var float
	 */
	protected $ascender = 0;
	/**
	 * From baseline to bottom (with jyg chars that are bellow baseline)
	 * @var float
	 */
	protected $descender = 0;
	/**
	 * @var string
	 */
	protected $fontPostscriptName;

	/**
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		$alreadyExists = $this->document->getFontInstance($this->family, $this->weight, $this->style);
		if (!$alreadyExists) {
			parent::init();
			$this->document->setFontInstance($this->family, $this->weight, $this->style, $this);
			$this->fontNumber = 'F' . $this->document->getActualFontId();
			$this->fontData = $this->loadFontData();
			$this->document->setFontData($this->family, $this->weight, $this->style, $this->fontData);
			$this->fontDescriptor = (new \YetiForcePDF\Objects\FontDescriptor())
				->setDocument($this->document)
				->setFont($this)
				->init();
			foreach ($this->document->getObjects('Page') as $page) {
				$page->synchronizeFonts();
			}
			return $this;
		}
		$this->setAddToDocument(false);
		// do not init parent! we don't want to create resources etc.
		return clone $alreadyExists;
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
	 * @param string $name
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
	 * Set font weight
	 * @param string $weight
	 * @return $this
	 */
	public function setWeight(string $weight)
	{
		$this->weight = $weight;
		return $this;
	}

	/**
	 * Get font name
	 * @return string
	 */
	public function getWeight(): string
	{
		return $this->weight;
	}

	/**
	 * Set font style
	 * @param string $style
	 * @return $this
	 */
	public function setStyle(string $style)
	{
		$this->style = $style;
		return $this;
	}

	/**
	 * Get font style
	 * @return string
	 */
	public function getStyle(): string
	{
		return $this->style;
	}

	/**
	 * Get full font name
	 * @return string
	 */
	public function getFullName()
	{
		return $this->fontPostscriptName;
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
	 * Get text width
	 * @param string $text
	 * @return float
	 */
	public function getTextWidth(string $text): float
	{
		$width = 0;
		for ($i = 0, $len = mb_strlen($text); $i < $len; $i++) {
			$char = mb_substr($text, $i, 1);
			$width += (float)$this->widths[mb_ord($char)];
		}
		return ($this->size * $width) / 1000;
	}

	/**
	 * Get text height
	 * @param string|null $text
	 * @return float
	 */
	public function getTextHeight(string $text = null): float
	{
		$height = $this->size * $this->height / $this->unitsPerEm;
		return $height;
	}

	/**
	 * Get ascender (from baseline to top of the bounding box)
	 * @return float
	 */
	public function getAscender(): float
	{
		return $this->size * $this->ascender / $this->unitsPerEm;
	}

	/**
	 * Get descender (from baseline to bottom of the bounding box)
	 * @return float
	 */
	public function getDescender(): float
	{
		return $this->size * $this->descender / $this->unitsPerEm;
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
		return round($value * ($base / $this->unitsPerEm));
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
	 * Get font file name without extension
	 * @return string
	 */
	public function getFontName()
	{
		$fontName = $this->family . '-';
		return $fontName;
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
		$os2 = $font->getData('OS/2');
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
		$this->outputInfo['descriptor']['Ascent'] = $hhea['ascent'];
		$this->outputInfo['descriptor']['Descent'] = $hhea['descent'];
		$this->ascender = $this->outputInfo['descriptor']['Ascent'];
		$this->descender = $this->outputInfo['descriptor']['Descent'];
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
		$this->charMap = $font->getUnicodeCharMap();
		$charMapUnicode = [];
		$cidToGid = str_pad('', 256 * 256 * 2, "\x00");
		foreach ($this->charMap as $c => $glyph) {
			// Set values in CID to GID map
			if ($c >= 0 && $c < 0xFFFF && $glyph) {
				$cidToGid[$c * 2] = chr($glyph >> 8);
				$cidToGid[$c * 2 + 1] = chr($glyph & 0xFF);
			}
			$width = $this->normalizeUnit(isset($hmtx[$glyph]) ? $hmtx[$glyph][0] : $hmtx[0][0]);
			$widths[] = $c . ' [' . $width . ']';
			$this->widths[$c] = $width;
		}
		$this->cidToGid = (new \YetiForcePDF\Objects\Basic\StreamObject())
			->setDocument($this->document)
			->init();
		$this->cidToGid->addRawContent($cidToGid)->setFilter('FlateDecode');
		$this->outputInfo['font']['Widths'] = $widths;
		$this->outputInfo['font']['FirstChar'] = 0;
		$this->outputInfo['font']['LastChar'] = count($widths) - 1;
		$this->height = (float)$hhea['ascent'] - (float)$hhea['descent'];
		if (isset($os2['typoLineGap'])) {
			$this->height += (float)$os2['typoLineGap'];
		}
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
		$this->fontPostscriptName = $font->getFontPostscriptName();
		$this->fontType0->setDictionaryType('Font')
			->addValue('Subtype', '/Type0')
			->addValue('BaseFont', '/' . $this->fontPostscriptName)
			->addValue('Encoding', '/Identity-H')
			->addValue('DescendantFonts', '[' . $this->getReference() . ']')
			->addValue('ToUnicode', $this->toUnicode->getReference());
		$font->close();
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
			"  /Subtype /CIDFontType2",
			"  /BaseFont /" . $this->getFullName(),
			"  /FontDescriptor " . $this->fontDescriptor->getReference(),
			'  /DW 500',
			'  /W [' . implode(' ', $this->outputInfo['font']['Widths']) . ' ]',
			'  /CIDSystemInfo ' . $this->cidSystemInfo->getReference(),
			'  /CIDToGIDMap ' . $this->cidToGid->getReference(),
			">>",
			"endobj"]);
	}

}
