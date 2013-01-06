<?php

abstract class FeedItemBase
{
	public $title;
	public $description;
	public $date;
	public $link;

	public $id;

	abstract public function parseXml($xml);

	private function getXmlChild($domElement, $name)
	{
		$list = $domElement->getElementsByTagName($name);

		return $list->length > 0 ? $list->item(0) : NULL;
	}

	protected function getXmlChildValue($domElement, $name)
	{
		$child = $this->getXmlChild($domElement, $name);

		return $child != NULL ? $child->nodeValue : NULL;
	}

	protected function getXmlChildAttributeValue($domElement, $name, $attributeName)
	{
		$child = $this->getXmlChild($domElement, $name);

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