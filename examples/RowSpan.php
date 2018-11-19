<?php

$loader = require '../vendor/autoload.php';
$files = ['RowSpan'];
foreach ($files as $file) {
    $document = (new YetiForcePDF\Document())->init();
    $document->loadHtml(file_get_contents($file . '.html'));
    $pdfFile = $document->render();
    file_put_contents($file . '.pdf', $pdfFile);
}
