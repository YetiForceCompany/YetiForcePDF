# YetiPDF
## PDF generation library for PHP (work in progress)

### usage

```PHP
<?php
$loader = require __DIR__ . '/vendor/autoload.php';

$document = new YetiPDF\Document();
$pdfFile = $document->render();
file_put_contents('test.pdf', $pdfFile);
```
