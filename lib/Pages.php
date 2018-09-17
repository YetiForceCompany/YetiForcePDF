<?php
declare(strict_types=1);
/**
 * Page class
 *
 * @package   YetiPDF\Document
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF;

/**
 * Class Pages
 */
class Pages extends \YetiPDF\Objects\Basic\DictionaryObject
{
	/**
	 * {@inheritdoc}
	 */
	protected $dictionaryType = 'Page';


}
