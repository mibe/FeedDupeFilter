<?php

class Rss1FeedItem extends FeedItemBase
{
	public function __construct(DOMElement $xmlElement)
	{
		parent::__construct($xmlElement);
	}

	public function parseXml()
	{
		$this->title = $this->getXmlChildValue('title');
		$this->description = $this->getXmlChildValue('description');
		$this->link = $this->getXmlChildValue('link');
	}
}