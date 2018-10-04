<?php
declare(strict_types=1);
/**
 * BlockBox class
 *
 * @package   YetiForcePDF\Render
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render;

use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;

/**
 * Class BlockBox
 */
class BlockBox extends Box
{

	/**
	 * @var Element
	 */
	protected $element;
	/**
	 * @var Style
	 */
	protected $style;

	/**
	 * Get element
	 * @return Element
	 */
	public function getElement()
	{
		return $this->element;
	}

	/**
	 * Set element
	 * @param Element $element
	 * @return $this
	 */
	public function setElement(Element $element)
	{
		$this->element = $element;
		$this->style = $element->getStyle();
		return $this;
	}

	/**
	 * Get style
	 * @return Style
	 */
	public function getStyle(): Style
	{
		return $this->style;
	}

	/**
	 * Reflow elements and create render tree basing on dom tree
	 * @return $this
	 */
	public function reflow()
	{
		$boxInLine = [];
		foreach ($this->getElement()->getChildren() as $childElement) {
			// make render box from the dom element
			$box = (new BlockBox())
				->setDocument($this->document)
				->setElement($childElement)
				->init();
			if ($childElement->getStyle()->getRules('display') === 'block') {
				if (isset($boxInLine[1])) { // faster than count()
					// finish line and add to current children boxes as line box
					$lineBox = (new LineBox())
						->setDocument($this->document)
						->init();
					$lineBox->appendChildren($boxInLine);
					$this->appendChild($lineBox);
					$boxInLine = [];
				}
				$this->appendChild($box);
				continue;
			}
			$boxInLine[] = $box;
		}
		// finish line and add to current children boxes as line box
		$lineBox = (new LineBox())
			->setDocument($this->document)
			->init();
		$lineBox->appendChildren($boxInLine);
		$this->appendChild($lineBox);

		return $this;
	}
}
