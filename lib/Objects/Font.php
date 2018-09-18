<?php
declare(strict_types=1);
/**
 * Font class
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
class Font extends \YetiPDF\Objects\Resource
{
	/**
	 * Which type of dictionary (Page, Catalog, Font etc...)
	 * @var string
	 */
	protected $resourceType = 'Font';
	/**
	 * Base font type
	 * @var string
	 */
	protected $baseFont = 'Arial';
	/**
	 * Font number
	 * @var string
	 */
	protected $fontNumber = 'F1';

	/**
	 * Set font number
	 * @param string $number
	 * @return \YetiPDF\Objects\Font
	 */
	public function setNumber(string $number): \YetiPDF\Objects\Font
	{
		$this->fontNumber = $number;
		return $this;
	}

	/**
	 * Get font number
	 * @return string
	 */
	public function getNumber(): string
	{
		return $this->fontNumber;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getReference(): string
	{
		return '<< /' . $this->fontNumber . ' ' . $this->getRawId() . ' R >>';
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return implode("\n", [$this->getRawId() . " obj",
			"<<",
			"/Type /Font",
			"/Subtype /TrueType",
			"/BaseFont /" . $this->baseFont,
			">>",
			"endobj"]);
	}
}
