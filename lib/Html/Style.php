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
	 * CSS text to parse
	 * @var string
	 */
	protected $content = '';
	/**
	 * Parent style if exists
	 * @var null|\YetiPDF\Html\Style
	 */
	protected $parent = null;

	/**
	 * Style constructor.
	 * @param string                   $content
	 * @param \YetiPDF\Html\Style|null $parent
	 */
	public function __construct(string $content, Style $parent = null)
	{
		$this->content = $content;
		$this->parent = $parent;
	}
}
