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
	protected $fontDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fonts' . DIRECTORY_SEPARATOR;
	protected $fontFiles = [
		'Lato' => 'lato.php',
		'Lato-Bold' => 'latob.php',
		'Lato-BoldItalic' => 'latobi.php',
		'Lato-Italic' => 'latoi.php',

		'PTSerif' => 'pt_serif.php',
		'PTSerif-Bold' => 'pt_serifb.php',
		'PTSerif-BoldItalic' => 'pt_serifbi.php',
		'PTSerif-Italic' => 'pt_serifi.php',

		'PTMono' => 'pt_mono.php',
	];
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
	protected $baseFont = 'Lato';
	/**
	 * Font number
	 * @var string
	 */
	protected $fontNumber = 'F1';
	/**
	 * Font size
	 * @var float
	 */
	protected $size = 12;

	/**
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		$this->fontNumber = 'F' . $this->document->getActualFontId();
		parent::init();
		foreach ($this->document->getObjects('Page') as $page) {
			$page->addResource('Font', $this->getNumber(), $this);
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
	 * Set font name
	 * @param string $base
	 * @return $this
	 */
	public function setName(string $name)
	{
		$this->baseFont = $name;
		return $this;
	}

	/**
	 * Set Font size
	 * @param float $size
	 * @return $this
	 */
	public function setSize(float $size)
	{
		$this->size = $size;
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
	 * Get font size
	 * @return float
	 */
	public function getSize(): float
	{
		return $this->size;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getReference(): string
	{
		return $this->getRawId() . ' R';
	}

	protected function loadFont()
	{
		$fileName = $this->fontDir . $this->fontFiles[$this->name];

	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return implode("\n", [$this->getRawId() . " obj",
			"<<",
			"  /Type /Font",
			"  /Subtype /Type1",
			"  /BaseFont /" . $this->baseFont,
			">>",
			"endobj"]);
	}
}
