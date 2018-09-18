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
	protected $dictionaryType = 'Pages';

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		$rendered = $this->getRawId() . " obj\n<<\n/Type /Pages\n/Count " . count($this->children) . "\n/Kids [";
		foreach ($this->children as $child) {
			$rendered .= $child->getReference() . ' ';
		}
		return $rendered . "]\n>>\nendobj\n";
	}
}
