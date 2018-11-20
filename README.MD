# YetiForcePDF
[![SymfonyInsight](https://insight.symfony.com/projects/d944b009-6a76-4957-8833-db95b010e5a5/big.png)](https://insight.symfony.com/projects/d944b009-6a76-4957-8833-db95b010e5a5)
[![Latest Stable Version](https://poser.pugx.org/yetiforce/yetiforcepdf/v/stable)](https://packagist.org/packages/yetiforce/yetiforcepdf)
[![Build Status](https://travis-ci.org/YetiForceCompany/YetiForcePDF.svg?branch=developer)](https://travis-ci.org/YetiForceCompany/YetiForcePDF)
[![Analytics](https://ga-beacon.appspot.com/UA-81376231-2/welcome-page)](https://github.com/igrigorik/ga-beacon)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/YetiForceCompany/YetiForcePDF/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/YetiForceCompany/YetiForcePDF/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b2e8645f5091496089ed203d05a05d61)](https://app.codacy.com/app/mariuszkrzaczkowski/YetiForcePDF?utm_source=github.com&utm_medium=referral&utm_content=YetiForceCompany/YetiForcePDF&utm_campaign=Badge_Grade_Settings)
[![Maintainability](https://api.codeclimate.com/v1/badges/af478ddd07cf7278841a/maintainability)](https://codeclimate.com/github/YetiForceCompany/YetiForcePDF/maintainability)
[![sonarcloud.io status alert](https://sonarcloud.io/api/project_badges/measure?project=YetiForcePDF&metric=alert_status)](https://sonarcloud.io/dashboard?id=YetiForcePDF)
[![sonarcloud.io bugs](https://sonarcloud.io/api/project_badges/measure?project=YetiForcePDF&metric=bugs)](https://sonarcloud.io/dashboard?id=YetiForcePDF)
[![sonarcloud.io sqale](https://sonarcloud.io/api/project_badges/measure?project=YetiForcePDF&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=YetiForcePDF)
[![sonarcloud.io security](https://sonarcloud.io/api/project_badges/measure?project=YetiForcePDF&metric=security_rating)](https://sonarcloud.io/dashboard?id=YetiForcePDF)
[![sonarcloud.io vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=YetiForcePDF&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=YetiForcePDF)

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
- [x] color [hex, rgb, named]
- [x] word-wrap [normal]
- [ ] max-width
- [ ] min-width
- [x] white-space [normal]
- [ ] word-wrap
- [ ] float
- [ ] clear
- [x] table displays
- [ ] background-image
- [x] rgb / rgba color [without alpha inside pdf for now]


### Available fonts

- Noto Sans
- Noto Serif
- Noto Mono
- Source Sans Pro
- Source Serif Pro
- Source Code Pro
- PT Sans
- PT Sans Narrow
- PT Serif
- PT Mono


### TODO (crucial)

- [x] ~~font-weight (font-family without specifying weight and variant)~~
- [x] ~~basic html elements with default css rules (br, table, image, hr etc.)~~
- [x] tables
- [ ] images
- [ ] page headers / footers
- [ ] alpha channel support for colors (rgba) and opacity
- [ ] background watermarks

### TODO (later but important)
- [ ] floats
- [ ] barcode generation
- [ ] display: list-item
- [ ] basic transformations (rotateXY, scaleXY, translateXY)
- [ ] position (absolute, relative)
