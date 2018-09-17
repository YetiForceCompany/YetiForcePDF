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
		$this->text = $text;
		return $this;
	}

	/**
	 * Set font
	 * @param \YetiPDF\Objects\Font $font
	 * @return \YetiPDF\Objects\TextStream
	 */
	public function setFont(\YetiPDF\Objects\Font $font, float $fontSize): \YetiPDF\Objects\TextStream
	{
		$this->font = $font;
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
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		$stream = 'BT ' . $this->x . ' ' . $this->y . ' Td /' . $this->font->getNumber() . ' ' . $this->fontSize . ' Tf (' . $this->text . ') Tj ET';
		return implode("\n", [
			$this->getRawId() . ' obj',
			"<<\n",
			"/Length " . strlen($stream),
			">>\n",
			"stream\n",
			$stream,
			"endstream\n",
			"endobj\n"
		]);
	}

}
