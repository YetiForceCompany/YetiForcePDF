<?php
declare(strict_types=1);
/**
 * Trailer class
 *
 * @package   YetiForcePDF\Objects
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Objects;

/**
 * Class Font
 */
class Trailer extends \YetiForcePDF\Objects\PdfObject
{
	/**
	 * @var string
	 */
	protected $basicType = 'Trailer';
	/**
	 * Object name
	 * @var string
	 */
	protected $name = 'Trailer';
	/**
	 * Root element
	 * @var \YetiForcePDF\Objects\PdfObject
	 */
	protected $root;

	/**
	 * Set root object
	 * @param \YetiForcePDF\Objects\PdfObject $root
	 */
	public function setRootObject(\YetiForcePDF\Objects\PdfObject $root)
	{
		$this->root = $root;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return "trailer\n<< /Root " . $this->root->getReference() . " >>\n";
	}
}
