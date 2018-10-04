<?php
declare(strict_types=1);
/**
 * Box class
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
use \YetiForcePDF\Render\Coordinates\Coordinates;
use \YetiForcePDF\Render\Coordinates\Offset;
use \YetiForcePDF\Render\Dimensions\Dimensions;
use \YetiForcePDF\Render\Dimensions\BoxDimensions;


/**
 * Class Box
 */
class Box extends \YetiForcePDF\Base
{

	/**
	 * @var Box
	 */
	protected $parent;
	/**
	 * @var Box[]
	 */
	protected $children = [];
	/**
	 * @var Box
	 */
	protected $next;
	/**
	 * @var Box
	 */
	protected $previous;
	/*
	 * @var Dimensions
	 */
	protected $dimensions;
	/**
	 * @var Coordinates
	 */
	protected $coordinates;
	/**
	 * @var Offset
	 */
	protected $offset;

	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		parent::init();
		$this->dimensions = (new BoxDimensions())
			->setDocument($this->document)
			->setBox($this)
			->init();
		$this->coordinates = (new Coordinates())
			->setDocument($this->document)
			->setBox($this)
			->init();
		$this->offset = (new Offset())
			->setDocument($this->document)
			->setBox($this)
			->init();
		return $this;
	}

	/**
	 * Set parent
	 * @param \YetiForcePDF\Render\Box $parent
	 * @return $this
	 */
	public function setParent(Box $parent)
	{
		$this->parent = $parent;
		return $this;
	}

	/**
	 * Get parent
	 * @return \YetiForcePDF\Render\Box
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Set next
	 * @param \YetiForcePDF\Render\Box $next
	 * @return $this
	 */
	public function setNext(Box $next)
	{
		$this->next = $next;
		return $this;
	}

	/**
	 * Get next
	 * @return \YetiForcePDF\Render\Box
	 */
	public function getNext()
	{
		return $this->next;
	}

	/**
	 * Set previous
	 * @param \YetiForcePDF\Render\Box $previous
	 * @return $this
	 */
	public function setPrevious(Box $previous)
	{
		$this->previous = $previous;
		return $this;
	}

	/**
	 * Get previous
	 * @return \YetiForcePDF\Render\Box
	 */
	public function getPrevious()
	{
		return $this->previous;
	}

	/**
	 * Append child box
	 * @param Box $box
	 * @return $this
	 */
	public function appendChild(Box $box)
	{
		$box->setParent($this);
		$childrenCount = count($this->children);
		if ($childrenCount > 0) {
			$previous = $this->children[$childrenCount - 1];
			$box->setPrevious($previous);
			$previous->setNext($box);
		}
		$this->children[] = $box;
		return $this;
	}

	/**
	 * Append children boxes
	 * @param Box $box
	 * @return $this
	 */
	public function appendChildren(array $boxes)
	{
		foreach ($boxes as $box) {
			$this->appendChild($box);
		}
		return $this;
	}

	/**
	 * Get children
	 * @return Box[]
	 */
	public function getChildren(): array
	{
		return $this->children;
	}

	/**
	 * Get all children
	 * @param array $allChildren
	 * @return Box[]
	 */
	public function getAllChildren($allChildren = [])
	{
		$allChildren[] = $this;
		foreach ($this->getChildren() as $child) {
			$child->getAllChildren($allChildren);
		}
		return $allChildren;
	}

	/**
	 * Get dimensions
	 * @return Dimensions
	 */
	public function getDimensions()
	{
		return $this->dimensions;
	}

	/**
	 * Get coordinates
	 * @return Coordinates
	 */
	public function getCoordinates()
	{
		return $this->coordinates;
	}

	/**
	 * Shorthand for offset
	 * @return Offset
	 */
	public function getOffset(): Offset
	{
		return $this->offset;
	}

	/**
	 * Filter text
	 * Filter the text, this is applied to all text just before being inserted into the pdf document
	 * it escapes the various things that need to be escaped, and so on
	 *
	 * @return string
	 */
	protected function filterText($text)
	{
		$text = trim(preg_replace('/[\n\r\t\s]+/', ' ', mb_convert_encoding($text, 'UTF-8')));
		$text = preg_replace('/\s+/', ' ', $text);
		$text = mb_convert_encoding($text, 'UTF-16');
		return strtr($text, [')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r']);
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
		$rules = $this->style->getRules();
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
		$style = $this->getStyle();
		$font = $style->getFont();
		$fontStr = '/' . $font->getNumber() . ' ' . $font->getSize() . ' Tf';
		$coordinates = $this->getCoordinates();
		$pdfX = $coordinates->getPdfX();
		$pdfY = $coordinates->getPdfY();
		$htmlX = $coordinates->getX();
		$htmlY = $coordinates->getY();
		$dimensions = $this->getDimensions();
		$width = $dimensions->getWidth();
		$height = $dimensions->getHeight();
		$element = $this->getElement();
		$textWidth = $style->getFont()->getTextWidth($element->getDOMElement()->textContent);
		$textHeight = $style->getFont()->getTextHeight();
		$baseLine = $style->getFont()->getDescender();
		$baseLineY = $pdfY - $baseLine;
		if ($this->getElement()->isTextNode()) {
			$textContent = '(' . $this->filterText($element->getDOMElement()->textContent) . ')';
			$element = [
				'q',
				"1 0 0 1 $pdfX $baseLineY cm % html x:$htmlX y:$htmlY",
				'BT',
				$fontStr,
				"$textContent Tj",
				'ET',
				'Q'
			];
			if ($this->drawTextOutline) {
				$element = array_merge($element, [
					'q',
					'1 w',
					'1 0 0 RG',
					"1 0 0 1 $pdfX $pdfY cm",
					"0 0 $textWidth $textHeight re",
					'S',
					'Q'
				]);
			}
		} else {
			$element = [];
			$element = $this->addBorderInstructions($element, $pdfX, $pdfY, $width, $height);
		}
		return implode("\n", $element);
	}

}
