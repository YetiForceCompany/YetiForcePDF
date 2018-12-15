<?php

declare(strict_types=1);
/**
 * Parser class.
 *
 * @package   YetiForcePDF\Html
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Html;

use YetiForcePDF\Layout\BlockBox;
use YetiForcePDF\Layout\PageGroupBox;

/**
 * Class Parser.
 */
class Parser extends \YetiForcePDF\Base
{
	/**
	 * @var \DOMDocument
	 */
	protected $domDocument;
	/**
	 * @var string
	 */
	protected $html = '';
	/**
	 * @var array page groups with html content divided
	 */
	protected $htmlPageGroups = [];
	/**
	 * @var array
	 */
	protected $pageGroups = [];

	/**
	 * Cleanup html.
	 *
	 * @param string $html
	 * @param string $fromEncoding
	 *
	 * @return string
	 */
	protected function cleanUpHtml(string $html)
	{
		$html = preg_replace('/\r\n/', "\r", $html);
		$html = preg_replace('/\n/', "\r", $html);
		return $html;
	}

	/**
	 * Load html string.
	 *
	 * @param string $html
	 * @param string $fromEncoding
	 *
	 * @return \YetiForcePDF\Html\Parser
	 */
	public function loadHtml(string $html, string $fromEncoding = ''): \YetiForcePDF\Html\Parser
	{
		$config = \HTMLPurifier_Config::createDefault();
		$config->set('CSS.AllowTricky', true);
		$config->set('CSS.Proprietary', true);
		$config->set('CSS.Trusted', true);
		$config->set('HTML.Trusted', true);
		$config->set('CSS.AllowDuplicates', true);
		$config->set('Attr.EnableID', true);
		$def = $config->getHTMLDefinition(true);
		$def->addAttribute('div', 'data-header', new \HTMLPurifier_AttrDef_Text());
		$def->addAttribute('div', 'data-footer', new \HTMLPurifier_AttrDef_Text());
		$def->addAttribute('div', 'data-watermark', new \HTMLPurifier_AttrDef_Text());
		$purifier = new \HTMLPurifier($config);
		$html = mb_convert_encoding($html, 'UTF-8', 'HTML-ENTITIES');
		$this->html = $this->cleanUpHtml($html);
		//$this->html = $purifier->purify($this->html);
		$this->html = mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8');
		return $this;
	}

	/**
	 * Get html
	 * @return string
	 */
	public function getHtml()
	{
		return $this->html;
	}

	/**
	 * Divide html into page groups
	 * @param $html
	 * @return array
	 */
	public function getHtmlPageGroups($html)
	{
		$pageGroups = [];
		$matches = [];
		preg_match_all('/\<div\s+data-page-group\s?/ui', $html, $matches, PREG_OFFSET_CAPTURE);
		$matches = $matches[0];
		$groupsCount = count($matches);
		for ($i = 0; $i < $groupsCount; $i++) {
			$start = $matches[$i][1];
			if (isset($matches[$i + 1])) {
				$stop = $matches[$i + 1][1];
				$len = $stop - $start;
				$pageGroups[] = substr($html, $start, $len);
			} else {
				$pageGroups[] = substr($html, $start);
			}
		}
		if (!isset($pageGroups[0])) {
			return [$html];
		}
		return $pageGroups;
	}

	public function setGroupOptions(PageGroupBox $root, \DOMDocument $domDocument)
	{
		$childDomElement = $domDocument->documentElement->firstChild;
		if ($childDomElement->hasAttribute('data-format')) {
			$root->format = $childDomElement->getAttribute('data-format');
		}
		if ($childDomElement->hasAttribute('data-orientation')) {
			$root->orientation = $childDomElement->getAttribute('data-orientation');
		}
		if ($childDomElement->hasAttribute('data-margin-left')) {
			$root->marginLeft = $childDomElement->getAttribute('data-margin-left');
		}
		if ($childDomElement->hasAttribute('data-margin-right')) {
			$root->marginRight = $childDomElement->getAttribute('data-margin-right');
		}
		if ($childDomElement->hasAttribute('data-margin-top')) {
			$root->marginTop = $childDomElement->getAttribute('data-margin-top');
		}
		if ($childDomElement->hasAttribute('data-margin-bottom')) {
			$root->marginBottom = $childDomElement->getAttribute('data-margin-bottom');
		}
		if ($childDomElement->hasAttribute('data-header-top')) {
			$root->headerTop = $childDomElement->getAttribute('data-header-top');
		}
		if ($childDomElement->hasAttribute('data-footer-bottom')) {
			$root->footerBottom = $childDomElement->getAttribute('data-footer-bottom');
		}
		return $this;
	}

	/**
	 * Convert loaded html to pdf objects.
	 */
	public function parse()
	{
		if ($this->html === '') {
			return null;
		}
		$this->htmlPageGroups = $this->getHtmlPageGroups($this->html);
		foreach ($this->htmlPageGroups as $htmlPageGroup) {
			$domDocument = new \DOMDocument();
			$domDocument->loadHTML('<div id="yetiforcepdf">' . $htmlPageGroup . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_NOWARNING);
			$pageGroup = (new PageGroupBox())
				->setDocument($this->document)
				->setRoot(true)
				->init();
			$this->setGroupOptions($pageGroup, $domDocument);
			$page = $this->document->addPage($pageGroup->format, $pageGroup->orientation);
			$page->setPageNumber(1);
			$page->setMargins($pageGroup->marginLeft, $pageGroup->marginTop, $pageGroup->marginRight, $pageGroup->marginBottom);

			$rootElement = (new \YetiForcePDF\Html\Element())
				->setDocument($this->document)
				->setDOMElement($domDocument->documentElement);
			// root element must be defined before initialisation
			$rootElement->init();
			$pageGroup->setElement($rootElement);
			$pageGroup->setStyle($rootElement->parseStyle());

			$pageGroup->buildTree();
			$pageGroup->fixTables();
			$pageGroup->getStyle()->fixDomTree();
			$pageGroup->layout();
			$this->document->getCurrentPage()->setBox($pageGroup);

			foreach ($this->document->getPages() as $page) {
				$page->getBox()->breakPageAfter();
			}
			foreach ($this->document->getPages() as $page) {
				$page->breakOverflow();
			}
			foreach ($this->document->getPages() as $page) {
				$page->getBox()->spanAllRows();
			}
			$this->document->fixPageNumbers();
			foreach ($this->document->getPages() as $page) {
				$this->document->setCurrentPage($page);
				$children = [];
				$page->setUpAbsoluteBoxes();
				$page->getBox()->replacePageNumbers();
				$page->getBox()->getAllChildren($children);
				foreach ($children as $box) {
					if (!$box instanceof \YetiForcePDF\Layout\LineBox && $box->isRenderable()) {
						$page->getContentStream()->addRawContent($box->getInstructions());
					}
				}
			}
		}
	}
}
