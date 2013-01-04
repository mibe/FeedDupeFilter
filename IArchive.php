<?php

interface IArchive
{
	public function add($uid);
	public function remove($uid);
	public function contains($uid);
	public function clear();
}
