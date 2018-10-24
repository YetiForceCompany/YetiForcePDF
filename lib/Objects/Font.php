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
			'100' => [
				'Regular' => 'NotoSans-CondensedThin.ttf',
				'Italic' => 'NotoSans-CondensedThinItalic.ttf',
			],
			'200' => [
				'Regular' => 'NotoSans-CondensedExtraLight.ttf',
				'Italic' => 'NotoSans-CondensedExtraLightItalic.ttf',
			],
			'300' => [
				'Regular' => 'NotoSans-CondensedLight.ttf',
				'Italic' => 'NotoSans-CondensedLightItalic.ttf',
			],
			'400' => [
				'Regular' => 'NotoSans-Condensed.ttf',
				'Italic' => 'NotoSans-CondensedItalic.ttf',
			],
			'500' => [
				'Regular' => 'NotoSans-CondensedMedium.ttf',
				'Italic' => 'NotoSans-CondensedMediumItalic.ttf',
			],
			'600' => [
				'Regular' => 'NotoSans-CondensedSemiBold.ttf',
				'Italic' => 'NotoSans-CondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'Regular' => 'NotoSans-CondensedBold.ttf',
				'Italic' => 'NotoSans-CondensedBoldItalic.ttf',
			],
			'800' => [
				'Regular' => 'NotoSans-CondensedExtraBold.ttf',
				'Italic' => 'NotoSans-CondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'Regular' => 'NotoSans-CondensedBlack.ttf',
				'Italic' => 'NotoSans-CondensedBlackItalic.ttf',
			],
		],
		'NotoSansSemiCondensed' => [
			'100' => [
				'Thin' => 'NotoSans-SemiCondensedThin.ttf',
				'ThinItalic' => 'NotoSans-SemiCondensedThinItalic.ttf',
			],
			'200' => [
				'ExtraLight' => 'NotoSans-SemiCondensedExtraLight.ttf',
				'ExtraLightItalic' => 'NotoSans-SemiCondensedExtraLightItalic.ttf',
			],
			'300' => [
				'Light' => 'NotoSans-SemiCondensedLight.ttf',
				'LightItalic' => 'NotoSans-SemiCondensedLightItalic.ttf',
			],
			'400' => [
				'Regular' => 'NotoSans-SemiCondensed.ttf',
				'Italic' => 'NotoSans-SemiCondensedItalic.ttf',
			],
			'500' => [
				'Medium' => 'NotoSans-SemiCondensedMedium.ttf',
				'MediumItalic' => 'NotoSans-SemiCondensedMediumItalic.ttf',
			],
			'600' => [
				'SemiBold' => 'NotoSans-SemiCondensedSemiBold.ttf',
				'SemiBoldItalic' => 'NotoSans-SemiCondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'Bold' => 'NotoSans-SemiCondensedBold.ttf',
				'BoldItalic' => 'NotoSans-SemiCondensedBoldItalic.ttf',
			],
			'800' => [
				'ExtraBold' => 'NotoSans-SemiCondensedExtraBold.ttf',
				'ExtraBoldItalic' => 'NotoSans-SemiCondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'Black' => 'NotoSans-SemiCondensedBlack.ttf',
				'BlackItalic' => 'NotoSans-SemiCondensedBlackItalic.ttf',
			],
		],
		'NotoSansExtraCondensed' => [
			'100' => [
				'Thin' => 'NotoSans-ExtraCondensedThin.ttf',
				'ThinItalic' => 'NotoSans-ExtraCondensedThinItalic.ttf',
			],
			'200' => [
				'ExtraLight' => 'NotoSans-ExtraCondensedExtraLight.ttf',
				'ExtraLightItalic' => 'NotoSans-ExtraCondensedExtraLightItalic.ttf',
			],
			'300' => [
				'Light' => 'NotoSans-ExtraCondensedLight.ttf',
				'LightItalic' => 'NotoSans-ExtraCondensedLightItalic.ttf',
			],
			'400' => [
				'Regular' => 'NotoSans-ExtraCondensed.ttf',
				'Italic' => 'NotoSans-ExtraCondensedItalic.ttf',
			],
			'500' => [
				'Medium' => 'NotoSans-ExtraCondensedMedium.ttf',
				'MediumItalic' => 'NotoSans-ExtraCondensedMediumItalic.ttf',
			],
			'600' => [
				'SemiBold' => 'NotoSans-ExtraCondensedSemiBold.ttf',
				'SemiBoldItalic' => 'NotoSans-ExtraCondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'Bold' => 'NotoSans-ExtraCondensedBold.ttf',
				'BoldItalic' => 'NotoSans-ExtraCondensedBoldItalic.ttf',
			],
			'800' => [
				'ExtraBold' => 'NotoSans-ExtraCondensedExtraBold.ttf',
				'ExtraBoldItalic' => 'NotoSans-ExtraCondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'Black' => 'NotoSans-ExtraCondensedBlack.ttf',
				'BlackItalic' => 'NotoSans-ExtraCondensedBlackItalic.ttf',
			],
		],
		'NotoSans' => [
			'100' => [
				'Thin' => 'NotoSans-Thin.ttf',
				'ThinItalic' => 'NotoSans-ThinItalic.ttf',
			],
			'200' => [
				'ExtraLight' => 'NotoSans-ExtraLight.ttf',
				'ExtraLightItalic' => 'NotoSans-ExtraLightItalic.ttf',
			],
			'300' => [
				'Light' => 'NotoSans-Light.ttf',
				'LightItalic' => 'NotoSans-LightItalic.ttf',
			],
			'400' => [
				'Regular' => 'NotoMono-Regular.ttf',
				'Italic' => 'NotoSans-Italic.ttf',
			],
			'500' => [
				'Medium' => 'NotoSans-Medium.ttf',
				'MediumItalic' => 'NotoSans-MediumItalic.ttf',
			],
			'600' => [
				'SemiBold' => 'NotoSans-SemiBold.ttf',
				'SemiBoldItalic' => 'NotoSans-SemiBoldItalic.ttf',
			],
			'700' => [
				'Bold' => 'NotoSans-Bold.ttf',
				'BoldItalic' => 'NotoSans-BoldItalic.ttf',
			],
			'800' => [
				'ExtraBold' => 'NotoSans-ExtraBold.ttf',
				'ExtraBoldItalic' => 'NotoSans-ExtraBoldItalic.ttf',
			],
			'900' => [
				'Black' => 'NotoSans-Black.ttf',
				'BlackItalic' => 'NotoSans-BlackItalic.ttf',
			],
		],

		'NotoSerifSemiCondensed' => [
			'100' => [
				'Thin' => 'NotoSerif-SemiCondensedThin.ttf',
				'ThinItalic' => 'NotoSerif-SemiCondensedThinItalic.ttf',
			],
			'200' => [
				'ExtraLight' => 'NotoSerif-SemiCondensedExtraLight.ttf',
				'ExtraLightItalic' => 'NotoSerif-SemiCondensedExtraLightItalic.ttf',
			],
			'300' => [
				'Light' => 'NotoSerif-SemiCondensedLight.ttf',
				'LightItalic' => 'NotoSerif-SemiCondensedLightItalic.ttf',
			],
			'400' => [
				'Regular' => 'NotoSerif-SemiCondensed.ttf',
				'Italic' => 'NotoSerif-SemiCondensedItalic.ttf',
			],
			'500' => [
				'Medium' => 'NotoSerif-SemiCondensedMedium.ttf',
				'MediumItalic' => 'NotoSerif-SemiCondensedMediumItalic.ttf',
			],
			'600' => [
				'SemiBold' => 'NotoSerif-SemiCondensedSemiBold.ttf',
				'SemiBoldItalic' => 'NotoSerif-SemiCondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'Bold' => 'NotoSerif-SemiCondensedBold.ttf',
				'BoldItalic' => 'NotoSerif-SemiCondensedBoldItalic.ttf',
			],
			'800' => [
				'ExtraBold' => 'NotoSerif-SemiCondensedExtraBold.ttf',
				'ExtraBoldItalic' => 'NotoSerif-SemiCondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'Black' => 'NotoSerif-SemiCondensedBlack.ttf',
				'BlackItalic' => 'NotoSerif-SemiCondensedBlackItalic.ttf',
			],
		],
		'NotoSerifExtraCondensed' => [
			'100' => [
				'Thin' => 'NotoSerif-ExtraCondensedThin.ttf',
				'ThinItalic' => 'NotoSerif-ExtraCondensedThinItalic.ttf',
			],
			'200' => [
				'ExtraLight' => 'NotoSerif-ExtraCondensedExtraLight.ttf',
				'ExtraLightItalic' => 'NotoSerif-ExtraCondensedExtraLightItalic.ttf',
			],
			'300' => [
				'Light' => 'NotoSerif-ExtraCondensedLight.ttf',
				'LightItalic' => 'NotoSerif-ExtraCondensedLightItalic.ttf',
			],
			'400' => [
				'Regular' => 'NotoSerif-ExtraCondensed.ttf',
				'Italic' => 'NotoSerif-ExtraCondensedItalic.ttf',
			],
			'500' => [
				'Medium' => 'NotoSerif-ExtraCondensedMedium.ttf',
				'MediumItalic' => 'NotoSerif-ExtraCondensedMediumItalic.ttf',
			],
			'600' => [
				'SemiBold' => 'NotoSerif-ExtraCondensedSemiBold.ttf',
				'SemiBoldItalic' => 'NotoSerif-ExtraCondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'Bold' => 'NotoSerif-ExtraCondensedBold.ttf',
				'BoldItalic' => 'NotoSerif-ExtraCondensedBoldItalic.ttf',
			],
			'800' => [
				'ExtraBold' => 'NotoSerif-ExtraCondensedExtraBold.ttf',
				'ExtraBoldItalic' => 'NotoSerif-ExtraCondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'Black' => 'NotoSerif-ExtraCondensedBlack.ttf',
				'BlackItalic' => 'NotoSerif-ExtraCondensedBlackItalic.ttf',
			],
		],
		'NotoSerifCondensed' => [
			'100' => [
				'Thin' => 'NotoSerif-CondensedThin.ttf',
				'ThinItalic' => 'NotoSerif-CondensedThinItalic.ttf',
			],
			'200' => [
				'ExtraLight' => 'NotoSerif-CondensedExtraLight.ttf',
				'ExtraLightItalic' => 'NotoSerif-CondensedExtraLightItalic.ttf',
			],
			'300' => [
				'Light' => 'NotoSerif-CondensedLight.ttf',
				'LightItalic' => 'NotoSerif-CondensedLightItalic.ttf',
			],
			'400' => [
				'Regular' => 'NotoSerif-Condensed.ttf',
				'Italic' => 'NotoSerif-CondensedItalic.ttf',
			],
			'500' => [
				'Medium' => 'NotoSerif-CondensedMedium.ttf',
				'MediumItalic' => 'NotoSerif-CondensedMediumItalic.ttf',
			],
			'600' => [
				'SemiBold' => 'NotoSerif-CondensedSemiBold.ttf',
				'SemiBoldItalic' => 'NotoSerif-CondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'Bold' => 'NotoSerif-CondensedBold.ttf',
				'BoldItalic' => 'NotoSerif-CondensedBoldItalic.ttf',
			],
			'800' => [
				'ExtraBold' => 'NotoSerif-CondensedExtraBold.ttf',
				'ExtraBoldItalic' => 'NotoSerif-CondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'Black' => 'NotoSerif-CondensedBlack.ttf',
				'BlackItalic' => 'NotoSerif-CondensedBlackItalic.ttf',
			],
		],
		'NotoSerif' => [
			'100' => [
				'Thin' => 'NotoSerif-Thin.ttf',
				'ThinItalic' => 'NotoSerif-ThinItalic.ttf',
			],
			'200' => [
				'ExtraLight' => 'NotoSerif-ExtraLight.ttf',
				'ExtraLightItalic' => 'NotoSerif-ExtraLightItalic.ttf',
			],
			'300' => [
				'Light' => 'NotoSerif-Light.ttf',
				'LightItalic' => 'NotoSerif-LightItalic.ttf',
			],
			'400' => [
				'Regular' => 'NotoSerif-Regular.ttf',
				'Italic' => 'NotoSerif-Italic.ttf',
			],
			'500' => [
				'Medium' => 'NotoSerif-Medium.ttf',
				'MediumItalic' => 'NotoSerif-MediumItalic.ttf',
			],
			'600' => [
				'SemiBold' => 'NotoSerif-SemiBold.ttf',
				'SemiBoldItalic' => 'NotoSerif-SemiBoldItalic.ttf',
			],
			'700' => [
				'Bold' => 'NotoSerif-Bold.ttf',
				'BoldItalic' => 'NotoSerif-BoldItalic.ttf',
			],
			'800' => [
				'ExtraBold' => 'NotoSerif-ExtraBold.ttf',
				'ExtraBoldItalic' => 'NotoSerif-ExtraBoldItalic.ttf',
			],
			'900' => [
				'Black' => 'NotoSerif-Black.ttf',
				'BlackItalic' => 'NotoSerif-BlackItalic.ttf',
			],
		],

		'SourceCodePro' => [
			'100' => [
				'ExtraLight' => 'SourceCodePro-ExtraLight.ttf',
			],
			'200' => [
				'ExtraLight' => 'SourceCodePro-ExtraLight.ttf',
			],
			'300' => [
				'Light' => 'SourceCodePro-Light.ttf',
			],
			'400' => [
				'Regular' => 'SourceCodePro-Regular.ttf',
			],
			'500' => [
				'Medium' => 'SourceCodePro-Medium.ttf',
			],
			'600' => [
				'Semibold' => 'SourceCodePro-Semibold.ttf',
			],
			'700' => [
				'Bold' => 'SourceCodePro-Bold.ttf',
			],
			'800' => [
				'Bold' => 'SourceCodePro-Bold.ttf',
			],
			'900' => [
				'Black' => 'SourceCodePro-Black.ttf',
			],
		],
		'SourceSerifPro' => [
			'100' => [
				'Regular' => 'SourceSerifPro-Regular.ttf',
			],
			'200' => [
				'Regular' => 'SourceSerifPro-Regular.ttf',
			],
			'300' => [
				'Regular' => 'SourceSerifPro-Regular.ttf',
			],
			'400' => [
				'Regular' => 'SourceSerifPro-Regular.ttf',
			],
			'500' => [
				'Regular' => 'SourceSerifPro-Regular.ttf',
			],
			'600' => [
				'Semibold' => 'SourceSerifPro-Semibold.ttf',
			],
			'700' => [
				'Bold' => 'SourceSerifPro-Bold.ttf',
			],
			'800' => [
				'Bold' => 'SourceSerifPro-Bold.ttf',
			],
			'900' => [
				'Bold' => 'SourceSerifPro-Bold.ttf',
			],
		],
		'SourceSansPro' => [
			'100' => [
				'ExtraLight' => 'SourceSansPro-ExtraLight.ttf',
				'ExtraLightItalic' => 'SourceSansPro-ExtraLightItalic.ttf',
			],
			'200' => [
				'ExtraLight' => 'SourceSansPro-ExtraLight.ttf',
				'ExtraLightItalic' => 'SourceSansPro-ExtraLightItalic.ttf',
			],
			'300' => [
				'Light' => 'SourceSansPro-Light.ttf',
				'LightItalic' => 'SourceSansPro-LightItalic.ttf',
			],
			'400' => [
				'Regular' => 'SourceSansPro-Regular.ttf',
				'Italic' => 'SourceSansPro-Italic.ttf',
			],
			'500' => [
				'SemiBold' => 'SourceSansPro-SemiBold.ttf',
				'SemiBoldItalic' => 'SourceSansPro-SemiBoldItalic.ttf',
			],
			'600' => [
				'SemiBold' => 'SourceSansPro-SemiBold.ttf',
				'SemiBoldItalic' => 'SourceSansPro-SemiBoldItalic.ttf',
			],
			'700' => [
				'Bold' => 'SourceSansPro-Bold.ttf',
				'BoldItalic' => 'SourceSansPro-BoldItalic.ttf',
			],
			'800' => [
				'Bold' => 'SourceSansPro-Bold.ttf',
				'BoldItalic' => 'SourceSansPro-BoldItalic.ttf',
			],
			'900' => [
				'Black' => 'SourceSansPro-Black.ttf',
				'BlackItalic' => 'SourceSansPro-BlackItalic.ttf',
			],
		],

		'PT Serif' => [
			'100' => [
				'Regular' => 'PT_Serif-Regular.ttf',
				'Italic' => 'PT_Serif-Italic.ttf',
			],
			'200' => [
				'Regular' => 'PT_Serif-Regular.ttf',
				'Italic' => 'PT_Serif-Italic.ttf',
			],
			'300' => [
				'Regular' => 'PT_Serif-Regular.ttf',
				'Italic' => 'PT_Serif-Italic.ttf',
			],
			'400' => [
				'Regular' => 'PT_Serif-Regular.ttf',
				'Italic' => 'PT_Serif-Italic.ttf',
			],
			'500' => [
				'Regular' => 'PT_Serif-Regular.ttf',
				'Italic' => 'PT_Serif-Italic.ttf',
			],
			'600' => [
				'Regular' => 'PT_Serif-Regular.ttf',
				'Italic' => 'PT_Serif-Italic.ttf',
			],
			'700' => [
				'Bold' => 'PT_Serif-Bold.ttf',
				'BoldItalic' => 'PT_Serif-BoldItalic.ttf',
			],
			'800' => [
				'Bold' => 'PT_Serif-Bold.ttf',
				'BoldItalic' => 'PT_Serif-BoldItalic.ttf',
			],
			'900' => [
				'Bold' => 'PT_Serif-Bold.ttf',
				'BoldItalic' => 'PT_Serif-BoldItalic.ttf',
			],
		],
		'PT Sans Narrow' => [
			'100' => [
				'Narrow-Regular' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'200' => [
				'Narrow-Regular' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'300' => [
				'Narrow-Regular' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'400' => [
				'Narrow-Regular' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'500' => [
				'Narrow-Regular' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'600' => [
				'Narrow-Regular' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'700' => [
				'Narrow-Bold' => 'PT_Sans-Narrow-Bold.ttf',
			],
			'800' => [
				'Narrow-Bold' => 'PT_Sans-Narrow-Bold.ttf',
			],
			'900' => [
				'Narrow-Bold' => 'PT_Sans-Narrow-Bold.ttf',
			],

		],
		'PT Sans' => [
			'100' => [
				'Regular' => 'PT_Sans-Regular.ttf',
				'Italic' => 'PT_Sans-Italic.ttf',
			],
			'200' => [
				'Regular' => 'PT_Sans-Regular.ttf',
				'Italic' => 'PT_Sans-Italic.ttf',
			],
			'300' => [
				'Regular' => 'PT_Sans-Regular.ttf',
				'Italic' => 'PT_Sans-Italic.ttf',
			],
			'400' => [
				'Regular' => 'PT_Sans-Regular.ttf',
				'Italic' => 'PT_Sans-Italic.ttf',
			],
			'500' => [
				'Regular' => 'PT_Sans-Regular.ttf',
				'Italic' => 'PT_Sans-Italic.ttf',
			],
			'600' => [
				'Regular' => 'PT_Sans-Regular.ttf',
				'Italic' => 'PT_Sans-Italic.ttf',
			],
			'700' => [
				'Bold' => 'PT_Sans-Bold.ttf',
				'BoldItalic' => 'PT_Sans-BoldItalic.ttf',
			],
			'800' => [
				'Bold' => 'PT_Sans-Bold.ttf',
				'BoldItalic' => 'PT_Sans-BoldItalic.ttf',
			],
			'900' => [
				'Bold' => 'PT_Sans-Bold.ttf',
				'BoldItalic' => 'PT_Sans-BoldItalic.ttf',
			],
		],

		'PT_Mono' => [
			'100' => [
				'Regular' => 'PT_Mono.ttf'
			],
			'200' => [
				'Regular' => 'PT_Mono.ttf'
			],
			'300' => [
				'Regular' => 'PT_Mono.ttf'
			],
			'400' => [
				'Regular' => 'PT_Mono.ttf'
			],
			'500' => [
				'Regular' => 'PT_Mono.ttf'
			],
			'600' => [
				'Regular' => 'PT_Mono.ttf'
			],
			'700' => [
				'Regular' => 'PT_Mono.ttf'
			],
			'800' => [
				'Regular' => 'PT_Mono.ttf'
			],
			'900' => [
				'Regular' => 'PT_Mono.ttf'
			],
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
