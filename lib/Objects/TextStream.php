<?php
declare(strict_types=1);
/**
 * TextStream class
 *
 * @package   YetiPDF\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Objects;

/**
 * Class TextStream
 */
class TextStream extends \YetiPDF\Objects\Basic\StreamObject
{
	/**
	 * Object name
	 * @var string
	 */
	protected $name = 'TextStream';
	/**
	 * Text stream
	 * @var string
	 */
	protected $text = '';
	/**
	 * Font for text stream
	 * @var \YetiPDF\Objects\Font
	 */
	protected $font;
	/**
	 * Font size
	 * @var float
	 */
	protected $fontSize = 10;
	/**
	 * Text x position at current page
	 * @var int
	 */
	protected $x = 0;
	/**
	 * Text y position at current page
	 * @var int
	 */
	protected $y = 0;

	/**
	 * Set text
	 * @param string $text
	 * @return \YetiPDF\Objects\Basic\TextStream
	 */
	public function setText(string $text): \YetiPDF\Objects\TextStream
	{
		$this->text = $this->escape($text);
		return $this;
	}

	/**
	 * Set font
	 * @param \YetiPDF\Objects\Font $font
	 * @return \YetiPDF\Objects\TextStream
	 */
	public function setFont(\YetiPDF\Objects\Font $font): \YetiPDF\Objects\TextStream
	{
		$this->font = $font;
		return $this;
	}

	/**
	 * Set font size
	 * @param float $fontSize
	 * @return \YetiPDF\Objects\TextStream
	 */
	public function setFontSize(float $fontSize): \YetiPDF\Objects\TextStream
	{
		$this->fontSize = $fontSize;
		return $this;
	}

	/**
	 * Set text x position inside current page
	 * @param float $x
	 * @return \YetiPDF\Objects\TextStream
	 */
	public function setX(float $x): \YetiPDF\Objects\TextStream
	{
		$this->x = $x;
		return $this;
	}

	/**
	 * Set text x position inside current page
	 * @param float $y
	 * @return \YetiPDF\Objects\TextStream
	 */
	public function setY(float $y): \YetiPDF\Objects\TextStream
	{
		$this->y = $y;
		return $this;
	}

	/**
	 * Escape string
	 * @param string $str
	 * @return string
	 */
	public function escape(string $str): string
	{
		return strtr($str, [')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r']);
	}

	/**
	 * Get raw text stream
	 * @return string
	 */
	public function getRawStream(): string
	{
		return 'BT ' . $this->x . ' ' . $this->y . ' Td /' . $this->font->getNumber() . ' ' . $this->fontSize . ' Tf (' . $this->text . ') Tj ET';
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		$stream = $this->getRawStream();
		return implode("\n", [
			$this->getRawId() . ' obj',
			"<<",
			" /Length " . strlen($stream),
			">>",
			"stream",
			$stream,
			"endstream",
			"endobj"
		]);
	}

}
