<?php

$loader = require '../vendor/autoload.php';
$files = ['Barcode'];
foreach ($files as $file) {
	$document = (new YetiForcePDF\Document())->init();
	$document->loadHtml(file_get_contents($file . '.html'));
	$pdfFile = $document->parse()->render();
	file_put_contents($file . '.pdf', $pdfFile);
}
