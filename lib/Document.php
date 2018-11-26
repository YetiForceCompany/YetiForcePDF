<?php
declare(strict_types=1);
/**
 * Document class
 *
 * @package   YetiForcePDF
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF;

use YetiForcePDF\Html\Element;


/**
 * Class Document
 */
class Document
{

    /**
     * Actual id auto incremented
     * @var int
     */
    protected $actualId = 0;
    /**
     * Main output buffer / content for pdf file
     * @var string
     */
    protected $buffer = '';
    /**
     * Main entry point - root element
     * @var \YetiForcePDF\Catalog $catalog
     */
    protected $catalog;
    /**
     * Pages dictionary
     * @var Pages
     */
    protected $pagesObject;
    /**
     * Current page object
     * @var Page
     */
    protected $currentPageObject;
    /**
     * @var string default page format
     */
    protected $defaultFormat = 'A4';
    /**
     * @var string default page orientation
     */
    protected $defaultOrientation = \YetiForcePDF\Page::ORIENTATION_PORTRAIT;

    /**
     * @var Page[] all pages in the document
     */
    protected $pages = [];
    /**
     * Default page margins
     * @var array
     */
    protected $defaultMargins = [
        'left' => 40,
        'top' => 40,
        'right' => 40,
        'bottom' => 40
    ];
    /**
     * All objects inside document
     * @var \YetiForcePDF\Objects\PdfObject[]
     */
    protected $objects = [];
    /**
     * @var \YetiForcePDF\Html\Parser
     */
    protected $htmlParser;
    /**
     * Fonts data
     * @var array
     */
    protected $fontsData = [];
    /**
     * @var array
     */
    protected $fontInstances = [];
    /**
     * Actual font id
     * @var int
     */
    protected $actualFontId = 0;
    /**
     * @var bool $debugMode
     */
    protected $debugMode = false;

    /**
     * Are we debugging?
     * @return bool
     */
    public function inDebugMode()
    {
        return $this->debugMode;
    }

    /**
     * Initialisation
     * @return $this
     */
    public function init()
    {
        $this->catalog = (new \YetiForcePDF\Catalog())->setDocument($this)->init();
        $this->pagesObject = $this->catalog->addChild((new Pages())->setDocument($this)->init());
        $this->currentPageObject = $this->addPage($this->defaultFormat, $this->defaultOrientation);
        return $this;
    }

    /**
     * Set default page format
     * @param string $defaultFormat
     * @return $this
     */
    public function setDefaultFormat(string $defaultFormat)
    {
        $this->defaultFormat = $defaultFormat;
        return $this;
    }

    /**
     * Set default page orientation
     * @param string $defaultOrientation
     * @return $this
     */
    public function setDefaultOrientation(string $defaultOrientation)
    {
        $this->defaultOrientation = $defaultOrientation;
        return $this;
    }

    /**
     * Set default page margins
     * @param float $left
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @return $this
     */
    public function setDefaultMargins(float $left, float $top, float $right, float $bottom)
    {
        $this->defaultMargins = [
            'left' => $left,
            'top' => $top,
            'right' => $right,
            'bottom' => $bottom,
            'horizontal' => $left + $right,
            'vertical' => $top + $bottom
        ];
        return $this;
    }

    /**
     * Get actual id for newly created object
     * @return int
     */
    public function getActualId()
    {
        return ++$this->actualId;
    }

    /**
     * Get actual id for newly created font
     * @return int
     */
    public function getActualFontId(): int
    {
        return ++$this->actualFontId;
    }

    /**
     * Set font
     * @param string $family
     * @param string $weight
     * @param string $style
     * @param \YetiForcePDF\Objects\Font $fontInstance
     * @return $this
     */
    public function setFontInstance(string $family, string $weight, string $style, \YetiForcePDF\Objects\Font $fontInstance)
    {
        $this->fontInstances[$family][$weight][$style] = $fontInstance;
        return $this;
    }

    /**
     * Get font instance
     * @param string $family
     * @param string $weight
     * @param string $style
     * @return null|\YetiForcePDF\Objects\Font
     */
    public function getFontInstance(string $family, string $weight, string $style)
    {
        if (!empty($this->fontInstances[$family][$weight][$style])) {
            return $this->fontInstances[$family][$weight][$style];
        }
        return null;
    }

    /**
     * Get all font instances
     * @return \YetiForcePDF\Objects\Font[]
     */
    public function getAllFontInstances()
    {
        $instances = [];
        foreach ($this->fontInstances as $family => $weights) {
            foreach ($weights as $weight => $styles) {
                foreach ($styles as $instance) {
                    $instances[] = $instance;
                }
            }
        }
        return $instances;
    }

    /**
     * Set font information
     * @param string $family
     * @param string $weight
     * @param string $style
     * @param \FontLib\TrueType\File $font
     * @return $this
     */
    public function setFontData(string $family, string $weight, string $style, \FontLib\TrueType\File $font)
    {
        if (empty($this->fontsData[$family][$weight][$style])) {
            $this->fontsData[$family][$weight][$style] = $font;
        }
        return $this;
    }

