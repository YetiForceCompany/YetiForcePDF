<?php

$loader = require '../vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml(file_get_contents('InvoiceTable.html'));
$time = microtime(true);
$pdfFile = $document->render();
echo microtime(true) - $time;
file_put_contents('InvoiceTable.pdf', $pdfFile);
