<?php
declare(strict_types=1);
/**
 * PdfObject class
 *
 * @package   YetiPDF\Document\Objects
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Document\Objects;

/**
 * Class PdfObject
 */
class PdfObject
{
	/**
	 * Basic object type (integer, string, boolean, dictionary etc..)
	 * @var string
	 */
	protected $basicType = '';
	/**
	 * Id of the current object
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Get object id
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get raw id (that will exists in pdf file)
	 * @return string
	 */
	public function getRawId()
	{
		return $this->id . ' 0';
	}

	/**
	 * Get object basic type (integer,string, boolean, dictionary etc..)
	 * @return string
	 */
	public function getBasicType()
	{
		return $this->basicType;
	}
}
