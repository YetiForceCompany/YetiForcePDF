<?php

$loader = require '../vendor/autoload.php';
$files = ['H123456'];
foreach ($files as $file) {
	$document = (new YetiForcePDF\Document())->init();
	$document->loadHtml(file_get_contents($file . '.html'));
	$time = microtime(true);
	$pdfFile = $document->render();
	echo microtime(true) - $time;
	file_put_contents($file . '.pdf', $pdfFile);
}
