<?php

class Rss2FeedItem extends FeedItemBase
{
	public function __construct(DOMElement $xmlElement)
	{
		parent::__construct($xmlElement);
	}

	public function parseXml()
	{
		$this->title = $this->getXmlChildValue('title');
		$this->description = $this->getXmlChildValue('description');
		$this->date = $this->getXmlChildValue('pubDate');
		$this->link = $this->getXmlChildValue('link');
		$this->id = $this->getXmlChildValue('guid');
	}
}