<?php

class Rss1FeedManipulator extends FeedManipulatorBase
{
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
			$fItem = new Rss1FeedItem($item);
			$fItem->parseXml();

			$this->items[] = $fItem;
		}
	}
}