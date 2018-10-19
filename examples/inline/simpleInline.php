<?php
$loader = require '../../vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml(file_get_contents('simpleInline.html'));
$pdfFile = $document->render();
//echo $pdfFile;
file_put_contents('simpleInline.pdf', $pdfFile);
