<?php
declare(strict_types=1);
/**
 * Trailer class
 *
 * @package   YetiPDF\Objects
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Objects;

/**
 * Class Font
 */
class Trailer extends \YetiPDF\Objects\PdfObject
{
	/**
	 * Object name
	 * @var string
	 */
	protected $name = 'Trailer';
	/**
	 * Root element
	 * @var \YetiPDF\Objects\PdfObject
	 */
	protected $root;

	/**
	 * Set root object
	 * @param \YetiPDF\Objects\PdfObject $root
	 */
	public function setRootObject(\YetiPDF\Objects\PdfObject $root)
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
