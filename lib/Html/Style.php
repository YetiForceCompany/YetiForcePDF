<?php
declare(strict_types=1);
/**
 * Style class
 *
 * @package   YetiPDF\Html
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Html;

/**
 * Class Parser
 */
class Style
{
	/**
	 * @var \YetiPDF\Document
	 */
	protected $document;
	/**
	 * CSS text to parse
	 * @var string|null
	 */
	protected $content = null;
	/**
	 * Parent style if exists
	 * @var null|\YetiPDF\Html\Style
	 */
	protected $parent = null;
	/**
	 * @var \YetiPDF\Objects\Font
	 */
	protected $font;

	/**
	 * Style constructor.
	 * @param \YetiPDF\Document        $document
	 * @param string|null              $content
	 * @param \YetiPDF\Html\Style|null $parent
	 */
	public function __construct(\YetiPDF\Document $document, string $content = null, Style $parent = null)
	{
		$this->document = $document;
		$this->content = $content;
		$this->parent = $parent;
		$this->font = new \YetiPDF\Objects\Font($document);
	}

	/**
	 * Get current style font
	 * @return \YetiPDF\Objects\Font
	 */
	public function getFont(): \YetiPDF\Objects\Font
	{
		return $this->font;
	}
}
