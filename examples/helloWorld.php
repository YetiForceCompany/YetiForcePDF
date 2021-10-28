<?php

$loader = require '../vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml(file_get_contents('helloWorld.html'));
$pdfFile = $document->render();
file_put_contents('helloWorld.pdf', $pdfFile);
