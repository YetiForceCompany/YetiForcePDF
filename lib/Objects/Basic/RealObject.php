<?php
declare(strict_types=1);
/**
 * RealObject class (real number)
 *
 * @package   YetiPDF\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Objects\Basic;

/**
 * Class RealObject
 */
class RealObject extends \YetiPDF\Objects\PdfObject
{
	/**
	 * Basic object type (integer, string, boolean, dictionary etc..)
	 * @var string
	 */
	protected $basicType = 'real';
	/**
	 * Object name
	 * @var string
	 */
	protected $name = 'Real';

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return '';
	}
}
