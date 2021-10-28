<?php

$html = '<table style="border:1px solid #000;border-collapse:collapse;"><tbody>';
for ($i = 0; $i < 100; ++$i) {
	$html .= '<tr><td style="border:1px solid red;">' . $i . '</td><td style="border:1px solid green;">item</td></tr>';
}
$html .= '</tbody></table>';
