<?php
declare(strict_types=1);
/**
 * StreamObject class
 *
 * @package   YetiForcePDF\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Objects\Basic;

/**
 * Class StreamObject
 */
class StreamObject extends \YetiForcePDF\Objects\PdfObject
{
	/**
	 * Basic object type (integer, string, boolean, dictionary etc..)
	 * @var string
	 */
	protected $basicType = 'Stream';
	/**
	 * Object name
	 * @var string
	 */
	protected $name = 'Stream';
	/**
	 * Content of the stream as string instructions
	 * @var string[]
	 */
	protected $content = [];

	/**
	 * StreamObject constructor.
	 * @param \YetiForcePDF\Document $document
	 * @param bool                   $addToDocument
	 */
	public function __construct(\YetiForcePDF\Document $document, bool $addToDocument = true)
	{
		$this->id = $document->getActualId();
		parent::__construct($document, $addToDocument);
	}

	/**
	 * Add raw content instructions as string
	 * @param string $content
	 * @return \YetiForcePDF\Objects\Basic\StreamObject
	 */
	public function addRawContent(string $content): \YetiForcePDF\Objects\Basic\StreamObject
	{
		$this->content[] = $content;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		$stream = implode("\n", $this->content);
		return implode("\n", [
			$this->getRawId() . ' obj',
			"<<",
			"  /Length " . \strlen($stream),
			">>",
			"stream",
			$stream,
			"endstream",
			"endobj"
		]);
	}
}
