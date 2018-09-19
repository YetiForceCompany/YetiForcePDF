<?php
declare(strict_types=1);
/**
 * Page class
 *
 * @package   YetiForcePDF\Document
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF;

/**
 * Class Pages
 */
class Pages extends \YetiForcePDF\Objects\Basic\DictionaryObject
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
		$kids = [];
		foreach ($this->children as $child) {
			$kids[] = $child->getReference();
		}
		return implode("\n", [
			$this->getRawId() . ' obj',
			'<<',
			'  /Type /Pages',
			'  /Count ' . count($kids),
			'  /Kids [' . implode("\n    ", $kids) . ']',
			'>>',
			'endobj'
		]);
	}
}
