<?php
declare(strict_types=1);
/**
 * Span class
 *
 * @package   YetiForcePDF\Style
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style;

/**
 * Class Span
 */
class Span extends Style
{
	/**
	 * Css rules (computed)
	 * @var array
	 */
	protected $rules = [
		'font-family' => 'NotoSerif-Regular',
		'font-size' => '12px',
		'font-weight' => 'normal',
		'margin-left' => 0,
		'margin-top' => 0,
		'margin-right' => 0,
		'margin-bottom' => 0,
		'padding-left' => 0,
		'padding-top' => 0,
		'padding-right' => 0,
		'padding-bottom' => 0,
		'border-left-width' => 0,
		'border-top-width' => 0,
		'border-right-width' => 0,
		'border-bottom-width' => 0,
		'border-left-color' => [0, 0, 0, 0],
		'border-top-color' => [0, 0, 0, 0],
		'border-right-color' => [0, 0, 0, 0],
		'border-bottom-color' => [0, 0, 0, 0],
		'border-left-style' => 'none',
		'border-top-style' => 'none',
		'border-right-style' => 'none',
		'border-bottom-style' => 'none',
		'box-sizing' => 'border-box',
		'display' => 'inline',
		'width' => 'auto',
		'height' => 'auto',
		'overflow' => 'visible',
		'vertical-align' => 'baseline',
		'line-height' => '1.2',
		'background-color' => 'transparent',
		'color' => '#000000',
		'word-wrap' => 'normal',
		'max-width' => 'none',
		'min-width' => 0,
		'white-space' => 'normal',
	];
}
