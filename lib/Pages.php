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
	 * Object name
	 * @var string
	 */
	protected $name = 'Pages';

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		$rendered = $this->getRawId() . " obj\n<<\n/Type /Pages\n/Count " . count($this->children) . "\n/Kids [";
		$kids = [];
		foreach ($this->children as $child) {
			$kids[] = $child->getReference();
		}
		$rendered .= implode("\n", $kids);
		return $rendered . "]\n>>\nendobj\n";
	}
}
