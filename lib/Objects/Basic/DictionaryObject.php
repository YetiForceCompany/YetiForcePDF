<?php
declare(strict_types=1);
/**
 * DictionaryObject class
 *
 * @package   YetiPDF\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Objects\Basic;

/**
 * Class DictionaryObject
 */
class DictionaryObject extends \YetiPDF\Objects\PdfObject
{
	/**
	 * Basic object type (integer, string, boolean, dictionary etc..)
	 * @var string
	 */
	protected $basicType = 'dictionary';
	/**
	 * Object name
	 * @var string
	 */
	protected $name = 'Dictionary';
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

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return "<<\n\t/Type /{$this->dictionaryType}\n>>\n";
	}
}
