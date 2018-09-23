<?php
$loader = require '../vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml('<div><div style="font-family: Lato-Regular;font-size:14px;width:100px;height:100px;padding:20px;margin-bottom:20px;">hello world!</div><div style="font-family: Lato-Regular;font-size:14px;width:200px;height:100px;margin-left:10px;margin-top:40px;">hello world2!</div><div style="font-family: Lato-Regular;font-size:14px;height:100px;padding:20px;margin-bottom:20px;">last but not least!</div></div>');
$pdfFile = $document->render();
echo $pdfFile;
file_put_contents('test.pdf', $pdfFile);
