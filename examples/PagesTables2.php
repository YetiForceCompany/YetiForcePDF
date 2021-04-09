<?php

$loader = require '../vendor/autoload.php';
require('PagesTables2.html.php');
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml($html);
$pdfFile = $document->render();
file_put_contents('PagesTables2.pdf', $pdfFile);