    /**
     * Get font data
     * @param string $family
     * @param string $weight
     * @param string $style
     * @return \FontLib\Font|null
     */
    public function getFontData(string $family, string $weight, string $style)
    {
        if (!empty($this->fontsData[$family][$weight][$style])) {
            return $this->fontsData[$family][$weight][$style];
        }
        return null;
    }

    /**
     * Get pages object
     * @return \YetiForcePDF\Pages
     */
    public function getPagesObject(): \YetiForcePDF\Pages
    {
        return $this->pagesObject;
    }

    /**
     * Get default page format
     * @return string
     */
    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }

    /**
     * Get default page orientation
     * @return string
     */
    public function getDefaultOrientation()
    {
        return $this->defaultOrientation;
    }

    /**
     * Get default margins
     * @return array
     */
    public function getDefaultMargins()
    {
        return $this->defaultMargins;
    }

    /**
     * Add page to the document
     * @param string $format - optional format 'A4' for example
     * @param string $orientation - optional orientation 'P' or 'L'
     * @param Page|null $page - we can add cloned page or page from other document too
     * @return \YetiForcePDF\Page
     */
    public function addPage(string $format = '', string $orientation = '', Page $page = null): \YetiForcePDF\Page
    {
        if ($page === null) {
            $page = (new Page())->setDocument($this)->init();
        }
        if (!$format) {
            $format = $this->defaultFormat;
        }
        if (!$orientation) {
            $orientation = $this->defaultOrientation;
        }
        $page->setOrientation($orientation)->setFormat($format);
        $this->pages[] = $page;
        $this->currentPageObject = $page;
        return $page;
    }

    /**
     * Get current page
     * @return \YetiForcePDF\Page
     */
    public function getCurrentPage(): \YetiForcePDF\Page
    {
        return $this->currentPageObject;
    }

    /**
     * Get all pages
     * @return Page[]
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Get document header
     * @return string
     */
    protected function getDocumentHeader(): string
    {
        return "%PDF-1.7\n%âăĎÓ\n";
    }

    /**
     * Get document footer
     * @return string
     */
    protected function getDocumentFooter(): string
    {
        return '%%EOF';
    }

    /**
     * Add object to document
     * @param \YetiForcePDF\Objects\Basic\StreamObject $stream
     * @return \YetiForcePDF\Document
     */
    public function addObject(\YetiForcePDF\Objects\PdfObject $object): \YetiForcePDF\Document
    {
        $this->objects[] = $object;
        return $this;
    }

    /**
     * Remove object from document
     * @param \YetiForcePDF\Objects\PdfObject $object
     * @return \YetiForcePDF\Document
     */
    public function removeObject(\YetiForcePDF\Objects\PdfObject $object): \YetiForcePDF\Document
    {
        $this->objects = array_filter($this->objects, function ($currentObject) use ($object) {
            return $currentObject !== $object;
        });
        return $this;
    }

    /**
     * Load html string
     * @param string $html
     * @return \YetiForcePDF\Document
     */
    public function loadHtml(string $html): \YetiForcePDF\Document
    {
        $this->htmlParser = (new \YetiForcePDF\Html\Parser())->setDocument($this)->init();
        $this->htmlParser->loadHtml($html);
        return $this;
    }

    /**
     * Count objects
     * @param string $name - object name
     * @return int
     */
    public function countObjects(string $name = ''): int
    {
        if ($name === '') {
            return count($this->objects);
        }
        $typeCount = 0;
        foreach ($this->objects as $object) {
            if ($object->getName() === $name) {
                $typeCount++;
            }
        }
        return $typeCount;
    }

    /**
     * Get objects
     * @param string $name - object name
     * @return \YetiForcePDF\Objects\PdfObject[]
     */
    public function getObjects(string $name = ''): array
    {
        if ($name === '') {
            return $this->objects;
        }
        return array_filter($this->objects, function ($currentObject) use ($name) {
            return $currentObject->getName() === $name;
        });
    }

    /**
     * Layout document content to pdf string
     * @return string
     */
    public function render(): string
    {
        $this->buffer = '';
        $this->buffer .= $this->getDocumentHeader();
        $this->htmlParser->parse();
        $trailer = (new \YetiForcePDF\Objects\Trailer())
            ->setDocument($this)
            ->init();
        $trailer->setRootObject($this->catalog)->setSize(count($this->objects) - 1);
        foreach ($this->objects as $object) {
            if (in_array($object->getBasicType(), ['Dictionary', 'Stream', 'Trailer', 'Array'])) {
                $this->buffer .= $object->render() . "\n";
            }
        }
        $this->buffer .= $this->getDocumentFooter();
        $this->removeObject($trailer);
        return $this->buffer;
    }

}
