<?php

/**
 * Represents an feed item in RSS 2.0 format.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class Rss2FeedItem extends FeedItemBase
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(DOMElement $xmlElement)
	{
		parent::__construct($xmlElement);
	}

	/**
	 * {@inheritdoc}
	 */
	public function parseXml()
	{
		$this->title = $this->getXmlChildValue('title');
		$this->description = $this->getXmlChildValue('description');
		$this->date = $this->getXmlChildValue('pubDate');
		$this->link = $this->getXmlChildValue('link');
		$this->id = $this->getXmlChildValue('guid');
	}
}