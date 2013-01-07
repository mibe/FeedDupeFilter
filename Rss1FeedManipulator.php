<?php

class Rss1FeedManipulator extends FeedManipulatorBase
{
	function __construct($rawFeed)
	{
		parent::__construct($rawFeed);
	}

	public function isSupported()
	{
		$rdf = $this->feed->documentElement;

		if ($rdf->tagName != 'rdf:RDF')
			return FALSE;

		return $rdf->isDefaultNamespace('http://purl.org/rss/1.0/');
	}

	public function parseFeed()
	{
		$items = $this->feed->getElementsByTagName('item');

		foreach($items as $item)
		{
			$fItem = new Rss1FeedItem();
			$fItem->parseXml($item);

			$this->items[] = $fItem;
		}
	}
}