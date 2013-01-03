<?php

class FileArchive extends IArchive
{
	private $file;
	private $contents;

	function __construct($file)
	{
		$this->file = $file;

		// Only unserialize if the data file exists.
		// If not, start with an empty archive.
		if (file_exists($file))
		{
			$data = file_get_contents($file);
			$this->contents = unserialize($data);
		}
		else
			$this->contents = array();
	}

	function __destruct()
	{
		$data = serialize($this->contents);
		$bytes = file_put_contents($this->file, $data);

		if ($bytes === FALSE)
			throw new Exception(sprintf('Could not write archive to file "%s".', $this->file));
	}

	public function add($uid)
	{
		if (!this->contains($uid))
			$this->contents[] = $uid;
	}

	public function remove($uid)
	{
		if (($key = array_search($uid, $this->contents, TRUE) !== FALSE)
			unset($this->contents[$key]);
	}

	public function contains($uid)
	{
		return in_array($uid, $this->contents, TRUE);
	}
}
