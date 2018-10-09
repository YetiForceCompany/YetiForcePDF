<?php
$loader = require '../vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml(file_get_contents('wordWrap.html'));
$pdfFile = $document->render();
echo $pdfFile;
file_put_contents('wordWrap.pdf', $pdfFile);
