<?php
declare(strict_types=1);
/**
 * BooleanObject class
 *
 * @package   YetiPDF\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Objects\Basic;

/**
 * Class BooleanObject
 */
class BooleanObject extends \YetiPDF\Objects\PdfObject
{

	/**
	 * Basic object type (integer, string, boolean, dictionary etc..)
	 * @var string
	 */
	protected $basicType = 'boolean';
}
