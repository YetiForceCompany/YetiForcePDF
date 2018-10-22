<?php

$loader = require '../vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml(file_get_contents('SimpleColor.html'));
$pdfFile = $document->render();
file_put_contents('SimpleColor.pdf', $pdfFile);
