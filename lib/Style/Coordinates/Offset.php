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
	 * Do we have left offset calculated already?
	 * @var bool
	 */
	protected $leftCalculated = false;

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
	 * @param string $phase
	 * @return $this
	 */
	public function calculateLeft(string $phase = 'inline')
	{
		$element = $this->style->getElement();
		$rules = $this->style->getRules();
		if ($element->isRoot()) {
			$pageCoord = $this->document->getCurrentPage()->getCoordinates();
			$this->left = $pageCoord->getAbsoluteHtmlX();
		} else {
			if ($parent = $this->style->getParent()) {
				$this->left += $parent->getRules('padding-left') + $parent->getRules('border-left-width');
			}
			$margin = ['top' => $rules['margin-top'], 'left' => $rules['margin-left']];
			if ($previous = $this->style->getPrevious()) {
				$previousDisplay = $previous->getRules('display');
				if ($previousDisplay !== 'block') {
					$this->left += $previous->getDimensions()->getWidth();
					$margin['left'] = max($margin['left'], $previous->getRules('margin-right'));
				}
				// previous of the previous - cumulative
				while ($previous = $previous->getPrevious()) {
					$previousDisplay = $previous->getRules('display');
					if ($previousDisplay !== 'block') {
						$this->left += $previous->getDimensions()->getWidth();
						if (isset($previousPrevious)) {
							$margin['left'] += max($previousPrevious->getRules('margin-right'), $previous->getRules('margin-left'));
						} else {
							$margin['left'] += $previous->getRules('margin-left');
						}
					}
					$previousPrevious = $previous;
				}
			}
			$this->left += $margin['left'];
		}
		var_dump($element->getDOMElement()->textContent . ' left:' . $this->left);
		$this->leftCalculated = true;
		return $this;
	}

	/**
	 * Calculate element offsets
	 * @param string $phase
	 * @return $this
	 */
	public function calculateTop(string $phase = 'inline')
	{
		$element = $this->style->getElement();
		$rules = $this->style->getRules();
		if ($element->isRoot()) {
			$pageCoord = $this->document->getCurrentPage()->getCoordinates();
			$this->top = $pageCoord->getAbsoluteHtmlY();
		} else {
			if ($parent = $this->style->getParent()) {
				$this->top += $parent->getRules('padding-top') + $parent->getRules('border-top-width');
				//var_dump('parent: ' . $parent->getRules('padding-top'));
			}
			$margin = ['top' => $rules['margin-top'], 'left' => $rules['margin-left']];
			if ($previous = $this->style->getPrevious()) {
				$previousDisplay = $previous->getRules('display');
				if ($previousDisplay === 'block') {
					$this->top += $previous->getDimensions()->getHeight();
					$margin['top'] = max($margin['top'], $previous->getRules('margin-bottom'));
				}
				// previous of the previous - cumulative
				while ($previous = $previous->getPrevious()) {
					$previousDisplay = $previous->getRules('display');
					if ($previousDisplay === 'block') {
						$this->top += $previous->getDimensions()->getHeight();
						if (isset($previousPrevious)) {
							$margin['top'] += max($previousPrevious->getRules('margin-bottom'), $previous->getRules('margin-bottom'));
						} else {
							$margin['top'] += $previous->getRules('margin-bottom');
						}
					}
					$previousPrevious = $previous;
					//var_dump('previous:' . " [$previousDisplay] " . $previous->getElement()->getDOMElement()->textContent . ' h:' . $previous->getDimensions()->getHeight() . ' t:' . $previous->getCoordinates()->getOffset()->getTop() . ' l:' . $previous->getCoordinates()->getOffset()->getLeft());
					//var_dump('current:' . " [{$rules['display']}] " . $element->getDOMElement()->textContent . ' h:' . $this->style->getDimensions()->getHeight() . ' t:' . ($this->top + $margin['top'] + $rules['border-top-width']) . ' l:' . ($this->left + $margin['left'] + $rules['border-left-width']));
				}
			}
			$this->top += $margin['top'];
		}
		var_dump($element->getDOMElement()->textContent . ' top:' . $this->top);
		return $this;
	}

	public function calculate()
	{
		if ($this->leftCalculated) {
			$this->calculateTop();
		} else {
			$this->calculateLeft();
		}
	}
}
