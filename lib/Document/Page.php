<?php
declare(strict_types=1);
/**
 * Page class
 *
 * @package   YetiPDF\Document
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Document;

/**
 * Class Document
 */
class Page extends \YetiPDF\Document\Objects\Basic\DictionaryObject
{
	/**
	 * {@inheritdoc}
	 */
	protected $dictionaryType = 'Page';
	/**
	 * Portrait page orientation
	 */
	const ORIENTATION_PORTRAIT = 'P';
	/**
	 * Landscape page orientation
	 */
	const ORIENTATION_LANDSCAPE = 'L';
	/**
	 * Current page format
	 * @var string $format
	 */
	protected $format;
	/**
	 * Current page orientation
	 * @var string $orientation
	 */
	protected $orientation;

	/**
	 * Document constructor.
	 * @param string $format
	 * @param string $orientation
	 */
	public function __construct(string $format = 'A4', string $orientation = self::ORIENTATION_PORTRAIT)
	{
		$this->format = $format;
		$this->orientation = $orientation;
	}

}
