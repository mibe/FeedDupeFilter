<?php

abstract class ArchiveBase
{
	protected $contents;
	protected $archiveIdentifier;

	function __construct($archiveIdentifier)
	{
		$this->archiveIdentifier = $archiveIdentifier;

		$this->load();
	}

	function __destruct()
	{
		$this->save();
	}

	abstract protected function load();
	abstract protected function save();

	public function add($uid)
	{
		if (!$this->contains($uid))
			$this->contents[] = $uid;
	}

	public function remove($uid)
	{
		if (($key = array_search($uid, $this->contents, TRUE)) !== FALSE)
			unset($this->contents[$key]);
	}

	public function contains($uid)
	{
		return in_array($uid, $this->contents, TRUE);
	}

	public function clear()
	{
		$this->contents = array();
	}
}
