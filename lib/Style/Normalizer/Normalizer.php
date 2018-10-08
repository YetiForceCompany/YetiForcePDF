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

use YetiForcePDF\Style\Style;

/**
 * Class Normalizer
 */
class Normalizer extends \YetiForcePDF\Base
{

	/**
	 * @var Style
	 */
	protected $style;

	/**
	 * Set style
	 * @param \YetiForcePDF\Style\Style $style
	 * @return $this
	 */
	public function setStyle(Style $style)
	{
		$this->style = $style;
		return $this;
	}

	/**
	 * Get normalizer class name
	 * @param string $ruleName
	 * @return string
	 */
	public static function getNormalizerClassName(string $ruleName)
	{
		$ucRuleName = str_replace('-', '', ucwords($ruleName, '-'));
		return "YetiForcePDF\\Style\\Normalizer\\$ucRuleName";

	}

	/**
	 * Normalize css rule
	 * @param mixed $ruleValue
	 * @return array
	 */
	public function normalize($ruleValue)
	{
		return [];
	}
}
