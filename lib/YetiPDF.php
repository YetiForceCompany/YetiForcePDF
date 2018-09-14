<?php
declare(strict_types=1);

/**
 * YetiPDF class for generating pdf documents out of html
 *
 * @package   YetiPDF
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF;

/**
 * Class YetiPDF
 */
class YetiPDF
{
	/**
	 * Main document instance
	 * @var \YetiPDF\Document\Document $document
	 */
	private $document;

	/**
	 * YetiPDF constructor.
	 */
	public function __construct()
	{
		$this->document = new \YetiPDF\Document\Document();
	}

	/**
	 * Get document instance
	 * @return \YetiPDF\Document\Document
	 */
	public function getDocument(): \YetiPDF\Document\Document
	{
		return $this->document;
	}
}
