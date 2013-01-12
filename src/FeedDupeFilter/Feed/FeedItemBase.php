<?php
namespace FeedDupeFilter\Feed;

/**
 * Respresents a single entry of an feed.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
abstract class FeedItemBase
{
	/**
	 * Title of the entry. NULL if not available.
	 *
	 * @var string|null
	 */
	public $title;

	/**
	 * Description of the entry. NULL if not available.
	 *
	 * @var string|null
	 */
	public $description;

	/**
	 * Date of the entry. NULL if not available.
	 *
	 * @var string|null
	 */
	public $date;

	/**
	 * Link of the entry. NULL if not available.
	 *
	 * @var string|null
	 */
	public $link;

	/**
	 * ID of the entry. NULL if not available.
	 *
	 * @var string|null
	 */
	public $id;

	/**
	 * Corresponding XML element.
	 *
	 * @var DOMElement
	 */
	public $xmlElement;

	/**
	 * Constructor of the class.
	 *
	 * @param DOMElement Corresponding XML element containing the feed entry.
	 */
	function __construct(\DOMElement $xmlElement)
	{
		$this->xmlElement = $xmlElement;
	}

	/**
	 * Parses the XML of the feed entry.
	 *
	 * In this function the public fields are filled.
	 *
	 * @return void
	 */
	abstract public function parseXml();

	/**
	 * Returns the first child XML element or NULL if not found.
	 *
	 * Throws an exception if the tag name is empty or not a string.
	 *
	 * @param string Name of the XML tag.
	 * @return string|null
	 */
	private function getXmlChild($name)
	{
		if (empty($name) || !is_string($name))
			throw new \InvalidArgumentException('Invalid tag name given. Must be a non-empty string.');

		$list = $this->xmlElement->getElementsByTagName($name);

		return $list->length > 0 ? $list->item(0) : NULL;
	}

	/**
	 * Returns the value of first child XML element or NULL if not found.
	 *
	 * Throws an exception if the tag name is empty or not a string.
	 *
	 * @param string Name of the XML tag.
	 * @return string|null
	 */
	protected function getXmlChildValue($name)
	{
		if (empty($name) || !is_string($name))
			throw new \InvalidArgumentException('Invalid tag name given. Must be a non-empty string.');

		$child = $this->getXmlChild($name);

		return $child != NULL ? $child->nodeValue : NULL;
	}

	/**
	 * Returns the attribute value of the first child XML element or NULL if not found.
	 *
	 * Throws an exception if the tag or attribute name is empty or not a string.
	 *
	 * @param string Name of the XML tag.
	 * @param string Name of the attribute.
	 * @return string|null
	 */
	protected function getXmlChildAttributeValue($name, $attributeName)
	{
		if (empty($name) || !is_string($name))
			throw new \InvalidArgumentException('Invalid tag name given. Must be a non-empty string.');

		if (empty($attributeName) || !is_string($attributeName))
			throw new \InvalidArgumentException('Invalid attribute name given. Must be a non-empty string.');

		$child = $this->getXmlChild($name);

		if ($child == NULL)
			return NULL;

		$attr = $child->getAttribute($attributeName);

		return $attr != '' ? $attr : NULL;
	}

	/**
	 * Human representation of this instance.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return sprintf("[%s] %s (%s) (URL: %s)\n", $this->id, $this->title, $this->date, $this->link);
	}
}