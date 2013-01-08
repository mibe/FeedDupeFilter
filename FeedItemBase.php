<?php

abstract class FeedItemBase
{
	public $title;
	public $description;
	public $date;
	public $link;

	public $id;

	public $xmlElement;

	function __construct(DOMElement $xmlElement)
	{
		$this->xmlElement = $xmlElement;
	}

	abstract public function parseXml();

	private function getXmlChild($name)
	{
		$list = $this->xmlElement->getElementsByTagName($name);

		return $list->length > 0 ? $list->item(0) : NULL;
	}

	protected function getXmlChildValue($name)
	{
		$child = $this->getXmlChild($name);

		return $child != NULL ? $child->nodeValue : NULL;
	}

	protected function getXmlChildAttributeValue($name, $attributeName)
	{
		$child = $this->getXmlChild($name);

		if ($child == NULL)
			return NULL;

		$attr = $child->getAttribute($attributeName);

		return $attr != '' ? $attr : NULL;
	}

	public function __toString()
	{
		return sprintf("[%s] %s (%s) (URL: %s)\n", $this->id, $this->title, $this->date, $this->link);
	}
}