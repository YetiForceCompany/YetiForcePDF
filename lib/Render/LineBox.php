<?php
declare(strict_types=0);
/**
 * LineBox class
 *
 * @package   YetiForcePDF\Render
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render;

/**
 * Class LineBox
 */
class LineBox extends Box
{

	/**
	 * Will this box fit in line? (or need to create new one)
	 * @param \YetiForcePDF\Render\InlineBox $box
	 * @return bool
	 */
	public function willFit(InlineBox $box)
	{
		$availableSpace = $this->getDimensions()->getWidth() - $this->getChildrenWidth();
		return $availableSpace >= $box->getDimensions()->getOuterWidth();
	}

	/**
	 * Are elements inside this line fit?
	 * @return bool
	 */
	public function elementsFit()
	{
		return $this->getDimensions()->getWidth() >= $this->getChildrenWidth();
	}

	/**
	 * Divide this line into more lines when objects doesn't fit
	 * @return LineBox[]
	 */
	public function divide()
	{
		$lineWidth = $this->getDimensions()->getWidth();
		$lines = [];
		if (!$this->elementsFit()) {
			$line = (new LineBox())->setDocument($this->document)->init();
			$line->getDimensions()->setWidth($lineWidth);
			foreach ($this->getChildren() as $childBox) {
				if ($line->willFit($childBox)) {
					$line->appendChild($childBox);
				} else {
					$lines[] = $line;
					$line = (new LineBox())->setDocument($this->document)->init();
					$line->getDimensions()->setWidth($lineWidth);
					$line->appendChild($childBox);
				}
			}
			// append last line
			$lines[] = $line;
			return $lines;
		}
		return [$this];
	}

	/**
	 * Get children width
	 * @return float|int
	 */
	public function getChildrenWidth()
	{
		$width = 0;
		foreach ($this->getChildren() as $childBox) {
			$width += $childBox->getDimensions()->getOuterWidth();
		}
		return $width;
	}


}
