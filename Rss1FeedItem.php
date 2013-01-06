<?php

class Rss1FeedItem extends FeedItemBase
{
	public function parseXml($xml)
	{
		$this->title = $this->getXmlChildValue($xml, 'title');
		$this->description = $this->getXmlChildValue($xml, 'description');
		$this->link = $this->getXmlChildValue($xml, 'link');
	}
}