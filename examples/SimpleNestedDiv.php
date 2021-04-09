<?php

$loader = require '../vendor/autoload.php';
$files = ['SimpleNestedDiv'];
foreach ($files as $file) {
    $document = (new YetiForcePDF\Document())->init();
    $document->loadHtml(file_get_contents($file . '.html'));
    $pdfFile = $document->render();
    file_put_contents($file . '.pdf', $pdfFile);
}
