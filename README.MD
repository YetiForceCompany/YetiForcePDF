# YetiForcePDF
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b2e8645f5091496089ed203d05a05d61)](https://app.codacy.com/app/mariuszkrzaczkowski/YetiForcePDF?utm_source=github.com&utm_medium=referral&utm_content=YetiForceCompany/YetiForcePDF&utm_campaign=Badge_Grade_Settings)
[![SymfonyInsight](https://insight.symfony.com/projects/2988ef66-83db-4ed9-9ba8-6fb4474566bd/big.png)](https://insight.symfony.com/projects/2988ef66-83db-4ed9-9ba8-6fb4474566bd)

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
