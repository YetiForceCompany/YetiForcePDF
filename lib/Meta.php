<?php
declare(strict_types=1);
/**
 * Meta class
 *
 * @package   YetiForcePDF
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF;

/**
 * Class Meta
 */
class Meta extends Base
{
	/**
	 * @var string
	 */
	protected $author = 'YetiForce';
	/**
	 * @var string
	 */
	protected $creator = 'YetiForcePDF';
	/**
	 * @var string
	 */
	protected $title = '';
	/**
	 * @var string
	 */
	protected $topic = '';
	/**
	 * @var string[]
	 */
	protected $keywords = [];

	/**
	 * Set author
	 * @param string $author
	 * @return $this
	 */
	public function setAuthor(string $author)
	{
		$this->author = $author;
		return $this;
	}

	/**
	 * Get author
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * Set creator
	 * @param string $creator
	 * @return $this
	 */
	public function setCreator(string $creator)
	{
		$this->creator = $creator;
		return $this;
	}

	/**
	 * Get creator
	 * @return string
	 */
	public function getCreator()
	{
		return $this->creator;
	}

	/**
	 * Set title
	 * @param string $title
	 * @return $this
	 */
	public function setTitle(string $title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Get title
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set topic
	 * @param string $topic
	 * @return $this
	 */
	public function setTopic(string $topic)
	{
		$this->topic = $topic;
		return $this;
	}

	/**
	 * Get topic
	 * @return string
	 */
	public function getTopic()
	{
		return $this->topic;
	}

	/**
	 * Set keywords
	 * @param string[] $keywords
	 * @return $this
	 */
	public function setKeywords(array $keywords)
	{
		$this->keywords = $keywords;
		return $this;
	}

	/**
	 * Get keywords
	 * @return string[]
	 */
	public function getKeywords()
	{
		return $this->keywords;
	}
}
