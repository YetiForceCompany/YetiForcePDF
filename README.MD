# YetiPDF
## PDF generation library for PHP (work in progress)

### usage

```PHP
$loader = require __DIR__ . '/vendor/autoload.php';

$document = new YetiPDF\Document();
$document->addText("hello world", 12, 0, 0);
$pdfFile = $document->render();
file_put_contents('test.pdf', $pdfFile);
```
