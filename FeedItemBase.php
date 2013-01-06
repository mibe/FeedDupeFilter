<?php

abstract class FeedItemBase
{
	public $title;
	public $description;
	public $date;
	public $link;

	public $id;

	abstract public function parseXml($xml);

	protected function getXmlChildValue($domElement, $name)
	{
		$list = $domElement->getElementsByTagName($name);

		if ($list->length == 0)
			return NULL;
		else
			return $list->item(0)->nodeValue;
	}
}