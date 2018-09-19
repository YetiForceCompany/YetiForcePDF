# YetiForcePDF
## PDF generation library for PHP (work in progress)

### example

```PHP
<?php
$loader = require __DIR__ . '/vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml('<div style="font-family: Times-Roman;font-size:50px;margin-left:100px;margin-bottom:200px;">hello world!</div><div style="font-family: Times-Roman;font-size:50px;margin-left:50px;margin-bottom:100px;">hello world2!</div>'');
$pdfFile = $document->render();
file_put_contents('test.pdf', $pdfFile);
```