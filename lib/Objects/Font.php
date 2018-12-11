<?php

declare(strict_types=1);
/**
 * Font class.
 *
 * @package   YetiForcePDF\Objects
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Objects;

use YetiForcePDF\Math;

/**
 * Class Font.
 */
class Font extends \YetiForcePDF\Objects\Resource
{
	protected $fontFiles = [
		'Noto Sans Condensed' => [
			'100' => [
				'normal' => 'NotoSans-CondensedThin.ttf',
				'italic' => 'NotoSans-CondensedThinItalic.ttf',
			],
			'200' => [
				'normal' => 'NotoSans-CondensedExtraLight.ttf',
				'italic' => 'NotoSans-CondensedExtraLightItalic.ttf',
			],
			'300' => [
				'normal' => 'NotoSans-CondensedLight.ttf',
				'italic' => 'NotoSans-CondensedLightItalic.ttf',
			],
			'400' => [
				'normal' => 'NotoSans-Condensed.ttf',
				'italic' => 'NotoSans-CondensedItalic.ttf',
			],
			'500' => [
				'normal' => 'NotoSans-CondensedMedium.ttf',
				'italic' => 'NotoSans-CondensedMediumItalic.ttf',
			],
			'600' => [
				'normal' => 'NotoSans-CondensedSemiBold.ttf',
				'italic' => 'NotoSans-CondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'normal' => 'NotoSans-CondensedBold.ttf',
				'italic' => 'NotoSans-CondensedBoldItalic.ttf',
			],
			'800' => [
				'normal' => 'NotoSans-CondensedExtraBold.ttf',
				'italic' => 'NotoSans-CondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'normal' => 'NotoSans-CondensedBlack.ttf',
				'italic' => 'NotoSans-CondensedBlackItalic.ttf',
			],
		],
		'Noto Sans SemiCondensed' => [
			'100' => [
				'normal' => 'NotoSans-SemiCondensedThin.ttf',
				'italic' => 'NotoSans-SemiCondensedThinItalic.ttf',
			],
			'200' => [
				'normal' => 'NotoSans-SemiCondensedExtraLight.ttf',
				'italic' => 'NotoSans-SemiCondensedExtraLightItalic.ttf',
			],
			'300' => [
				'normal' => 'NotoSans-SemiCondensedLight.ttf',
				'italic' => 'NotoSans-SemiCondensedLightItalic.ttf',
			],
			'400' => [
				'normal' => 'NotoSans-SemiCondensed.ttf',
				'italic' => 'NotoSans-SemiCondensedItalic.ttf',
			],
			'500' => [
				'normal' => 'NotoSans-SemiCondensedMedium.ttf',
				'italic' => 'NotoSans-SemiCondensedMediumItalic.ttf',
			],
			'600' => [
				'normal' => 'NotoSans-SemiCondensedSemiBold.ttf',
				'italic' => 'NotoSans-SemiCondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'normal' => 'NotoSans-SemiCondensedBold.ttf',
				'italic' => 'NotoSans-SemiCondensedBoldItalic.ttf',
			],
			'800' => [
				'normal' => 'NotoSans-SemiCondensedExtraBold.ttf',
				'italic' => 'NotoSans-SemiCondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'normal' => 'NotoSans-SemiCondensedBlack.ttf',
				'italic' => 'NotoSans-SemiCondensedBlackItalic.ttf',
			],
		],
		'Noto Sans ExtraCondensed' => [
			'100' => [
				'normal' => 'NotoSans-ExtraCondensedThin.ttf',
				'italic' => 'NotoSans-ExtraCondensedThinItalic.ttf',
			],
			'200' => [
				'normal' => 'NotoSans-ExtraCondensedExtraLight.ttf',
				'italic' => 'NotoSans-ExtraCondensedExtraLightItalic.ttf',
			],
			'300' => [
				'normal' => 'NotoSans-ExtraCondensedLight.ttf',
				'italic' => 'NotoSans-ExtraCondensedLightItalic.ttf',
			],
			'400' => [
				'normal' => 'NotoSans-ExtraCondensed.ttf',
				'italic' => 'NotoSans-ExtraCondensedItalic.ttf',
			],
			'500' => [
				'normal' => 'NotoSans-ExtraCondensedMedium.ttf',
				'italic' => 'NotoSans-ExtraCondensedMediumItalic.ttf',
			],
			'600' => [
				'normal' => 'NotoSans-ExtraCondensedSemiBold.ttf',
				'italic' => 'NotoSans-ExtraCondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'normal' => 'NotoSans-ExtraCondensedBold.ttf',
				'italic' => 'NotoSans-ExtraCondensedBoldItalic.ttf',
			],
			'800' => [
				'normal' => 'NotoSans-ExtraCondensedExtraBold.ttf',
				'italic' => 'NotoSans-ExtraCondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'normal' => 'NotoSans-ExtraCondensedBlack.ttf',
				'italic' => 'NotoSans-ExtraCondensedBlackItalic.ttf',
			],
		],
		'Noto Mono' => [
			'100' => [
				'normal' => 'NotoMono-Regular.ttf',
				'italic' => 'NotoMono-Regular.ttf',
			],
			'200' => [
				'normal' => 'NotoMono-Regular.ttf',
				'italic' => 'NotoMono-Regular.ttf',
			],
			'300' => [
				'normal' => 'NotoMono-Regular.ttf',
				'italic' => 'NotoMono-Regular.ttf',
			],
			'400' => [
				'normal' => 'NotoMono-Regular.ttf',
				'italic' => 'NotoMono-Regular.ttf',
			],
			'500' => [
				'normal' => 'NotoMono-Regular.ttf',
				'italic' => 'NotoMono-Regular.ttf',
			],
			'600' => [
				'normal' => 'NotoMono-Regular.ttf',
				'italic' => 'NotoMono-Regular.ttf',
			],
			'700' => [
				'normal' => 'NotoMono-Regular.ttf',
				'italic' => 'NotoMono-Regular.ttf',
			],
			'800' => [
				'normal' => 'NotoMono-Regular.ttf',
				'italic' => 'NotoMono-Regular.ttf',
			],
			'900' => [
				'normal' => 'NotoMono-Regular.ttf',
				'italic' => 'NotoMono-Regular.ttf',
			],
		],
		'Noto Sans' => [
			'100' => [
				'normal' => 'NotoSans-Thin.ttf',
				'italic' => 'NotoSans-ThinItalic.ttf',
			],
			'200' => [
				'normal' => 'NotoSans-ExtraLight.ttf',
				'italic' => 'NotoSans-ExtraLightItalic.ttf',
			],
			'300' => [
				'normal' => 'NotoSans-Light.ttf',
				'italic' => 'NotoSans-LightItalic.ttf',
			],
			'400' => [
				'normal' => 'NotoSans-Regular.ttf',
				'italic' => 'NotoSans-Italic.ttf',
			],
			'500' => [
				'normal' => 'NotoSans-Medium.ttf',
				'italic' => 'NotoSans-MediumItalic.ttf',
			],
			'600' => [
				'normal' => 'NotoSans-SemiBold.ttf',
				'italic' => 'NotoSans-SemiBoldItalic.ttf',
			],
			'700' => [
				'normal' => 'NotoSans-Bold.ttf',
				'italic' => 'NotoSans-BoldItalic.ttf',
			],
			'800' => [
				'normal' => 'NotoSans-ExtraBold.ttf',
				'italic' => 'NotoSans-ExtraBoldItalic.ttf',
			],
			'900' => [
				'normal' => 'NotoSans-Black.ttf',
				'italic' => 'NotoSans-BlackItalic.ttf',
			],
		],

		'Noto Serif SemiCondensed' => [
			'100' => [
				'normal' => 'NotoSerif-SemiCondensedThin.ttf',
				'italic' => 'NotoSerif-SemiCondensedThinItalic.ttf',
			],
			'200' => [
				'normal' => 'NotoSerif-SemiCondensedExtraLight.ttf',
				'italic' => 'NotoSerif-SemiCondensedExtraLightItalic.ttf',
			],
			'300' => [
				'normal' => 'NotoSerif-SemiCondensedLight.ttf',
				'italic' => 'NotoSerif-SemiCondensedLightItalic.ttf',
			],
			'400' => [
				'normal' => 'NotoSerif-SemiCondensed.ttf',
				'italic' => 'NotoSerif-SemiCondensedItalic.ttf',
			],
			'500' => [
				'normal' => 'NotoSerif-SemiCondensedMedium.ttf',
				'italic' => 'NotoSerif-SemiCondensedMediumItalic.ttf',
			],
			'600' => [
				'normal' => 'NotoSerif-SemiCondensedSemiBold.ttf',
				'italic' => 'NotoSerif-SemiCondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'normal' => 'NotoSerif-SemiCondensedBold.ttf',
				'italic' => 'NotoSerif-SemiCondensedBoldItalic.ttf',
			],
			'800' => [
				'normal' => 'NotoSerif-SemiCondensedExtraBold.ttf',
				'italic' => 'NotoSerif-SemiCondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'normal' => 'NotoSerif-SemiCondensedBlack.ttf',
				'italic' => 'NotoSerif-SemiCondensedBlackItalic.ttf',
			],
		],
		'Noto Serif ExtraCondensed' => [
			'100' => [
				'normal' => 'NotoSerif-ExtraCondensedThin.ttf',
				'italic' => 'NotoSerif-ExtraCondensedThinItalic.ttf',
			],
			'200' => [
				'normal' => 'NotoSerif-ExtraCondensedExtraLight.ttf',
				'italic' => 'NotoSerif-ExtraCondensedExtraLightItalic.ttf',
			],
			'300' => [
				'normal' => 'NotoSerif-ExtraCondensedLight.ttf',
				'italic' => 'NotoSerif-ExtraCondensedLightItalic.ttf',
			],
			'400' => [
				'normal' => 'NotoSerif-ExtraCondensed.ttf',
				'italic' => 'NotoSerif-ExtraCondensedItalic.ttf',
			],
			'500' => [
				'normal' => 'NotoSerif-ExtraCondensedMedium.ttf',
				'italic' => 'NotoSerif-ExtraCondensedMediumItalic.ttf',
			],
			'600' => [
				'normal' => 'NotoSerif-ExtraCondensedSemiBold.ttf',
				'italic' => 'NotoSerif-ExtraCondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'normal' => 'NotoSerif-ExtraCondensedBold.ttf',
				'italic' => 'NotoSerif-ExtraCondensedBoldItalic.ttf',
			],
			'800' => [
				'normal' => 'NotoSerif-ExtraCondensedExtraBold.ttf',
				'italic' => 'NotoSerif-ExtraCondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'normal' => 'NotoSerif-ExtraCondensedBlack.ttf',
				'italic' => 'NotoSerif-ExtraCondensedBlackItalic.ttf',
			],
		],
		'Noto Serif Condensed' => [
			'100' => [
				'normal' => 'NotoSerif-CondensedThin.ttf',
				'italic' => 'NotoSerif-CondensedThinItalic.ttf',
			],
			'200' => [
				'normal' => 'NotoSerif-CondensedExtraLight.ttf',
				'italic' => 'NotoSerif-CondensedExtraLightItalic.ttf',
			],
			'300' => [
				'normal' => 'NotoSerif-CondensedLight.ttf',
				'italic' => 'NotoSerif-CondensedLightItalic.ttf',
			],
			'400' => [
				'normal' => 'NotoSerif-Condensed.ttf',
				'italic' => 'NotoSerif-CondensedItalic.ttf',
			],
			'500' => [
				'normal' => 'NotoSerif-CondensedMedium.ttf',
				'italic' => 'NotoSerif-CondensedMediumItalic.ttf',
			],
			'600' => [
				'normal' => 'NotoSerif-CondensedSemiBold.ttf',
				'italic' => 'NotoSerif-CondensedSemiBoldItalic.ttf',
			],
			'700' => [
				'normal' => 'NotoSerif-CondensedBold.ttf',
				'italic' => 'NotoSerif-CondensedBoldItalic.ttf',
			],
			'800' => [
				'normal' => 'NotoSerif-CondensedExtraBold.ttf',
				'italic' => 'NotoSerif-CondensedExtraBoldItalic.ttf',
			],
			'900' => [
				'normal' => 'NotoSerif-CondensedBlack.ttf',
				'italic' => 'NotoSerif-CondensedBlackItalic.ttf',
			],
		],
		'Noto Serif' => [
			'100' => [
				'normal' => 'NotoSerif-Thin.ttf',
				'italic' => 'NotoSerif-ThinItalic.ttf',
			],
			'200' => [
				'normal' => 'NotoSerif-ExtraLight.ttf',
				'italic' => 'NotoSerif-ExtraLightItalic.ttf',
			],
			'300' => [
				'normal' => 'NotoSerif-Light.ttf',
				'italic' => 'NotoSerif-LightItalic.ttf',
			],
			'400' => [
				'normal' => 'NotoSerif-Regular.ttf',
				'italic' => 'NotoSerif-Italic.ttf',
			],
			'500' => [
				'normal' => 'NotoSerif-Medium.ttf',
				'italic' => 'NotoSerif-MediumItalic.ttf',
			],
			'600' => [
				'normal' => 'NotoSerif-SemiBold.ttf',
				'italic' => 'NotoSerif-SemiBoldItalic.ttf',
			],
			'700' => [
				'normal' => 'NotoSerif-Bold.ttf',
				'italic' => 'NotoSerif-BoldItalic.ttf',
			],
			'800' => [
				'normal' => 'NotoSerif-ExtraBold.ttf',
				'italic' => 'NotoSerif-ExtraBoldItalic.ttf',
			],
			'900' => [
				'normal' => 'NotoSerif-Black.ttf',
				'italic' => 'NotoSerif-BlackItalic.ttf',
			],
		],

		'Source Code Pro' => [
			'100' => [
				'normal' => 'SourceCodePro-ExtraLight.ttf',
				'italic' => 'SourceCodePro-ExtraLight.ttf',
			],
			'200' => [
				'normal' => 'SourceCodePro-ExtraLight.ttf',
				'italic' => 'SourceCodePro-ExtraLight.ttf',
			],
			'300' => [
				'normal' => 'SourceCodePro-Light.ttf',
				'italic' => 'SourceCodePro-Light.ttf',
			],
			'400' => [
				'normal' => 'SourceCodePro-Regular.ttf',
				'italic' => 'SourceCodePro-Regular.ttf',
			],
			'500' => [
				'normal' => 'SourceCodePro-Medium.ttf',
				'italic' => 'SourceCodePro-Medium.ttf',
			],
			'600' => [
				'normal' => 'SourceCodePro-Semibold.ttf',
				'italic' => 'SourceCodePro-Semibold.ttf',
			],
			'700' => [
				'normal' => 'SourceCodePro-Bold.ttf',
				'italic' => 'SourceCodePro-Bold.ttf',
			],
			'800' => [
				'normal' => 'SourceCodePro-Bold.ttf',
				'italic' => 'SourceCodePro-Bold.ttf',
			],
			'900' => [
				'normal' => 'SourceCodePro-Black.ttf',
				'italic' => 'SourceCodePro-Black.ttf',
			],
		],
		'Source Serif Pro' => [
			'100' => [
				'normal' => 'SourceSerifPro-Regular.ttf',
				'italic' => 'SourceSerifPro-Regular.ttf',
			],
			'200' => [
				'normal' => 'SourceSerifPro-Regular.ttf',
				'italic' => 'SourceSerifPro-Regular.ttf',
			],
			'300' => [
				'normal' => 'SourceSerifPro-Regular.ttf',
				'italic' => 'SourceSerifPro-Regular.ttf',
			],
			'400' => [
				'normal' => 'SourceSerifPro-Regular.ttf',
				'italic' => 'SourceSerifPro-Regular.ttf',
			],
			'500' => [
				'normal' => 'SourceSerifPro-Regular.ttf',
				'italic' => 'SourceSerifPro-Regular.ttf',
			],
			'600' => [
				'normal' => 'SourceSerifPro-Semibold.ttf',
				'italic' => 'SourceSerifPro-Semibold.ttf',
			],
			'700' => [
				'normal' => 'SourceSerifPro-Bold.ttf',
				'italic' => 'SourceSerifPro-Bold.ttf',
			],
			'800' => [
				'normal' => 'SourceSerifPro-Bold.ttf',
				'italic' => 'SourceSerifPro-Bold.ttf',
			],
			'900' => [
				'normal' => 'SourceSerifPro-Bold.ttf',
				'italic' => 'SourceSerifPro-Bold.ttf',
			],
		],
		'Source Sans Pro' => [
			'100' => [
				'normal' => 'SourceSansPro-ExtraLight.ttf',
				'italic' => 'SourceSansPro-ExtraLightItalic.ttf',
			],
			'200' => [
				'normal' => 'SourceSansPro-ExtraLight.ttf',
				'italic' => 'SourceSansPro-ExtraLightItalic.ttf',
			],
			'300' => [
				'normal' => 'SourceSansPro-Light.ttf',
				'italic' => 'SourceSansPro-LightItalic.ttf',
			],
			'400' => [
				'normal' => 'SourceSansPro-Regular.ttf',
				'italic' => 'SourceSansPro-Italic.ttf',
			],
			'500' => [
				'normal' => 'SourceSansPro-SemiBold.ttf',
				'italic' => 'SourceSansPro-SemiBoldItalic.ttf',
			],
			'600' => [
				'normal' => 'SourceSansPro-SemiBold.ttf',
				'italic' => 'SourceSansPro-SemiBoldItalic.ttf',
			],
			'700' => [
				'normal' => 'SourceSansPro-Bold.ttf',
				'italic' => 'SourceSansPro-BoldItalic.ttf',
			],
			'800' => [
				'normal' => 'SourceSansPro-Bold.ttf',
				'italic' => 'SourceSansPro-BoldItalic.ttf',
			],
			'900' => [
				'normal' => 'SourceSansPro-Black.ttf',
				'italic' => 'SourceSansPro-BlackItalic.ttf',
			],
		],

		'PT Serif' => [
			'100' => [
				'normal' => 'PT_Serif-Regular.ttf',
				'italic' => 'PT_Serif-Italic.ttf',
			],
			'200' => [
				'normal' => 'PT_Serif-Regular.ttf',
				'italic' => 'PT_Serif-Italic.ttf',
			],
			'300' => [
				'normal' => 'PT_Serif-Regular.ttf',
				'italic' => 'PT_Serif-Italic.ttf',
			],
			'400' => [
				'normal' => 'PT_Serif-Regular.ttf',
				'italic' => 'PT_Serif-Italic.ttf',
			],
			'500' => [
				'normal' => 'PT_Serif-Regular.ttf',
				'italic' => 'PT_Serif-Italic.ttf',
			],
			'600' => [
				'normal' => 'PT_Serif-Regular.ttf',
				'italic' => 'PT_Serif-Italic.ttf',
			],
			'700' => [
				'normal' => 'PT_Serif-Bold.ttf',
				'italic' => 'PT_Serif-BoldItalic.ttf',
			],
			'800' => [
				'normal' => 'PT_Serif-Bold.ttf',
				'italic' => 'PT_Serif-BoldItalic.ttf',
			],
			'900' => [
				'normal' => 'PT_Serif-Bold.ttf',
				'italic' => 'PT_Serif-BoldItalic.ttf',
			],
		],
		'PT Sans Narrow' => [
			'100' => [
				'normal' => 'PT_Sans-Narrow-Regular.ttf',
				'italic' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'200' => [
				'normal' => 'PT_Sans-Narrow-Regular.ttf',
				'italic' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'300' => [
				'normal' => 'PT_Sans-Narrow-Regular.ttf',
				'italic' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'400' => [
				'normal' => 'PT_Sans-Narrow-Regular.ttf',
				'italic' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'500' => [
				'normal' => 'PT_Sans-Narrow-Regular.ttf',
				'italic' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'600' => [
				'normal' => 'PT_Sans-Narrow-Regular.ttf',
				'italic' => 'PT_Sans-Narrow-Regular.ttf',
			],
			'700' => [
				'normal' => 'PT_Sans-Narrow-Bold.ttf',
				'italic' => 'PT_Sans-Narrow-Bold.ttf',
			],
			'800' => [
				'normal' => 'PT_Sans-Narrow-Bold.ttf',
				'italic' => 'PT_Sans-Narrow-Bold.ttf',
			],
			'900' => [
				'normal' => 'PT_Sans-Narrow-Bold.ttf',
				'italic' => 'PT_Sans-Narrow-Bold.ttf',
			],
		],
		'PT Sans' => [
			'100' => [
				'normal' => 'PT_Sans-Regular.ttf',
				'italic' => 'PT_Sans-Italic.ttf',
			],
			'200' => [
				'normal' => 'PT_Sans-Regular.ttf',
				'italic' => 'PT_Sans-Italic.ttf',
			],
			'300' => [
				'normal' => 'PT_Sans-Regular.ttf',
				'italic' => 'PT_Sans-Italic.ttf',
			],
			'400' => [
				'normal' => 'PT_Sans-Regular.ttf',
				'italic' => 'PT_Sans-Italic.ttf',
			],
			'500' => [
				'normal' => 'PT_Sans-Regular.ttf',
				'italic' => 'PT_Sans-Italic.ttf',
			],
			'600' => [
				'normal' => 'PT_Sans-Regular.ttf',
				'italic' => 'PT_Sans-Italic.ttf',
			],
			'700' => [
				'normal' => 'PT_Sans-Bold.ttf',
				'italic' => 'PT_Sans-BoldItalic.ttf',
			],
			'800' => [
				'normal' => 'PT_Sans-Bold.ttf',
				'italic' => 'PT_Sans-BoldItalic.ttf',
			],
			'900' => [
				'normal' => 'PT_Sans-Bold.ttf',
				'italic' => 'PT_Sans-BoldItalic.ttf',
			],
		],

		'PT Mono' => [
			'100' => [
				'normal' => 'PT_Mono.ttf',
				'italic' => 'PT_Mono.ttf'
			],
			'200' => [
				'normal' => 'PT_Mono.ttf',
				'italic' => 'PT_Mono.ttf'
			],
			'300' => [
				'normal' => 'PT_Mono.ttf',
				'italic' => 'PT_Mono.ttf'
			],
			'400' => [
				'normal' => 'PT_Mono.ttf',
				'italic' => 'PT_Mono.ttf'
			],
			'500' => [
				'normal' => 'PT_Mono.ttf',
				'italic' => 'PT_Mono.ttf'
			],
			'600' => [
				'normal' => 'PT_Mono.ttf',
				'italic' => 'PT_Mono.ttf'
			],
			'700' => [
				'normal' => 'PT_Mono.ttf',
				'italic' => 'PT_Mono.ttf'
			],
			'800' => [
				'normal' => 'PT_Mono.ttf',
				'italic' => 'PT_Mono.ttf'
			],
			'900' => [
				'normal' => 'PT_Mono.ttf',
				'italic' => 'PT_Mono.ttf'
			],
		],
	];
	/**
	 * @var string
	 */
	protected $fontDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fonts' . DIRECTORY_SEPARATOR;
	/**
	 * Which type of dictionary (Page, Catalog, Font etc...).
	 *
	 * @var string
	 */
	protected $resourceType = 'Font';
	/**
	 * Object name.
	 *
	 * @var string
	 */
	protected $resourceName = 'Font';
	/**
	 * Base font type aka font family.
	 *
	 * @var string
	 */
	protected $family = 'Noto Serif';
	/**
	 * Font weight.
	 *
	 * @var string
	 */
	protected $weight = 'normal';
	/**
	 * Font style.
	 *
	 * @var string
	 */
	protected $style = 'normal';
	/**
	 * Font number.
	 *
	 * @var string
	 */
	protected $fontNumber = 'F1';
	/**
	 * Font size.
	 *
	 * @var string
	 */
	protected $size = '12px';
	/**
	 * Font height.
	 *
	 * @var string
	 */
	protected $height = '0';
	/**
	 * Text height with ascender and descender.
	 *
	 * @var string|null
	 */
	protected $textHeight;
	/**
	 * Font data.
	 *
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
	 * Info needed to write in pdf.
	 *
	 * @var array
	 */
	protected $outputInfo = [];
	/**
	 * @var string
	 */
	protected $unitsPerEm = '1000';
	/**
	 * Character widths.
	 *
	 * @var int[]
	 */
	protected $widths = [];
	/**
	 * Cid to Gid characters map.
	 *
	 * @var \YetiForcePDF\Objects\Basic\StreamObject
	 */
	protected $cidToGid;
	/**
	 * Cid system info.
	 *
	 * @var \YetiForcePDF\Objects\Basic\DictionaryObject
	 */
	protected $cidSystemInfo;
	/**
	 * Character map (unicode).
	 *
	 * @var array
	 */
	protected $charMap = [];
	/**
	 * Unicode char map stream.
	 *
	 * @var \YetiForcePDF\Objects\Basic\StreamObject
	 */
	protected $toUnicode;
	/**
	 * Main font that is used - first font - this file is just descendant font.
	 *
	 * @var \YetiForcePDF\Objects\Basic\DictionaryObject
	 */
	protected $fontType0;
	/**
	 * From baseline to top of the font.
	 *
	 * @var string
	 */
	protected $ascender = '0';
	/**
	 * From baseline to bottom (with jyg chars that are bellow baseline).
	 *
	 * @var string
	 */
	protected $descender = '0';
	/**
	 * @var string
	 */
	protected $fontPostscriptName;

	/**
	 * Initialisation.
	 *
	 * @return $this
	 */
	public function init()
	{
		$alreadyExists = $this->document->getFontInstance($this->family, $this->weight, $this->style);
		if ($alreadyExists === null) {
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
	 * Set font number.
	 *
	 * @param string $number
	 *
	 * @return \YetiForcePDF\Objects\Font
	 */
	public function setNumber(string $number): \YetiForcePDF\Objects\Font
	{
		$this->fontNumber = $number;
		return $this;
	}

	/**
	 * Set font name.
	 *
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setFamily(string $name)
	{
		$this->family = $name;
		return $this;
	}

	/**
	 * Get font name.
	 *
	 * @return string
	 */
	public function getFamily(): string
	{
		return $this->family;
	}

	/**
	 * Set font weight.
	 *
	 * @param string $weight
	 *
	 * @return $this
	 */
	public function setWeight(string $weight)
	{
		$this->weight = $weight;
		return $this;
	}

	/**
	 * Get font name.
	 *
	 * @return string
	 */
	public function getWeight(): string
	{
		return $this->weight;
	}

	/**
	 * Set font style.
	 *
	 * @param string $style
	 *
	 * @return $this
	 */
	public function setStyle(string $style)
	{
		$this->style = $style;
		return $this;
	}

	/**
	 * Get font style.
	 *
	 * @return string
	 */
	public function getStyle(): string
	{
		return $this->style;
	}

	/**
	 * Get full font name.
	 *
	 * @return string
	 */
	public function getFullName()
	{
		return $this->fontPostscriptName;
	}

	/**
	 * Get output info.
	 *
	 * @return array
	 */
	public function getOutputInfo()
	{
		return $this->outputInfo;
	}

	/**
	 * Get font data.
	 *
	 * @return \FontLib\Font
	 */
	public function getData()
	{
		return $this->fontData;
	}

	/**
	 * Set Font size.
	 *
	 * @param string $size
	 *
	 * @return $this
	 */
	public function setSize(string $size)
	{
		$this->size = $size;
		$this->textHeight = Math::div(Math::mul($this->size, $this->height), $this->unitsPerEm);
		return $this;
	}

	/**
	 * Get font size.
	 *
	 * @return string
	 */
	public function getSize(): string
	{
		return $this->size;
	}

	/**
	 * Convert character to int
	 * @param $string
	 * @return int
	 */
	public function mbOrd($string)
	{
		if (extension_loaded('mbstring') === true) {
			mb_language('Neutral');
			mb_internal_encoding('UTF-8');
			mb_detect_order(array('UTF-8', 'ISO-8859-15', 'ISO-8859-1', 'ASCII'));
			$result = unpack('N', mb_convert_encoding($string, 'UCS-4BE', 'UTF-8'));
			if (is_array($result) === true) {
				return $result[1];
			}
		}
		return ord($string);
	}

	/**
	 * Get text width.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function getTextWidth(string $text): string
	{
		$width = '0';
		for ($i = 0, $len = mb_strlen($text); $i < $len; $i++) {
			$char = mb_substr($text, $i, 1);
			if (isset($this->widths[$this->mbOrd($char)])) {
				$width = Math::add($width, (string)$this->widths[$this->mbOrd($char)]);
			}
		}
		return Math::div(Math::mul($this->size, $width), '1000');
	}

	/**
	 * Get text height.
	 *
	 * @param string|null $text
	 *
	 * @return string
	 */
	public function getTextHeight(string $text = null): string
	{
		if ($this->textHeight === null) {
			$this->textHeight = Math::div(Math::mul($this->size, $this->height), $this->unitsPerEm);
		}
		return $this->textHeight;
	}

	/**
	 * Get ascender (from baseline to top of the bounding box).
	 *
	 * @return string
	 */
	public function getAscender(): string
	{
		return Math::div(Math::mul($this->size, $this->ascender), $this->unitsPerEm);
	}

	/**
	 * Get descender (from baseline to bottom of the bounding box).
	 *
	 * @return string
	 */
	public function getDescender(): string
	{
		return Math::div(Math::mul($this->size, $this->descender), $this->unitsPerEm);
	}

	/**
	 * Get font number.
	 *
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
	 * Get data stream.
	 *
	 * @return \YetiForcePDF\Objects\Basic\StreamObject
	 */
	public function getDataStream()
	{
		return $this->dataStream;
	}

	public function normalizeUnit(string $value, string $base = '1000')
	{
		return bcmul($value, Math::div($base, $this->unitsPerEm), 0);
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
	 * Get Type0 font - main one.
	 *
	 * @return Font
	 */
	public function getType0Font()
	{
		return $this->fontType0;
	}

	/**
	 * Get font file name without extension.
	 *
	 * @return string
	 */
	public function getFontFileName()
	{
		return $this->fontDir . $this->fontFiles[$this->family][$this->weight][$this->style];
	}

	/**
	 * Load font.
	 *
	 * @throws \FontLib\Exception\FontNotFoundException
	 *
	 * @return \FontLib\TrueType\File|null
	 */
	protected function loadFontData()
	{
		$fileName = $this->getFontFileName();
		$fileContent = file_get_contents($fileName);
		$font = \FontLib\Font::load($fileName);
		$font->parse();
		$head = $font->getData('head');
		$hhea = $font->getData('hhea');
		$hmtx = $font->getData('hmtx');
		$post = $font->getData('post');
		$os2 = $font->getData('OS/2');
		if (isset($head['unitsPerEm'])) {
			$this->unitsPerEm = (string)$head['unitsPerEm'];
		}
		$this->outputInfo['descriptor'] = [];
		$this->outputInfo['descriptor']['FontBBox'] = '[' . implode(' ', [
				$this->normalizeUnit((string)$head['xMin']),
				$this->normalizeUnit((string)$head['yMin']),
				$this->normalizeUnit((string)$head['xMax']),
				$this->normalizeUnit((string)$head['yMax']),
			]) . ']';
		$this->outputInfo['descriptor']['Ascent'] = (string)$hhea['ascent'];
		$this->outputInfo['descriptor']['Descent'] = (string)$hhea['descent'];
		$this->ascender = (string)$this->outputInfo['descriptor']['Ascent'];
		$this->descender = (string)$this->outputInfo['descriptor']['Descent'];
		$this->outputInfo['descriptor']['MissingWidth'] = '500';
		$this->outputInfo['descriptor']['StemV'] = '80';
		if (isset($post['usWeightClass']) && $post['usWeightClass'] > 400) {
			$this->outputInfo['descriptor']['StemV'] = '120';
		}
		$this->outputInfo['descriptor']['ItalicAngle'] = (string)$post['italicAngle'];
		$flags = 0;
		if ($this->outputInfo['descriptor']['ItalicAngle'] !== '0') {
			$flags += 2 ** 6;
		}
		if ($post['isFixedPitch'] === true) {
			$flags += 1;
		}
		$flags += 2 ** 5;
		$this->outputInfo['descriptor']['Flags'] = (string)$flags;
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
			$width = $this->normalizeUnit(isset($hmtx[$glyph]) ? (string)$hmtx[$glyph][0] : (string)$hmtx[0][0]);
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
		$this->height = Math::sub((string)$hhea['ascent'], (string)$hhea['descent']);
		if (isset($os2['typoLineGap'])) {
			$this->height = Math::add($this->height, (string)$os2['typoLineGap']);
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
		return implode("\n", [$this->getRawId() . ' obj',
			'<<',
			'  /Type /Font',
			'  /Subtype /CIDFontType2',
			'  /BaseFont /' . $this->getFullName(),
			'  /FontDescriptor ' . $this->fontDescriptor->getReference(),
			'  /DW 500',
			'  /W [' . implode(' ', $this->outputInfo['font']['Widths']) . ' ]',
			'  /CIDSystemInfo ' . $this->cidSystemInfo->getReference(),
			'  /CIDToGIDMap ' . $this->cidToGid->getReference(),
			'>>',
			'endobj']);
	}
}
