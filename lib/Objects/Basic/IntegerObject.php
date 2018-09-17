<?php
declare(strict_types=1);
/**
 * IntegerObject class
 *
 * @package   YetiPDF\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Objects\Basic;

/**
 * Class IntegerObject
 */
class IntegerObject extends \YetiPDF\Objects\PdfObject
{
	/**
	 * Basic object type (integer, string, boolean, dictionary etc..)
	 * @var string
	 */
	protected $basicType = 'integer';

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return '';
	}
}
