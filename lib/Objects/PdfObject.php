<?php
declare(strict_types=1);
/**
 * PdfObject class
 *
 * @package   YetiPDF\Objects
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiPDF\Objects;

/**
 * Class PdfObject
 */
class PdfObject
{
	/**
	 * Basic object type (integer, string, boolean, dictionary etc..)
	 * @var string
	 */
	protected $basicType = '';
	/**
	 * Id of the current object
	 * @var int
	 */
	protected $id = 1;

	/**
	 * Children elements - referenced
	 * @var array
	 */
	protected $children = [];
	/**
	 * Parent object
	 * @var \YetiPDF\Objects\PdfObject
	 */
	protected $parent;

	/**
	 * PdfObject constructor.
	 * @param int $id
	 */
	public function __construct(int $id)
	{
		$this->id = $id;
	}

	/**
	 * Get object id
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get raw id (that will exists in pdf file)
	 * @return string
	 */
	public function getRawId()
	{
		return $this->id . ' 0';
	}

	/**
	 * Get object basic type (integer,string, boolean, dictionary etc..)
	 * @return string
	 */
	public function getBasicType()
	{
		return $this->basicType;
	}

	/**
	 * Get children elements (pages etc)
	 * @param bool $all - do we want all children from tree (flat structure)?
	 */
	public function getChildren(bool $all = false, array &$current = []): array
	{
		if ($all) {
			foreach ($this->children as $child) {
				$current[] = $child;
				$child->getChildren(true, $current);
			}
			return $current;
		}
		return $this->children;
	}

	/**
	 * Add child object
	 * @param \YetiPDF\Objects\PdfObject $child
	 * @return \YetiPDF\Objects\PdfObject
	 * @throws \InvalidArgumentException
	 */
	public function addChild(\YetiPDF\Objects\PdfObject $child)
	{
		if (in_array($this->getBasicType(), ['dictionary', 'array'])) {
			$child->setParent($this);
			return $this->children[] = $child;
		}
		throw new \InvalidArgumentException("Object of basic type '{$this->basicType}' cannot have a child.");
	}

	/**
	 * Set parent object
	 * @param \YetiPDF\Objects\PdfObject $parent
	 * @return \YetiPDF\Objects\PdfObject
	 */
	public function setParent(\YetiPDF\Objects\PdfObject $parent): \YetiPDF\Objects\PdfObject
	{
		$this->parent = $parent;
		return $this;
	}

	/**
	 * Render current object
	 * @return string
	 */
	public abstract function render();

}
