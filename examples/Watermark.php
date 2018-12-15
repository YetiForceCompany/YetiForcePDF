<?php

$loader = require '../vendor/autoload.php';
$files = ['Watermark'];
foreach ($files as $file) {
	$document = (new YetiForcePDF\Document())->init();
	$document->loadHtml(file_get_contents($file . '.html'));
	$meta = $document->getMeta();
	$meta->setSubject('Test ąęść pdfa');
	$meta->setTitle('Tytuł pdfa');
	$meta->setKeywords(['Słowa kluczowe', 'YetiForcePDF', 'YetiForceCRM']);
	$pdfFile = $document->parse()->render();
	file_put_contents($file . '.pdf', $pdfFile);
}
