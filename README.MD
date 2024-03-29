# YetiForcePDF
[![Latest Stable Version](https://poser.pugx.org/yetiforce/yetiforcepdf/v/stable)](https://packagist.org/packages/yetiforce/yetiforcepdf)
[![Build Status](https://travis-ci.org/YetiForceCompany/YetiForcePDF.svg?branch=developer)](https://travis-ci.org/YetiForceCompany/YetiForcePDF)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/YetiForceCompany/YetiForcePDF/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/YetiForceCompany/YetiForcePDF/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/af478ddd07cf7278841a/maintainability)](https://codeclimate.com/github/YetiForceCompany/YetiForcePDF/maintainability)

## PDF generation library for PHP
The best library in the world to generate PDF from HTML

## Issues & bugs
Report errors related to PDF in https://github.com/YetiForceCompany/YetiForceCRM/issues

## Basic usage (for more take a look at examples folder)

```php
<?php
$loader = require '../vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml('<div style="text-align:center;">your html goes here</div>');
$pdfFile = $document->render();
file_put_contents('YourPDF.pdf', $pdfFile);
```

### Supported css rules

- [x] font-family
- [x] font-size
- [x] font-weight
- [x] font-style
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
- [x] color [hex, rgb, rgba, named]
- [x] word-wrap [normal]
- [ ] max-width
- [ ] min-width
- [x] white-space [normal]
- [ ] word-wrap
- [ ] float
- [ ] clear
- [x] table displays
- [x] background-image
- [x] rgb / rgba color


### Available fonts by default

- DejaVu Sans

### Custom fonts

Upload your ttf font files to server and add following code at the beginning of your html.

Each font weight and style could be defined if your font supports it.

```php
<div data-font data-family="Your Font Name" data-weight="400" data-style="normal" data-file="/absolute/path/to/font/file/YourFontName-Regular.ttf"></div>
<div data-font data-family="Your Font Name" data-weight="400" data-style="italic" data-file="/absolute/path/to/font/file/YourFontName-Italic.ttf"></div>
<div data-font data-family="Your Font Name" data-weight="800" data-style="normal" data-file="/absolute/path/to/font/file/YourFontName-Bold.ttf"></div>
<div data-font data-family="Your Font Name" data-weight="800" data-style="italic" data-file="/absolute/path/to/font/file/YourFontName-BoldItalic.ttf"></div>
```

You can also add fonts inside php like:

```php
\YetiForcePDF\Document::addFonts([
	[
		'family' => 'Pacifico',
		'weight' => '400',
		'style' => 'normal',
		'file' => '/absolute/path/to/Pacifico/Pacifico-Regular.ttf'
	],
	[
		'family' => 'Lobster Two',
		'weight' => '400',
		'style' => 'normal',
		'file' => '/absolute/path/to/Lobster_Two/LobsterTwo-Regular.ttf'
	],
	[
		'family' => 'Lobster Two',
		'weight' => 'bold',
		'style' => 'normal',
		'file' => '/absolute/path/to/Lobster_Two/LobsterTwo-Bold.ttf'
	],
]);
```

### Headers, footers and watermarks

You can define header, footer and watermark to be displayed inside pdf file

```php
<div data-header>
    <table style="border:1px solid greenyellow;font-size:12px;margin:10px;background-color:yellow;width:100%">
        <tr>
            <td style="width:50%;border:1px solid red;">This is a page header</td>
            <td style="text-align:right;border:1px solid red;">which could be table</td>
        </tr>
    </table>
</div>
<div data-footer>
    <div style="border:1px solid greenyellow;font-size:14px;margin:10px;">
        <div style="text-align:center">Footer</div>
    </div>
</div>
<div data-watermark style="text-align:center;">
    <div style="display:inline-block;font-size:20px;opacity:0.25;">
        <img src="Image.jpg">
    </div>
</div>
```

### Page format, orientation and margins

Page format, orientation and margins could be defined from YetiForcePDF instance or inside html code.

If you have multiple page formats and orientations inside one pdf document you can insert multiple `data-page-group`s to your html.

```php
<div data-page-group
     data-format="A4"
     data-orientation="P"
     data-margin-left="30"
     data-margin-right="30"
     data-margin-top="40"
     data-margin-bottom="40"
     data-header-top="10"
     data-footer-bottom="10"
></div>
```

### Page break

When you want to add new page use `data-page-group` or set style on div element `page-break-after:always`.

```php
<div style="page-break-after:always;"></div>
```

### Page numbering

When you want to place page number (in header or footer for example) you can do it by placing `{p}` or `{a}` text inside your html - it will be replaced by `{p}` page number or `{a}` all pages count.

```php
<div data-footer>
    <div style="border:1px solid greenyellow;font-size:14px;margin:10px;">
        <div style="text-align:center">Page {p} / {a}</div>
    </div>
</div>
```

### Barcodes

```php
<div data-barcode="EAN13" data-size="1" data-height="16" data-code="9780691147727"></div>
```

### TODO (later but important)
- [ ] floats
- [ ] display: list-item
- [ ] basic transformations (rotateXY, scaleXY, translateXY)
- [ ] position (absolute, relative)

## License

Distributed under the MIT license. See LICENSE for details.

## 👥 Contributors

This project exists thanks to all the people who contribute. 

<a href="https://github.com/YetiForceCompany/YetiForcePDF/graphs/contributors">
<img src="https://contrib.rocks/image?repo=YetiForceCompany/YetiForcePDF" />
</a>

