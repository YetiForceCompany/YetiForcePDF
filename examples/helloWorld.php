<?php
$loader = require '../vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml('<div><div style="font-family: NotoSans-Bold;font-size:18px;width:100px;height:100px;padding:20px;margin-bottom:20px;">Witaj świecie! Смотришь нежно</div><div style="font-family: PT_Serif-Regular;font-size:14px;width:200px;height:100px;margin-left:10px;margin-top:40px;">hello world2! Смотришь нежно,</div><div style="font-family: PT_Sans-Regular;font-size:14px;height:100px;padding:20px;margin-bottom:20px;">last but not least!</div></div>');
$pdfFile = $document->render();
echo $pdfFile;
file_put_contents('test.pdf', $pdfFile);
