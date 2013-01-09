<?php

require('Core.php');

if (!isset($_GET['feed']))
	Core::generateHttpError(400, 'No feed URL specified. Supply a valid feed URL in the \'feed\' parameter.');

$feed = $_GET['feed'];

$core = new Core($feed);
$core->filter();
