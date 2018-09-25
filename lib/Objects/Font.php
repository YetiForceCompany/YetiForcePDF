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
		'NotoSerif-SemiBoldItalic' => 'NotoSerif-SemiBoldItalic.ttf',
		'NotoSans-ExtraCondensedExtraBold' => 'NotoSans-ExtraCondensedExtraBold.ttf',
		'NotoSans-CondensedExtraBold' => 'NotoSans-CondensedExtraBold.ttf',
		'NotoSans-SemiCondensedMediumItalic' => 'NotoSans-SemiCondensedMediumItalic.ttf',
		'NotoSans-SemiBoldItalic' => 'NotoSans-SemiBoldItalic.ttf',
		'NotoMono-Regular' => 'NotoMono-Regular.ttf',
		'NotoSans-ThinItalic' => 'NotoSans-ThinItalic.ttf',
		'NotoSans-Thin' => 'NotoSans-Thin.ttf',
		'NotoSans-SemiCondensedThinItalic' => 'NotoSans-SemiCondensedThinItalic.ttf',
		'NotoSans-SemiCondensedThin' => 'NotoSans-SemiCondensedThin.ttf',
		'NotoSans-SemiCondensedSemiBoldItalic' => 'NotoSans-SemiCondensedSemiBoldItalic.ttf',
		'NotoSans-SemiCondensedSemiBold' => 'NotoSans-SemiCondensedSemiBold.ttf',
		'NotoSans-SemiCondensedMedium' => 'NotoSans-SemiCondensedMedium.ttf',
		'NotoSans-SemiCondensedLightItalic' => 'NotoSans-SemiCondensedLightItalic.ttf',
		'NotoSans-SemiCondensedLight' => 'NotoSans-SemiCondensedLight.ttf',
		'NotoSans-SemiCondensedItalic' => 'NotoSans-SemiCondensedItalic.ttf',
		'NotoSans-SemiCondensedExtraLightItalic' => 'NotoSans-SemiCondensedExtraLightItalic.ttf',
		'NotoSans-SemiCondensedExtraLight' => 'NotoSans-SemiCondensedExtraLight.ttf',
		'NotoSans-SemiCondensedExtraBoldItalic' => 'NotoSans-SemiCondensedExtraBoldItalic.ttf',
		'NotoSans-SemiCondensedExtraBold' => 'NotoSans-SemiCondensedExtraBold.ttf',
		'NotoSans-SemiCondensedBoldItalic' => 'NotoSans-SemiCondensedBoldItalic.ttf',
		'NotoSans-SemiCondensedBold' => 'NotoSans-SemiCondensedBold.ttf',
		'NotoSans-SemiCondensedBlackItalic' => 'NotoSans-SemiCondensedBlackItalic.ttf',
		'NotoSans-SemiCondensedBlack' => 'NotoSans-SemiCondensedBlack.ttf',
		'NotoSans-SemiCondensed' => 'NotoSans-SemiCondensed.ttf',
		'NotoSans-SemiBold' => 'NotoSans-SemiBold.ttf',
		'NotoSans-Regular' => 'NotoSans-Regular.ttf',
		'NotoSans-MediumItalic' => 'NotoSans-MediumItalic.ttf',
		'NotoSans-Medium' => 'NotoSans-Medium.ttf',
		'NotoSans-LightItalic' => 'NotoSans-LightItalic.ttf',
		'NotoSans-Light' => 'NotoSans-Light.ttf',
		'NotoSans-Italic' => 'NotoSans-Italic.ttf',
		'NotoSans-ExtraLightItalic' => 'NotoSans-ExtraLightItalic.ttf',
		'NotoSans-ExtraLight' => 'NotoSans-ExtraLight.ttf',
		'NotoSans-ExtraCondensedThinItalic' => 'NotoSans-ExtraCondensedThinItalic.ttf',
		'NotoSans-ExtraCondensedThin' => 'NotoSans-ExtraCondensedThin.ttf',
		'NotoSans-ExtraCondensedSemiBoldItalic' => 'NotoSans-ExtraCondensedSemiBoldItalic.ttf',
		'NotoSans-ExtraCondensedSemiBold' => 'NotoSans-ExtraCondensedSemiBold.ttf',
		'NotoSans-ExtraCondensedMediumItalic' => 'NotoSans-ExtraCondensedMediumItalic.ttf',
		'NotoSans-ExtraCondensedMedium' => 'NotoSans-ExtraCondensedMedium.ttf',
		'NotoSans-ExtraCondensedLightItalic' => 'NotoSans-ExtraCondensedLightItalic.ttf',
		'NotoSans-ExtraCondensedLight' => 'NotoSans-ExtraCondensedLight.ttf',
		'NotoSans-ExtraCondensedItalic' => 'NotoSans-ExtraCondensedItalic.ttf',
		'NotoSans-ExtraCondensedExtraLightItalic' => 'NotoSans-ExtraCondensedExtraLightItalic.ttf',
		'NotoSans-ExtraCondensedExtraLight' => 'NotoSans-ExtraCondensedExtraLight.ttf',
		'NotoSans-ExtraCondensedExtraBoldItalic' => 'NotoSans-ExtraCondensedExtraBoldItalic.ttf',
		'NotoSans-ExtraCondensedBoldItalic' => 'NotoSans-ExtraCondensedBoldItalic.ttf',
		'NotoSans-ExtraCondensedBold' => 'NotoSans-ExtraCondensedBold.ttf',
		'NotoSans-ExtraCondensedBlackItalic' => 'NotoSans-ExtraCondensedBlackItalic.ttf',
		'NotoSans-ExtraCondensedBlack' => 'NotoSans-ExtraCondensedBlack.ttf',
		'NotoSans-ExtraCondensed' => 'NotoSans-ExtraCondensed.ttf',
		'NotoSans-ExtraBoldItalic' => 'NotoSans-ExtraBoldItalic.ttf',
		'NotoSans-ExtraBold' => 'NotoSans-ExtraBold.ttf',
		'NotoSans-CondensedThinItalic' => 'NotoSans-CondensedThinItalic.ttf',
		'NotoSans-CondensedThin' => 'NotoSans-CondensedThin.ttf',
		'NotoSans-CondensedSemiBoldItalic' => 'NotoSans-CondensedSemiBoldItalic.ttf',
		'NotoSans-CondensedSemiBold' => 'NotoSans-CondensedSemiBold.ttf',
		'NotoSans-CondensedMediumItalic' => 'NotoSans-CondensedMediumItalic.ttf',
		'NotoSans-CondensedMedium' => 'NotoSans-CondensedMedium.ttf',
		'NotoSans-CondensedLightItalic' => 'NotoSans-CondensedLightItalic.ttf',
		'NotoSans-CondensedLight' => 'NotoSans-CondensedLight.ttf',
		'NotoSans-CondensedItalic' => 'NotoSans-CondensedItalic.ttf',
		'NotoSans-CondensedExtraLightItalic' => 'NotoSans-CondensedExtraLightItalic.ttf',
		'NotoSans-CondensedExtraLight' => 'NotoSans-CondensedExtraLight.ttf',
		'NotoSans-CondensedExtraBoldItalic' => 'NotoSans-CondensedExtraBoldItalic.ttf',
		'NotoSans-CondensedBoldItalic' => 'NotoSans-CondensedBoldItalic.ttf',
		'NotoSans-CondensedBold' => 'NotoSans-CondensedBold.ttf',
		'NotoSans-CondensedBlackItalic' => 'NotoSans-CondensedBlackItalic.ttf',
		'NotoSans-CondensedBlack' => 'NotoSans-CondensedBlack.ttf',
		'NotoSans-Condensed' => 'NotoSans-Condensed.ttf',
		'NotoSans-BoldItalic' => 'NotoSans-BoldItalic.ttf',
		'NotoSans-Bold' => 'NotoSans-Bold.ttf',
		'NotoSans-BlackItalic' => 'NotoSans-BlackItalic.ttf',
		'NotoSans-Black' => 'NotoSans-Black.ttf',
		'NotoSerif-ThinItalic' => 'NotoSerif-ThinItalic.ttf',
		'NotoSerif-Thin' => 'NotoSerif-Thin.ttf',
		'NotoSerif-SemiCondensedThinItalic' => 'NotoSerif-SemiCondensedThinItalic.ttf',
		'NotoSerif-SemiCondensedThin' => 'NotoSerif-SemiCondensedThin.ttf',
		'NotoSerif-SemiCondensedSemiBoldItalic' => 'NotoSerif-SemiCondensedSemiBoldItalic.ttf',
		'NotoSerif-SemiCondensedSemiBold' => 'NotoSerif-SemiCondensedSemiBold.ttf',
		'NotoSerif-SemiCondensedMediumItalic' => 'NotoSerif-SemiCondensedMediumItalic.ttf',
		'NotoSerif-SemiCondensedMedium' => 'NotoSerif-SemiCondensedMedium.ttf',
		'NotoSerif-SemiCondensedLightItalic' => 'NotoSerif-SemiCondensedLightItalic.ttf',
		'NotoSerif-SemiCondensedLight' => 'NotoSerif-SemiCondensedLight.ttf',
		'NotoSerif-SemiCondensedItalic' => 'NotoSerif-SemiCondensedItalic.ttf',
		'NotoSerif-SemiCondensedExtraLightItalic' => 'NotoSerif-SemiCondensedExtraLightItalic.ttf',
		'NotoSerif-SemiCondensedExtraLight' => 'NotoSerif-SemiCondensedExtraLight.ttf',
		'NotoSerif-SemiCondensedExtraBoldItalic' => 'NotoSerif-SemiCondensedExtraBoldItalic.ttf',
		'NotoSerif-SemiCondensedExtraBold' => 'NotoSerif-SemiCondensedExtraBold.ttf',
		'NotoSerif-SemiCondensedBoldItalic' => 'NotoSerif-SemiCondensedBoldItalic.ttf',
		'NotoSerif-SemiCondensedBold' => 'NotoSerif-SemiCondensedBold.ttf',
		'NotoSerif-SemiCondensedBlackItalic' => 'NotoSerif-SemiCondensedBlackItalic.ttf',
		'NotoSerif-SemiCondensedBlack' => 'NotoSerif-SemiCondensedBlack.ttf',
		'NotoSerif-SemiCondensed' => 'NotoSerif-SemiCondensed.ttf',
		'NotoSerif-SemiBold' => 'NotoSerif-SemiBold.ttf',
		'NotoSerif-Regular' => 'NotoSerif-Regular.ttf',
		'NotoSerif-MediumItalic' => 'NotoSerif-MediumItalic.ttf',
		'NotoSerif-Medium' => 'NotoSerif-Medium.ttf',
		'NotoSerif-LightItalic' => 'NotoSerif-LightItalic.ttf',
		'NotoSerif-Light' => 'NotoSerif-Light.ttf',
		'NotoSerif-Italic' => 'NotoSerif-Italic.ttf',
		'NotoSerif-ExtraLightItalic' => 'NotoSerif-ExtraLightItalic.ttf',
		'NotoSerif-ExtraLight' => 'NotoSerif-ExtraLight.ttf',
		'NotoSerif-ExtraCondensedThinItalic' => 'NotoSerif-ExtraCondensedThinItalic.ttf',
		'NotoSerif-ExtraCondensedThin' => 'NotoSerif-ExtraCondensedThin.ttf',
		'NotoSerif-ExtraCondensedSemiBoldItalic' => 'NotoSerif-ExtraCondensedSemiBoldItalic.ttf',
		'NotoSerif-ExtraCondensedSemiBold' => 'NotoSerif-ExtraCondensedSemiBold.ttf',
		'NotoSerif-ExtraCondensedMediumItalic' => 'NotoSerif-ExtraCondensedMediumItalic.ttf',
		'NotoSerif-ExtraCondensedMedium' => 'NotoSerif-ExtraCondensedMedium.ttf',
		'NotoSerif-ExtraCondensedLightItalic' => 'NotoSerif-ExtraCondensedLightItalic.ttf',
		'NotoSerif-ExtraCondensedLight' => 'NotoSerif-ExtraCondensedLight.ttf',
		'NotoSerif-ExtraCondensedItalic' => 'NotoSerif-ExtraCondensedItalic.ttf',
		'NotoSerif-ExtraCondensedExtraLightItalic' => 'NotoSerif-ExtraCondensedExtraLightItalic.ttf',
		'NotoSerif-ExtraCondensedExtraLight' => 'NotoSerif-ExtraCondensedExtraLight.ttf',
		'NotoSerif-ExtraCondensedExtraBoldItalic' => 'NotoSerif-ExtraCondensedExtraBoldItalic.ttf',
		'NotoSerif-ExtraCondensedExtraBold' => 'NotoSerif-ExtraCondensedExtraBold.ttf',
		'NotoSerif-ExtraCondensedBoldItalic' => 'NotoSerif-ExtraCondensedBoldItalic.ttf',
		'NotoSerif-ExtraCondensedBold' => 'NotoSerif-ExtraCondensedBold.ttf',
		'NotoSerif-ExtraCondensedBlackItalic' => 'NotoSerif-ExtraCondensedBlackItalic.ttf',
		'NotoSerif-ExtraCondensedBlack' => 'NotoSerif-ExtraCondensedBlack.ttf',
		'NotoSerif-ExtraCondensed' => 'NotoSerif-ExtraCondensed.ttf',
		'NotoSerif-ExtraBoldItalic' => 'NotoSerif-ExtraBoldItalic.ttf',
		'NotoSerif-ExtraBold' => 'NotoSerif-ExtraBold.ttf',
		'NotoSerif-CondensedThinItalic' => 'NotoSerif-CondensedThinItalic.ttf',
		'NotoSerif-CondensedThin' => 'NotoSerif-CondensedThin.ttf',
		'NotoSerif-CondensedSemiBoldItalic' => 'NotoSerif-CondensedSemiBoldItalic.ttf',
		'NotoSerif-CondensedSemiBold' => 'NotoSerif-CondensedSemiBold.ttf',
		'NotoSerif-CondensedMediumItalic' => 'NotoSerif-CondensedMediumItalic.ttf',
		'NotoSerif-CondensedMedium' => 'NotoSerif-CondensedMedium.ttf',
		'NotoSerif-CondensedLightItalic' => 'NotoSerif-CondensedLightItalic.ttf',
		'NotoSerif-CondensedLight' => 'NotoSerif-CondensedLight.ttf',
		'NotoSerif-CondensedItalic' => 'NotoSerif-CondensedItalic.ttf',
		'NotoSerif-CondensedExtraLightItalic' => 'NotoSerif-CondensedExtraLightItalic.ttf',
		'NotoSerif-CondensedExtraLight' => 'NotoSerif-CondensedExtraLight.ttf',
		'NotoSerif-CondensedExtraBoldItalic' => 'NotoSerif-CondensedExtraBoldItalic.ttf',
		'NotoSerif-CondensedExtraBold' => 'NotoSerif-CondensedExtraBold.ttf',
		'NotoSerif-CondensedBoldItalic' => 'NotoSerif-CondensedBoldItalic.ttf',
		'NotoSerif-CondensedBold' => 'NotoSerif-CondensedBold.ttf',
		'NotoSerif-CondensedBlackItalic' => 'NotoSerif-CondensedBlackItalic.ttf',
		'NotoSerif-CondensedBlack' => 'NotoSerif-CondensedBlack.ttf',
		'NotoSerif-Condensed' => 'NotoSerif-Condensed.ttf',
		'NotoSerif-BoldItalic' => 'NotoSerif-BoldItalic.ttf',
		'NotoSerif-Bold' => 'NotoSerif-Bold.ttf',
		'NotoSerif-BlackItalic' => 'NotoSerif-BlackItalic.ttf',
		'NotoSerif-Black' => 'NotoSerif-Black.ttf',
		'SourceCodePro-Bold' => 'SourceCodePro-Bold.ttf',
		'SourceSerifPro-Bold' => 'SourceSerifPro-Bold.ttf',
		'SourceSerifPro-Semibold' => 'SourceSerifPro-Semibold.ttf',
		'SourceSerifPro-Regular' => 'SourceSerifPro-Regular.ttf',
		'SourceSansPro-BlackItalic' => 'SourceSansPro-BlackItalic.ttf',
		'SourceSansPro-Black' => 'SourceSansPro-Black.ttf',
		'SourceSansPro-BoldItalic' => 'SourceSansPro-BoldItalic.ttf',
		'SourceSansPro-Bold' => 'SourceSansPro-Bold.ttf',
		'SourceSansPro-SemiBoldItalic' => 'SourceSansPro-SemiBoldItalic.ttf',
		'SourceSansPro-SemiBold' => 'SourceSansPro-SemiBold.ttf',
		'SourceSansPro-Italic' => 'SourceSansPro-Italic.ttf',
		'SourceSansPro-Regular' => 'SourceSansPro-Regular.ttf',
		'SourceSansPro-LightItalic' => 'SourceSansPro-LightItalic.ttf',
		'SourceSansPro-Light' => 'SourceSansPro-Light.ttf',
		'SourceSansPro-ExtraLightItalic' => 'SourceSansPro-ExtraLightItalic.ttf',
		'SourceSansPro-ExtraLight' => 'SourceSansPro-ExtraLight.ttf',
		'SourceCodePro-Black' => 'SourceCodePro-Black.ttf',
		'SourceCodePro-Semibold' => 'SourceCodePro-Semibold.ttf',
		'SourceCodePro-Medium' => 'SourceCodePro-Medium.ttf',
		'SourceCodePro-Regular' => 'SourceCodePro-Regular.ttf',
		'SourceCodePro-Light' => 'SourceCodePro-Light.ttf',
		'SourceCodePro-ExtraLight' => 'SourceCodePro-ExtraLight.ttf',
		'PT_Serif-BoldItalic' => 'PT_Serif-BoldItalic.ttf',
		'PT_Serif-Bold' => 'PT_Serif-Bold.ttf',
		'PT_Serif-Italic' => 'PT_Serif-Italic.ttf',
		'PT_Serif-Regular' => 'PT_Serif-Regular.ttf',
		'PT_Sans-Narrow-Bold' => 'PT_Sans-Narrow-Bold.ttf',
		'PT_Sans-Narrow-Regular' => 'PT_Sans-Narrow-Regular.ttf',
		'PT_Sans-BoldItalic' => 'PT_Sans-BoldItalic.ttf',
		'PT_Sans-Bold' => 'PT_Sans-Bold.ttf',
		'PT_Sans-Italic' => 'PT_Sans-Italic.ttf',
		'PT_Sans-Regular' => 'PT_Sans-Regular.ttf',
		'PT_Mono' => 'PT_Mono.ttf',
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
	protected $family = 'NotoSans-Regular';
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
	 * Main font that is used - first font - this file is just descendant font
	 * @var \YetiForcePDF\Objects\Basic\DictionaryObject
	 */
	protected $fontType0;

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
		$this->charMap = $font->getUnicodeCharMap();
		$charMapUnicode = [];
		$cidToGid = str_pad('', 256 * 256 * 2, "\x00");
		foreach ($this->charMap as $c => $glyph) {
			// Set values in CID to GID map
			if ($c >= 0 && $c < 0xFFFF && $glyph) {
				$cidToGid[$c * 2] = chr($glyph >> 8);
				$cidToGid[$c * 2 + 1] = chr($glyph & 0xFF);
			}
			$widths[] = $c . ' [' . $this->normalizeUnit(isset($hmtx[$glyph]) ? $hmtx[$glyph][0] : $hmtx[0][0]) . ']';
		}
		$this->cidToGid = (new \YetiForcePDF\Objects\Basic\StreamObject())
			->setDocument($this->document)
			->init();
		$this->cidToGid->addRawContent($cidToGid)->setFilter('FlateDecode');
		$this->outputInfo['font']['Widths'] = $widths;
		$this->outputInfo['font']['FirstChar'] = 0;
		$this->outputInfo['font']['LastChar'] = count($widths) - 1;
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
			->addValue('Subtype', '/Type0')
			->addValue('BaseFont', '/' . $font->getFontPostscriptName())
			->addValue('Encoding', '/Identity-H')
			->addValue('DescendantFonts', '[' . $this->getReference() . ']')
			->addValue('ToUnicode', $this->toUnicode->getReference());
		$font->close();
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
