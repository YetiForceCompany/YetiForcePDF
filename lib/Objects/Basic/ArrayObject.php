<?php
declare(strict_types=1);
/**
 * ArrayObject class
 *
 * @package   YetiPDF\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Objects\Basic;

/**
 * Class ArrayObject
 */
class ArrayObject extends \YetiPDF\Objects\PdfObject
{
	/**
	 * Basic object type (integer, string, boolean, dictionary etc..)
	 * @var string
	 */
	protected $basicType = 'Array';
	/**
	 * Object name
	 * @var string
	 */
	protected $name = 'Array';
	/**
	 * Collection of items
	 * @var array
	 */
	protected $items = [];

	public function addItem($item): \YetiPDF\Objects\Basic\ArrayObject
	{
		$this->items[] = $item;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		$stringItems = [];
		foreach ($this->items as $item) {
			if ($item instanceof \YetiPDF\Objects\PdfObject) {
				$stringItems[] = $item->getReference();
			}
		}
		return '[ ' . implode(' ', $stringItems) . ' ]';
	}
}
