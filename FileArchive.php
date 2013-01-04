<?php

class FileArchive implements IArchive
{
	private $file;
	private $contents;

	function __construct($archiveIdentifier)
	{
		$this->buildFilename($archiveIdentifier);

		$this->load();
	}

	function __destruct()
	{
		$this->save();
	}

	private function buildFilename($id)
	{
		// Replace all characters which are not digits and latin chars with a dash
		$this->file = preg_replace("/[^a-zA-Z0-9]/", '-', $id);

		// Chop the filename if it's longer than 200 chars.
		if (strlen($this->file) > 200)
			$this->file = substr($this->file, 0, 200);
	}

	private function load()
	{
		// Only unserialize if the data file exists.
		// If not, start with an empty archive.
		if (file_exists($this->file))
		{
			$data = file_get_contents($this->file);
			$this->contents = unserialize($data);
		}
		else
		{
			$this->clear();
			$this->save();
		}

		// Get realpath, because otherwise with a relative filepath the
		// file would be written to SERVER_ROOT in the destructor.
		$this->file = realpath($this->file);
	}

	private function save()
	{
		$data = serialize($this->contents);
		$bytes = file_put_contents($this->file, $data);

		if ($bytes === FALSE)
			throw new Exception(sprintf('Could not write archive to file "%s".', $this->file));
	}

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
