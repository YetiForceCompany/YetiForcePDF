<?php
declare(strict_types=1);
/**
 * InlineBox class
 *
 * @package   YetiForcePDF\Render
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render;

/**
 * Class InlineBox
 */
class InlineBox extends BlockBox
{

	/**
	 * Move children elements up recursively and make flat array of nested children
	 * @param \YetiForcePDF\Render\InlineBox $box
	 * @return $this
	 */
	public function moveUp()
	{
		$parent = $this->getParent();
		$allChildren = [];
		$this->getAllChildren($allChildren);
		foreach ($allChildren as $childBox) {
			$child = $childBox->getParent()->removeChild($childBox);
			$parent->appendChild($child);
		}
		return $this;
	}

	/**
	 * Convert text to words and wrap with InlineBox
	 */
	public function split()
	{
		if ($this->getElement()->isTextNode()) {
			$text = $this->getElement()->getText();
			$parent = $this->getParent();
			$words = explode(' ', $text);
			$count = count($words);
			foreach ($words as $index => $word) {
				if ($index !== $count - 1) {
					$word .= ' ';
				}
				$parent->createTextBox($word)->segregate()->measurePhaseOne();
			}
			$parent->removeChild($this);
		} else {
			foreach ($this->getChildren() as $box) {
				$box->split();
			}
		}
	}

}
