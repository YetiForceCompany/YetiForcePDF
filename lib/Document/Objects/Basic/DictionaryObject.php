<?php
declare(strict_types=1);
/**
 * DictionaryObject class
 *
 * @package   YetiPDF\Document\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Document\Objects\Basic;

/**
 * Class DictionaryObject
 */
class DictionaryObject extends \YetiPDF\Document\Objects\PdfObject
{
	/**
	 * Basic object type (integer, string, boolean, dictionary etc..)
	 * @var string
	 */
	protected $basicType = 'dictionary';
	/**
	 * Which type of dictionary (Page, Catalog, Font etc...)
	 * @var string
	 */
	protected $dictionaryType = '';

	/**
	 * Get dictionary type (Page, Catalog, Font etc...)
	 * @return string
	 */
	public function getDictionaryType()
	{
		return $this->dictionaryType;
	}
}
