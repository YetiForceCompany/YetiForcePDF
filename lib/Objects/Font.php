<?php
declare(strict_types=1);
/**
 * Font class
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
class Font extends \YetiForcePDF\Objects\Resource
{
	/**
	 * Which type of dictionary (Page, Catalog, Font etc...)
	 * @var string
	 */
	protected $resourceType = 'Font';
	/**
	 * Object name
	 * @var string
	 */
	protected $name = 'Font';
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
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		$this->fontNumber = 'F' . $this->document->getActualFontId();
		parent::init();
		foreach ($this->document->getObjects('Page') as $page) {
			$page->addResource($this);
		}
		return $this;
	}

	/**
	 * Set font number
	 * @param string $number
	 * @return \YetiForcePDF\Objects\Font
	 */
	public function setNumber(string $number): \YetiForcePDF\Objects\Font
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
		return '/' . $this->fontNumber . ' ' . $this->getRawId() . ' R';
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return implode("\n", [$this->getRawId() . " obj",
			"<<",
			"  /Type /Font",
			"  /Subtype /TrueType",
			"  /BaseFont /" . $this->baseFont,
			">>",
			"endobj"]);
	}
}
