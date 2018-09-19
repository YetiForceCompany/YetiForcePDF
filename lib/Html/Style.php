<?php
declare(strict_types=1);
/**
 * Style class
 *
 * @package   YetiForcePDF\Html
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Html;

/**
 * Class Parser
 */
class Style
{
	/**
	 * @var \YetiForcePDF\Document
	 */
	protected $document;
	/**
	 * CSS text to parse
	 * @var string|null
	 */
	protected $content = null;
	/**
	 * Parent style if exists
	 * @var null|\YetiForcePDF\Html\Style
	 */
	protected $parent = null;
	/**
	 * @var \YetiForcePDF\Objects\Font
	 */
	protected $font;

	/**
	 * Style constructor.
	 * @param \YetiForcePDF\Document        $document
	 * @param string|null              $content
	 * @param \YetiForcePDF\Html\Style|null $parent
	 */
	public function __construct(\YetiForcePDF\Document $document, string $content = null, Style $parent = null)
	{
		$this->document = $document;
		$this->content = $content;
		$this->parent = $parent;
		$this->font = new \YetiForcePDF\Objects\Font($document);
	}

	/**
	 * Get current style font
	 * @return \YetiForcePDF\Objects\Font
	 */
	public function getFont(): \YetiForcePDF\Objects\Font
	{
		return $this->font;
	}
}
