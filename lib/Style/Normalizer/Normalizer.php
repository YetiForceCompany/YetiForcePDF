<?php
declare(strict_types=1);
/**
 * Normalizer class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class Normalizer
 */
class Normalizer extends \YetiForcePDF\Base
{

	/**
	 * @var \YetiForcePDF\Html\Element
	 */
	protected $element;

	/**
	 * Set element
	 * @param \YetiForcePDF\Html\Element $element
	 * @return $this
	 */
	public function setElement(\YetiForcePDF\Html\Element $element)
	{
		$this->element = $element;
		return $this;
	}

	/**
	 * Normalize css rule
	 * @param string $ruleValue
	 * @return array
	 */
	public function normalize(string $ruleValue)
	{
		return [];
	}
}
