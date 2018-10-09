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
	 * @param \YetiForcePDF\Render\Box $box
	 * @return bool
	 */
	public function willFit(Box $box)
	{
		$availableSpace = $this->getDimensions()->getWidth() - $this->getChildrenWidth();
		$boxOuterWidth = $box->getDimensions()->getOuterWidth();
		return bccomp((string)$availableSpace, (string)$boxOuterWidth) >= 0;
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
	 * Break word normal
	 * @param \YetiForcePDF\Render\Box $box
	 * @return Box[]
	 */
	protected function wrapWordNormal(Box $box)
	{
		if ($box->getElement()->isTextNode()) {
			$text = $box->getElement()->getText();
			$parent = $this->getParent();
			$words = explode(' ', $text);
			$count = count($words);
			$boxes = [];
			foreach ($words as $index => $word) {
				if ($index !== $count - 1) {
					$word .= ' ';
				}
				$boxes[] = $currentBox = $parent->createTextBox($word);
				// create box inside parent box and detach it to put it into splitted lines later
				$currentBox->segregate();
				$currentBox->measurePhaseOne();
				$parent->removeChild($currentBox);
			}
			return $boxes;
		}
		return [$box];
	}

	/**
	 * Break words into elements
	 * @param Box $box
	 * @return Box[]
	 */
	protected function wrapWord(Box $box)
	{
		$wordBreak = $box->getStyle()->getRules('word-wrap');
		switch ($wordBreak) {
			case 'normal':
				return $this->wrapWordNormal($box);
			case 'break-word':
				//return $this->breakWordBreakWord($box);
		}
		return [$box];
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
			$line->setParent($this->getParent());
			foreach ($this->getChildren() as $childBox) {
				$wrapped = $this->wrapWord($childBox);
				foreach ($wrapped as $wrappedChild) {
					if ($line->willFit($wrappedChild)) {
						$line->appendChild($wrappedChild);
					} else {
						$lines[] = $line;
						$line = (new LineBox())->setDocument($this->document)->init();
						$line->setParent($this->getParent());
						$line->getDimensions()->setWidth($lineWidth);
						$line->appendChild($wrappedChild);
					}
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

	/**
	 * Get children height
	 * @return float|int
	 */
	public function getChildrenHeight()
	{
		$height = 0;
		foreach ($this->getChildren() as $childBox) {
			$height += $childBox->getDimensions()->getOuterHeight();
		}
		return $height;
	}

	/**
	 * Add border instructions
	 * @param array $element
	 * @param float $pdfX
	 * @param float $pdfY
	 * @param float $width
	 * @param float $height
	 * @return array
	 */
	protected function addBorderInstructions(array $element, float $pdfX, float $pdfY, float $width, float $height)
	{
		$rules = [
			'font-family' => 'NotoSerif-Regular',
			'font-size' => 12,
			'font-weight' => 'normal',
			'margin-left' => 0,
			'margin-top' => 0,
			'margin-right' => 0,
			'margin-bottom' => 0,
			'padding-left' => 0,
			'padding-top' => 0,
			'padding-right' => 0,
			'padding-bottom' => 0,
			'border-left-width' => 1,
			'border-top-width' => 1,
			'border-right-width' => 1,
			'border-bottom-width' => 1,
			'border-left-color' => [1, 0, 0, 1],
			'border-top-color' => [1, 0, 0, 1],
			'border-right-color' => [1, 0, 0, 1],
			'border-bottom-color' => [1, 0, 0, 1],
			'border-left-style' => 'solid',
			'border-top-style' => 'solid',
			'border-right-style' => 'solid',
			'border-bottom-style' => 'solid',
			'box-sizing' => 'border-box',
			'display' => 'block',
			'width' => 'auto',
			'height' => 'auto',
			'overflow' => 'visible',
		];
		$x1 = 0;
		$x2 = $width;
		$y1 = $height;
		$y2 = 0;
		$element[] = '% start border';
		if ($rules['border-top-width'] && $rules['border-top-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x2, $y1]),
				implode(' ', [$x2 - $rules['border-right-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x1 + $rules['border-left-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x1, $y1])
			]);
			$borderTop = [
				'q',
				"{$rules['border-top-color'][0]} {$rules['border-top-color'][1]} {$rules['border-top-color'][2]} rg",
				"1 0 0 1 $pdfX $pdfY cm",
				"$x1 $y1 m", // move to start point
				$path . ' l h',
				'F',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		if ($rules['border-right-width'] && $rules['border-right-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x2, $y2]),
				implode(' ', [$x2 - $rules['border-right-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x2 - $rules['border-right-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x2, $y1]),
			]);
			$borderTop = [
				'q',
				"1 0 0 1 $pdfX $pdfY cm",
				"{$rules['border-right-color'][0]} {$rules['border-right-color'][1]} {$rules['border-right-color'][2]} rg",
				"$x2 $y1 m",
				$path . ' l h',
				'F',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		if ($rules['border-bottom-width'] && $rules['border-bottom-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x2, $y2]),
				implode(' ', [$x2 - $rules['border-right-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x1 + $rules['border-left-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x1, $y2]),
			]);
			$borderTop = [
				'q',
				"1 0 0 1 $pdfX $pdfY cm",
				"{$rules['border-bottom-color'][0]} {$rules['border-bottom-color'][1]} {$rules['border-bottom-color'][2]} rg",
				"$x1 $y2 m",
				$path . ' l h',
				'F',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		if ($rules['border-left-width'] && $rules['border-left-style'] !== 'none') {
			$path = implode(" l\n", [
				implode(' ', [$x1 + $rules['border-left-width'], $y1 - $rules['border-top-width']]),
				implode(' ', [$x1 + $rules['border-left-width'], $y2 + $rules['border-bottom-width']]),
				implode(' ', [$x1, $y2]),
				implode(' ', [$x1, $y1]),
			]);
			$borderTop = [
				'q',
				"1 0 0 1 $pdfX $pdfY cm",
				"{$rules['border-left-color'][0]} {$rules['border-left-color'][1]} {$rules['border-left-color'][2]} rg",
				"$x1 $y1 m",
				$path . ' l h',
				'F',
				'Q'
			];
			$element = array_merge($element, $borderTop);
		}
		$element[] = '% end border';
		return $element;
	}

	/**
	 * Get element PDF instructions to use in content stream
	 * @return string
	 */
	public function getInstructions(): string
	{

		$coordinates = $this->getCoordinates();
		$pdfX = $coordinates->getPdfX();
		$pdfY = $coordinates->getPdfY();
		$dimensions = $this->getDimensions();
		$width = $dimensions->getWidth();
		$height = $dimensions->getHeight();
		$element = [];
		$element = $this->addBorderInstructions($element, $pdfX, $pdfY, $width, $height);

		return implode("\n", $element);
	}
}
