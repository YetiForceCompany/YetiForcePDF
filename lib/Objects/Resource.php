<?php
declare(strict_types=1);
/**
 * Resource class
 *
 * @package   YetiPDF\Objects
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Objects;

/**
 * Class Resource
 */
class Resource extends \YetiPDF\Objects\PdfObject
{
	/**
	 * Resource type
	 * @var string
	 */
	protected $resourceType = '';

	/**
	 * Get resource type
	 * @return string
	 */
	public function getResourceType(): string
	{
		return $this->resourceType;
	}
}
