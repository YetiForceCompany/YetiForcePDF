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
	protected $root;

	public function setRootObject(\YetiPDF\Objects\PdfObject $root)
	{
		$this->root = $root;
	}

	public function render(): string
	{
		return "trailer\n<< /Root " . $this->root->getReference() . " >>\n";
	}
}
