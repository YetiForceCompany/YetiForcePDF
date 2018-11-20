<?php
declare(strict_types=1);
/**
 * ImageStream class
 *
 * @package   YetiForcePDF\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Objects;

/**
 * Class ImageStream
 */
class ImageStream extends \YetiForcePDF\Objects\Resource
{
    /**
     * Object name
     * @var string
     */
    protected $name = 'ImageStream';
    /**
     * Text x position at current page
     * @var int
     */
    protected $x = 0;
    /**
     * Text y position at current page
     * @var int
     */
    protected $y = 0;
    /**
     * @var string Image data
     */
    protected $imageData = '';
    /**
     * @var string image original width
     */
    protected $width = '0';
    /**
     * @var string image original height
     */
    protected $height = '0';
    /**
     * @var int bits per component
     */
    protected $bitsPerComponent = 8;

    /**
     * Load image data
     * @param string $fileName
     * @return $this
     */
    public function loadImage(string $fileName)
    {
        $this->imageData = file_get_contents($fileName);
        $info = getimagesize($fileName);
        $this->bitsPerComponent = $info['bits'];
        $this->width = $info[0];
        $this->height = $info[1];
        return $this;
    }

    /**
     * Get image name
     * @return string
     */
    public function getImageName()
    {
        return 'Im' . $this->id;
    }

    /**
     * Get image width
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get image height
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * {@inheritdoc}
     */
    public function render(): string
    {
        $stream = $this->imageData;
        return implode("\n", [
            $this->getRawId() . ' obj',
            '<<',
            '  /Type /XObject',
            '  /Subtype  /Image',
            '  /ColorSpace  /DeviceRGB',
            '  /Width ' . $this->width,
            '  /Height ' . $this->height,
            '  /BitsPerComponent ' . $this->bitsPerComponent,
            '  /Length  ' . strlen($stream),
            '  /Filter /DCTDecode',
            '>>',
            'stream',
            $stream,
            'endstream',
            'endobj'
        ]);
    }

}
