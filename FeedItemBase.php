<?php

abstract class FeedItemBase
{
	public $title;
	public $description;
	public $date;
	public $link;

	public $id;

	abstract public function parseXml($xml);
}