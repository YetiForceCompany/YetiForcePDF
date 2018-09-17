<?php
declare(strict_types=1);
/**
 * BooleanObject class
 *
 * @package   YetiPDF\Document\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Document\Objects\Basic;

/**
 * Class BooleanObject
 */
class BooleanObject extends \YetiPDF\Document\Objects\PdfObject
{

	/**
	 * Basic object type (integer, string, boolean, dictionary etc..)
	 * @var string
	 */
	protected $basicType = 'boolean';
}
