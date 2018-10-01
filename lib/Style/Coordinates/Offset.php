<?php
declare(strict_types=1);
/**
 * Offset class
 *
 * @package   YetiForcePDF\Style\Coordinates
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Coordinates;

/**
 * Class Offset
 */
class Offset extends \YetiForcePDF\Base
{
	/**
	 * @var \YetiForcePDF\Style\Style
	 */
	protected $style;
	/**
	 * Offset top
	 * @var float
	 */
	protected $top = 0;
	/**
	 * Offset left
	 * @var int
	 */
	protected $left = 0;

	/**
	 * Get offset top
	 * @return float
	 */
	public function getTop()
	{
		return $this->top;
	}

	/**
	 * Set offset top
	 * @param float $top
	 * @return $this
	 */
	public function setTop(float $top)
	{
		$this->top = $top;
		return $this;
	}

	/**
	 * Add offset top
	 * @param float $add
	 * @return $this
	 */
	public function addTop(float $add)
	{
		$this->top += $add;
		return $this;
	}

	/**
	 * Get offset left
	 * @return float
	 */
	public function getLeft()
	{
		return $this->left;
	}

	/**
	 * Set offset left
	 * @param float $left
	 * @return $this
	 */
	public function setLeft(float $left)
	{
		$this->left = $left;
		return $this;
	}

	/**
	 * Add offset left
	 * @param float $add
	 * @return $this
	 */
	public function addLeft(float $add)
	{
		$this->left += $add;
		return $this;
	}

	/**
	 * Set style
	 * @param \YetiForcePDF\Style\Style $style
	 * @return $this
	 */
	public function setStyle(\YetiForcePDF\Style\Style $style)
	{
		$this->style = $style;
		return $this;
	}

	/**
	 * Get style
	 * @return \YetiForcePDF\Style\Style
	 */
	public function getStyle()
	{
		return $this->style;
	}

	/**
	 * Calculate element offsets
	 * @return $this
	 */
	public function calculateLeft(array $withRules = null)
	{
		var_dump($withRules);
		$element = $this->style->getElement();
		$rules = $this->style->getRules();
		if ($element->isRoot()) {
			$this->left = $this->document->getCurrentPage()->getCoordinates()->getAbsoluteHtmlX();
		} else {
			$parent = $this->style->getParent();
			$this->left = $parent->getRules('padding-left') + $parent->getRules('border-left-width');
			$margin = ['top' => $rules['margin-top'], 'left' => $rules['margin-left']];
			if ($previous = $this->style->getPrevious()) {
				$previousDisplay = $previous->getRules('display');
				if ($previousDisplay !== 'block' && $rules['display'] !== 'block') {
					$this->left = $previous->getOffset()->getLeft() + $previous->getDimensions()->getWidth();
					$margin['left'] = max($margin['left'], $previous->getRules('margin-right'));
					var_dump($element->getText() . ' is NOT block ' . $this->left);
				} else {
					var_dump($element->getText() . ' is block ' . $this->left . ' :' . $this->style->getRules('display'));
				}
			}
			$this->left += $margin['left'];
		}
		if ($withRules !== null) {
			var_dump('left', count($this->style->getChildren($withRules)));
			foreach ($this->style->getChildren($withRules) as $child) {
				$child->getOffset()->calculateLeft($withRules);
			}
		}
		//var_dump($element->getDOMElement()->textContent . ' left:' . $this->left);
		return $this;
	}

	/**
	 * Calculate element offsets
	 * @return $this
	 */
	public function calculateTop(array $withRules = null)
	{
		$element = $this->style->getElement();
		$rules = $this->style->getRules();
		if ($element->isRoot()) {
			$this->top = $this->document->getCurrentPage()->getCoordinates()->getAbsoluteHtmlY();
		} else {
			$parent = $this->style->getParent();
			$this->top = $parent->getRules('padding-top') + $parent->getRules('border-top-width');
			$margin = ['top' => $rules['margin-top'], 'left' => $rules['margin-left']];
			if ($previous = $this->style->getPrevious()) {
				$previousDisplay = $previous->getRules('display');
				if ($previousDisplay === 'block' || $rules['display'] === 'block') {
					$this->top = $previous->getOffset()->getTop() + $previous->getDimensions()->getHeight();
					$margin['top'] = max($margin['top'], $previous->getRules('margin-bottom'));
				}
			}
			$this->top += $margin['top'];
		}
		if ($withRules !== null) {
			foreach ($this->style->getChildren($withRules) as $child) {
				$child->getOffset()->calculateTop($withRules);
			}
		}
		//var_dump($element->getDOMElement()->textContent . ' top:' . $this->top);
		return $this;
	}

}
