<?php
namespace FeedDupeFilter\Feed;

/**
 * Represents an feed item in ATOM format.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class AtomFeedItem extends FeedItemBase
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(\DOMElement $xmlElement)
	{
		parent::__construct($xmlElement);
	}

	/**
	 * {@inheritdoc}
	 */
	public function parseXml()
	{
		$this->title = $this->getXmlChildValue('title');
		$this->description = $this->getXmlChildValue('summary');
		$this->date = $this->getXmlChildValue('updated');
		$this->link = $this->getXmlChildAttributeValue('link', 'href');
		$this->id = $this->getXmlChildValue('id');
	}
}