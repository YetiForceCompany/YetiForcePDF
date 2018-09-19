<?php
declare(strict_types=1);
/**
 * Style class
 *
 * @package   YetiForcePDF\Style
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style;

/**
 * Class Parser
 */
class Style extends \YetiForcePDF\Base
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
	 * @var \YetiForcePDF\Html\Element
	 */
	protected $element;
	/**
	 * Parent style if exists
	 * @var null|\YetiForcePDF\Style\Style
	 */
	protected $parent = null;
	/**
	 * @var \YetiForcePDF\Objects\Font
	 */
	protected $font;
	/**
	 * Css rules
	 * @var array
	 */
	protected $rules = [];

	/**
	 * Initialisation
	 * @return \YetiForcePDF\Style\Style
	 */
	public function init(): Style
	{
		$this->rules = $this->parse();
		$this->font = (new \YetiForcePDF\Objects\Font())
			->setDocument($this->document)
			->setName($this->rules['font-family'])
			->setSize($this->rules['font-size'])
			->init();
		return $this;
	}

	/**
	 * Set element
	 * @param \YetiForcePDF\Html\Element $element
	 * @return \YetiForcePDF\Style\Style
	 */
	public function setElement(\YetiForcePDF\Html\Element $element): Style
	{
		$this->element = $element;
		return $this;
	}

	/**
	 * Set content
	 * @param string|null $content
	 * @return \YetiForcePDF\Style\Style
	 */
	public function setContent(string $content = null): Style
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * Set parent
	 * @param \YetiForcePDF\Style\Style $parent
	 * @return \YetiForcePDF\Style\Style
	 */
	public function setParent(Style $parent = null): Style
	{
		$this->parent = $parent;
		return $this;
	}

	/**
	 * Get rules
	 * @return array|mixed
	 */
	public function getRules()
	{
		return $this->rules;
	}

	/**
	 * Get current style font
	 * @return \YetiForcePDF\Objects\Font
	 */
	public function getFont(): \YetiForcePDF\Objects\Font
	{
		return $this->font;
	}

	/**
	 * Parse css style
	 * @return array
	 */
	protected function parse(): array
	{
		$parsed = [];
		if ($this->parent) {
			$parsed = $this->parent->getRules();
		}
		if ($this->content === null) {
			return $parsed;
		}
		$rules = explode(';', $this->content);
		foreach ($rules as $rule) {
			$rule = trim($rule);
			if ($rule !== '') {
				$ruleExploded = explode(':', $rule);
				$ruleName = trim($ruleExploded[0]);
				$ruleValue = trim($ruleExploded[1]);
				$ucRuleName = str_replace('-', '', ucwords($ruleName, '-'));
				$normalizerName = "YetiForcePDF\\Style\\Normalizer\\$ucRuleName";
				$normalizer = (new $normalizerName())->setDocument($this->document)->init();
				foreach ($normalizer->normalize($ruleValue) as $name => $value) {
					$parsed[$name] = $value;
				}
			}
		}
		return $parsed;
	}
}
