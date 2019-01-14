<?php

$loader = require '../vendor/autoload.php';

\YetiForcePDF\Document::addFonts([
	[
		'family' => 'Pacifico',
		'weight' => '400',
		'style' => 'normal',
		'file' => 'd:\fonts\Pacifico\Pacifico-Regular.ttf'
	],
	[
		'family' => 'Lobster Two',
		'weight' => '400',
		'style' => 'normal',
		'file' => 'd:\fonts\Lobster_Two\LobsterTwo-Regular.ttf'
	],
	[
		'family' => 'Lobster Two',
		'weight' => 'bold',
		'style' => 'normal',
		'file' => 'd:\fonts\Lobster_Two\LobsterTwo-Bold.ttf'
	],
]);

$document = (new YetiForcePDF\Document())->init();
$document->loadHtml(file_get_contents('CustomFonts.html'));
$pdfFile = $document->render();
file_put_contents('CustomFonts.pdf', $pdfFile);
