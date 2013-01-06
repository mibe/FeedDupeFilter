<?php

class Rss2FeedItem extends FeedItemBase
{
	public function parseXml($xml)
	{
		$this->title = $this->getXmlChildValue($xml, 'title');
		$this->description = $this->getXmlChildValue($xml, 'description');
		$this->date = $this->getXmlChildValue($xml, 'pubDate');
		$this->link = $this->getXmlChildValue($xml, 'link');
		$this->id = $this->getXmlChildValue($xml, 'guid');
	}
}