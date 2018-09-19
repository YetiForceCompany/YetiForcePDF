<?php
declare(strict_types=1);
/**
 * StreamObject class
 *
 * @package   YetiPDF\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Objects\Basic;

/**
 * Class StreamObject
 */
class StreamObject extends \YetiPDF\Objects\PdfObject
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
	 * Elements from which we will create content
	 * @var \YetiPDF\Html\Element[]
	 */
	protected $elements = [];
	/**
	 * Content of the stream as string instructions
	 * @var string[]
	 */
	protected $content = [];

	/**
	 * StreamObject constructor.
	 * @param \YetiPDF\Document $document
	 * @param bool              $addToDocument
	 */
	public function __construct(\YetiPDF\Document $document, bool $addToDocument = true)
	{
		$this->id = $document->getActualId();
		parent::__construct($document, $addToDocument);
	}

	/**
	 * Add html element
	 * @param \YetiPDF\Html\Element $element
	 * @return \YetiPDF\Objects\Basic\StreamObject
	 */
	public function addElement(\YetiPDF\Html\Element $element): \YetiPDF\Objects\Basic\StreamObject
	{
		$this->elements[] = $element;
		return $this;
	}

	/**
	 * Add raw content instructions as string
	 * @param string $content
	 * @return \YetiPDF\Objects\Basic\StreamObject
	 */
	public function addRawContent(string $content): \YetiPDF\Objects\Basic\StreamObject
	{
		$this->content[] = $content;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return '';
	}
}
