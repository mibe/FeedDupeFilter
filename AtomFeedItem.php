<?php

class AtomFeedItem extends FeedItemBase
{
	public function parseXml($xml)
	{
		$this->title = $this->getXmlChildValue($xml, 'title');
		$this->description = $this->getXmlChildValue($xml, 'summary');
		$this->date = $this->getXmlChildValue($xml, 'updated');
		$this->link = $this->getXmlChildAttributeValue($xml, 'link', 'href');
		$this->id = $this->getXmlChildValue($xml, 'id');
	}
}