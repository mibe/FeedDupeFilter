<?php

class AtomFeedItem extends FeedItemBase
{
	public function __construct(DOMElement $xmlElement)
	{
		parent::__construct($xmlElement);
	}

	public function parseXml()
	{
		$this->title = $this->getXmlChildValue('title');
		$this->description = $this->getXmlChildValue('summary');
		$this->date = $this->getXmlChildValue('updated');
		$this->link = $this->getXmlChildAttributeValue('link', 'href');
		$this->id = $this->getXmlChildValue('id');
	}
}