<?php
declare(strict_types=1);
/**
 * Catalog class
 *
 * @package   YetiPDF\Document
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Document;

/**
 * Class Catalog
 */
class Catalog extends \YetiPDF\Document\PdfObject
{
	protected $type = 'Catalog';
	protected $pages = [];
}
