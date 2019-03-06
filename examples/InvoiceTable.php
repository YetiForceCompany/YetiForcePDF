<?php

$loader = require '../vendor/autoload.php';

$document = (new YetiForcePDF\Document())->init();
$document->loadHtml(file_get_contents('InvoiceTable.html'));
$pdfFile = $document->render();
file_put_contents('InvoiceTable.pdf', $pdfFile);
