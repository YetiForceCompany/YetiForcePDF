<?php

declare(strict_types=1);
/**
 * ImageStream class.
 *
 * @package   YetiForcePDF\Objects\Basic
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License v3
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Objects;

/**
 * Class ImageStream.
 */
class ImageStream extends \YetiForcePDF\Objects\Resource
{
	/**
	 * Object name.
	 *
	 * @var string
	 */
	protected $name = 'ImageStream';
	/**
	 * Text x position at current page.
	 *
	 * @var int
	 */
	protected $x = 0;
	/**
	 * Text y position at current page.
	 *
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
	 * Convert images to jpeg.
	 *
	 * @param string $imageData
	 *
	 * @return string
	 */
	protected function convertToJpg(string $imageData)
	{
		if (substr($imageData, 0, 10) === 'data:image') {
			$matches = [];
			preg_match('/data:image\/[a-z]+;base64,(.*)/', $imageData, $matches);
			$imageData = base64_decode($matches[1]);
		}
		$image = imagecreatefromstring($imageData);
		$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
		imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
		imagealphablending($bg, true);
		imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
		ob_start();
		imagejpeg($bg, null, 90);
		$imageString = ob_get_contents();
		ob_end_clean();
		imagedestroy($image);
		imagedestroy($bg);
		return $imageString;
	}

	/**
	 * Load image data.
	 *
	 * @param string $fileName
	 *
	 * @return $this
	 */
	public function loadImage(string $fileName)
	{
		if (substr($fileName, 0, 10) === 'data:image') {
			$this->imageData = $this->convertToJpg($fileName);
		} elseif (filter_var($fileName, FILTER_VALIDATE_URL)) {
			try {
				$client = new \GuzzleHttp\Client();
				$res = $client->request('GET', $fileName, ['verify' => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()]);
				if ($res->getStatusCode() === 200) {
					$res->getHeader('content-type');
					$this->imageData = $this->convertToJpg((string) $res->getBody());
				}
			} catch (\Exception $e) {
			}
		} else {
			if (file_exists($fileName)) {
				$this->imageData = $this->convertToJpg(file_get_contents($fileName));
			}
		}
		if ($this->imageData) {
			$info = getimagesizefromstring($this->imageData);
			$this->bitsPerComponent = (string) $info['bits'];
			$this->width = (string) $info[0];
			$this->height = (string) $info[1];
		}
		return $this;
	}

	/**
	 * Get image name.
	 *
	 * @return string
	 */
	public function getImageName()
	{
		return 'Im' . $this->id;
	}

	/**
	 * Get image width.
	 *
	 * @return string
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Get image height.
	 *
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
