<?php
declare(strict_types=1);
/**
 * Color class
 *
 * @package   YetiForcePDF\Style
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style;

/**
 * Class Color
 */
class Color
{
	public static function fromHash(string $hashColor)
	{
		$color = substr($hashColor, 1);
		if (strlen($color) === 3) {
			$r = substr($color, 0, 1);
			$g = substr($color, 1, 1);
			$b = substr($color, 2, 1);
			$a = 'F';
			$color = "$r$r$g$g$b$b$a$a";
		}
		if (strlen($color) === 4) {
			$r = substr($color, 0, 1);
			$g = substr($color, 1, 1);
			$b = substr($color, 2, 1);
			$a = substr($color, 3, 1);
			$color = "$r$r$g$g$b$b$a$a";
		}
		if (strlen($color) === 6) {
			$color .= 'FF';
		}
		$r = hexdec(substr($color, 0, 2));
		$g = hexdec(substr($color, 2, 2));
		$b = hexdec(substr($color, 4, 2));
		$a = hexdec(substr($color, 6, 2));
		return [$r, $g, $b, $a];
	}

	public static function fromRGB(string $rgbColor)
	{
		$color = preg_match('/rgb\(([0-9]+)\,([0-9]_)\,([0-9]+)\)/', str_replace("\n\t\r ", '', $rgbColor));
		// TODO transform rgb color to [r,g,b,a]
	}

	public static function toRGBA(string $colorInput, bool $inPDFColorSpace = false)
	{
		if ($colorInput[0] === '#') {
			$color = static::fromHash($colorInput);
		}
		$r = $inPDFColorSpace ? $color[0] / 255 : $color[0];
		$g = $inPDFColorSpace ? $color[1] / 255 : $color[1];
		$b = $inPDFColorSpace ? $color[2] / 255 : $color[2];
		$a = $inPDFColorSpace ? $color[3] / 255 : $color[3];
		return [$r, $g, $b, $a];
	}

	public static function toPdfString(string $colorInput)
	{
		$color = static::toRGBA($colorInput);
		return "{$color[0]} {$color[1]} {$color[2]} RG";
	}

	public static function RGBtoPdfString(int $r, int $g, int $b)
	{
		$r = $r / 255;
		$g = $g / 255;
		$b = $b / 255;
		return "$r $g $b RG";
	}

}
