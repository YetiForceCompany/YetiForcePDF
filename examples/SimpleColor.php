<?php
echo "test";
$loader = require '../vendor/autoload.php';
echo 'before';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml(file_get_contents('SimpleColor.html'));
$pdfFile = $document->render();
echo 'SimpleColor';
file_put_contents('SimpleColor.pdf', $pdfFile);
