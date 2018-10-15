# YetiForcePDF
[![SymfonyInsight](https://insight.symfony.com/projects/2988ef66-83db-4ed9-9ba8-6fb4474566bd/big.png)](https://insight.symfony.com/projects/2988ef66-83db-4ed9-9ba8-6fb4474566bd)

## PDF generation library for PHP (work in progress)

## Basic usage (for more take a look at examples folder)

```php
<?php
$loader = require '../vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml('<div style="text-align:center;">your html goes here</div>');
$pdfFile = $document->render();
// echo $pdfFile;
file_put_contents('YourPDFD.pdf', $pdfFile);
```

### Supported css rules

- [x] font-family
- [x] font-size
- [x] margin [px, em, cm, mm, pt, in]
- [x] margin-left  [px, em, cm, mm, pt, in]
- [x] margin-top  [px, em, cm, mm, pt, in]
- [x] margin-right  [px, em, cm, mm, pt, in]
- [x] margin-bottom  [px, em, cm, mm, pt, in]
- [x] padding [px, em, cm, mm, pt, in]
- [x] padding-left [px, em, cm, mm, pt, in]
- [x] padding-top [px, em, cm, mm, pt, in]
- [x] padding-right [px, em, cm, mm, pt, in]
- [x] padding-bottom [px, em, cm, mm, pt, in]
- [x] border [px, em, cm, mm, pt, in]
- [x] border-left
- [x] border-top
- [x] border-right
- [x] border-bottom
- [x] border-left-width  [px, em, cm, mm, pt, in]
- [x] border-top-width [px, em, cm, mm, pt, in]
- [x] border-right-width [px, em, cm, mm, pt, in]
- [x] border-bottom-width [px, em, cm, mm, pt, in]
- [x] border-color [hex, rgb]
- [x] border-left-color [hex, rgb]
- [x] border-top-color [hex, rgb]
- [x] border-right-color [hex, rgb]
- [x] border-bottom-color [hex, rgb]
- [ ] border-style
- [ ] border-left-style
- [ ] border-top-style
- [ ] border-right-style
- [ ] border-bottom-style
- [ ] box-sizing
- [x] display [block, inline, inline-block, none]
- [x] width [%, px, em, cm, mm, pt, in]
- [x] height [%, px, em, cm, mm, pt, in]
- [ ] overflow
- [x] vertical-align [baseline, top, bottom, middle]
- [x] line-height [px, em, cm, mm, pt, in]
- [x] background-color [hex, rgb]
- [x] color [hex, rgb]
- [x] word-wrap
- [ ] max-width
- [ ] min-width
- [ ] word-wrap
- [ ] float
- [ ] clear
- [ ] table displays
- [ ] background-image
- [ ] rgba color


### Available fonts

- NotoSerif-SemiBoldItalic
- NotoSans-ExtraCondensedExtraBold
- NotoSans-CondensedExtraBold
- NotoSans-SemiCondensedMediumItalic
- NotoSans-SemiBoldItalic
- NotoMono-Regular
- NotoSans-ThinItalic
- NotoSans-Thin
- NotoSans-SemiCondensedThinItalic
- NotoSans-SemiCondensedThin
- NotoSans-SemiCondensedSemiBoldItalic
- NotoSans-SemiCondensedSemiBold
- NotoSans-SemiCondensedMedium
- NotoSans-SemiCondensedLightItalic
- NotoSans-SemiCondensedLight
- NotoSans-SemiCondensedItalic
- NotoSans-SemiCondensedExtraLightItalic
- NotoSans-SemiCondensedExtraLight
- NotoSans-SemiCondensedExtraBoldItalic
- NotoSans-SemiCondensedExtraBold
- NotoSans-SemiCondensedBoldItalic
- NotoSans-SemiCondensedBold
- NotoSans-SemiCondensedBlackItalic
- NotoSans-SemiCondensedBlack
- NotoSans-SemiCondensed
- NotoSans-SemiBold
- NotoSans-Regular
- NotoSans-MediumItalic
- NotoSans-Medium
- NotoSans-LightItalic
- NotoSans-Light
- NotoSans-Italic
- NotoSans-ExtraLightItalic
- NotoSans-ExtraLight
- NotoSans-ExtraCondensedThinItalic
- NotoSans-ExtraCondensedThin
- NotoSans-ExtraCondensedSemiBoldItalic
- NotoSans-ExtraCondensedSemiBold
- NotoSans-ExtraCondensedMediumItalic
- NotoSans-ExtraCondensedMedium
- NotoSans-ExtraCondensedLightItalic
- NotoSans-ExtraCondensedLight
- NotoSans-ExtraCondensedItalic
- NotoSans-ExtraCondensedExtraLightItalic
- NotoSans-ExtraCondensedExtraLight
- NotoSans-ExtraCondensedExtraBoldItalic
- NotoSans-ExtraCondensedBoldItalic
- NotoSans-ExtraCondensedBold
- NotoSans-ExtraCondensedBlackItalic
- NotoSans-ExtraCondensedBlack
- NotoSans-ExtraCondensed
- NotoSans-ExtraBoldItalic
- NotoSans-ExtraBold
- NotoSans-CondensedThinItalic
- NotoSans-CondensedThin
- NotoSans-CondensedSemiBoldItalic
- NotoSans-CondensedSemiBold
- NotoSans-CondensedMediumItalic
- NotoSans-CondensedMedium
- NotoSans-CondensedLightItalic
- NotoSans-CondensedLight
- NotoSans-CondensedItalic
- NotoSans-CondensedExtraLightItalic
- NotoSans-CondensedExtraLight
- NotoSans-CondensedExtraBoldItalic
- NotoSans-CondensedBoldItalic
- NotoSans-CondensedBold
- NotoSans-CondensedBlackItalic
- NotoSans-CondensedBlack
- NotoSans-Condensed
- NotoSans-BoldItalic
- NotoSans-Bold
- NotoSans-BlackItalic
- NotoSans-Black
- NotoSerif-ThinItalic
- NotoSerif-Thin
- NotoSerif-SemiCondensedThinItalic
- NotoSerif-SemiCondensedThin
- NotoSerif-SemiCondensedSemiBoldItalic
- NotoSerif-SemiCondensedSemiBold
- NotoSerif-SemiCondensedMediumItalic
- NotoSerif-SemiCondensedMedium
- NotoSerif-SemiCondensedLightItalic
- NotoSerif-SemiCondensedLight
- NotoSerif-SemiCondensedItalic
- NotoSerif-SemiCondensedExtraLightItalic
- NotoSerif-SemiCondensedExtraLight
- NotoSerif-SemiCondensedExtraBoldItalic
- NotoSerif-SemiCondensedExtraBold
- NotoSerif-SemiCondensedBoldItalic
- NotoSerif-SemiCondensedBold
- NotoSerif-SemiCondensedBlackItalic
- NotoSerif-SemiCondensedBlack
- NotoSerif-SemiCondensed
- NotoSerif-SemiBold
- NotoSerif-Regular
- NotoSerif-MediumItalic
- NotoSerif-Medium
- NotoSerif-LightItalic
- NotoSerif-Light
- NotoSerif-Italic
- NotoSerif-ExtraLightItalic
- NotoSerif-ExtraLight
- NotoSerif-ExtraCondensedThinItalic
- NotoSerif-ExtraCondensedThin
- NotoSerif-ExtraCondensedSemiBoldItalic
- NotoSerif-ExtraCondensedSemiBold
- NotoSerif-ExtraCondensedMediumItalic
- NotoSerif-ExtraCondensedMedium
- NotoSerif-ExtraCondensedLightItalic
- NotoSerif-ExtraCondensedLight
- NotoSerif-ExtraCondensedItalic
- NotoSerif-ExtraCondensedExtraLightItalic
- NotoSerif-ExtraCondensedExtraLight
- NotoSerif-ExtraCondensedExtraBoldItalic
- NotoSerif-ExtraCondensedExtraBold
- NotoSerif-ExtraCondensedBoldItalic
- NotoSerif-ExtraCondensedBold
- NotoSerif-ExtraCondensedBlackItalic
- NotoSerif-ExtraCondensedBlack
- NotoSerif-ExtraCondensed
- NotoSerif-ExtraBoldItalic
- NotoSerif-ExtraBold
- NotoSerif-CondensedThinItalic
- NotoSerif-CondensedThin
- NotoSerif-CondensedSemiBoldItalic
- NotoSerif-CondensedSemiBold
- NotoSerif-CondensedMediumItalic
- NotoSerif-CondensedMedium
- NotoSerif-CondensedLightItalic
- NotoSerif-CondensedLight
- NotoSerif-CondensedItalic
- NotoSerif-CondensedExtraLightItalic
- NotoSerif-CondensedExtraLight
- NotoSerif-CondensedExtraBoldItalic
- NotoSerif-CondensedExtraBold
- NotoSerif-CondensedBoldItalic
- NotoSerif-CondensedBold
- NotoSerif-CondensedBlackItalic
- NotoSerif-CondensedBlack
- NotoSerif-Condensed
- NotoSerif-BoldItalic
- NotoSerif-Bold
- NotoSerif-BlackItalic
- NotoSerif-Black
- SourceCodePro-Bold
- SourceSerifPro-Bold
- SourceSerifPro-Semibold
- SourceSerifPro-Regular
- SourceSansPro-BlackItalic
- SourceSansPro-Black
- SourceSansPro-BoldItalic
- SourceSansPro-Bold
- SourceSansPro-SemiBoldItalic
- SourceSansPro-SemiBold
- SourceSansPro-Italic
- SourceSansPro-Regular
- SourceSansPro-LightItalic
- SourceSansPro-Light
- SourceSansPro-ExtraLightItalic
- SourceSansPro-ExtraLight
- SourceCodePro-Black
- SourceCodePro-Semibold
- SourceCodePro-Medium
- SourceCodePro-Regular
- SourceCodePro-Light
- SourceCodePro-ExtraLight
- PT_Serif-BoldItalic
- PT_Serif-Bold
- PT_Serif-Italic
- PT_Serif-Regular
- PT_Sans-Narrow
- PT_Sans-Narrow
- PT_Sans-BoldItalic
- PT_Sans-Bold
- PT_Sans-Italic
- PT_Sans-Regular
- PT_Mono


### TODO (crucial)

- [ ] tables
- [ ] images
- [ ] page headers / footers
- [ ] background watermarks
- [ ] font-weight (font-family without specifying weight and variant)
- [ ] alpha channel support for colors (rgba) and opacity
- [ ] floats
- [ ] basic html elements with default css rules (br, table, image, hr etc.)
- [ ] barcode generation
- [ ] display: list-item
- [ ] basic transformations (rotateXY, scaleXY, translateXY)
- [ ] position (absolute, relative)
