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
	protected $resourceName = 'Font';
	/**
	 * Base font type aka font family
	 * @var string
	 */
	protected $family = 'Lato';
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
	 * Font info
	 * @var array
	 */
	protected $fontInfo = [];

	/**
	 * Initialisation
	 * @return $this
	 */
	public function init()
	{
		$this->loadFontsInfo();
		$alreadyExists = $this->document->getFontInstance($this->family);
		if (!$alreadyExists) {
			parent::init();
			$this->document->setFontInstance($this->family, $this);
			$this->fontNumber = 'F' . $this->document->getActualFontId();
			$this->fontInfo = $this->document->getFonts($this->family);
			foreach ($this->document->getObjects('Page') as $page) {
				$page->synchronizeFonts();
			}
			return $this;
		}
		$this->setAddToDocument(false);
		parent::init();
		return $alreadyExists;
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
	public function setFamily(string $name)
	{
		$this->family = $name;
		return $this;
	}

	/**
	 * Get font name
	 * @return string
	 */
	public function getFamily(): string
	{
		return $this->family;
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
	 * Get font size
	 * @return float
	 */
	public function getSize(): float
	{
		return $this->size;
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
		return $this->getRawId() . ' R';
	}

	/**
	 * Load fonts information if not exists already
	 */
	protected function loadFontsInfo()
	{
		if (empty($this->document->getFonts())) {
			foreach ($this->fontFiles as $name => $path) {
				$fontInfo = require($this->fontDir . $path);
				$this->document->setFontInfo($name, $fontInfo);
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return implode("\n", [$this->getRawId() . " obj",
			"<<",
			"  /Type /Font",
			"  /Subtype /" . $this->fontInfo['type'],
			"  /BaseFont /" . $this->family,
			">>",
			"endobj"]);
	}
}
