# YetiForcePDF
## PDF generation library for PHP (work in progress)

### example

```PHP
<?php
$loader = require __DIR__ . '/vendor/autoload.php';
$document = (new YetiForcePDF\Document())->init();
$document->loadHtml('<div style="font-family: Times-Roman;font-size:50px;">hello world!</div>');
$pdfFile = $document->render();
file_put_contents('test.pdf', $pdfFile);
```