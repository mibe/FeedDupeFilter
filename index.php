<?php

function fdf_generateHttpError($errorCode, $errorMessage = '')
{
	if (!is_numeric($errorCode) || $errorCode < 100 || $errorCode > 599)
		throw new InvalidArgumentException('errorCode is not an valid HTTP status code.');

	switch($errorCode)
	{
		case 400: $errorCode .= ' Bad Request';
			break;
		case 500: $errorCode .= ' Server Error';
			break;
		case 501: $errorCode .= ' Not Implemented';
			break;
	}

	header('HTTP/1.1 ' . $errorCode);
	exit($errorMessage);
}

// Check if the feed URL was specified by the caller.
if (!isset($_GET['feed']))
	fdf_generateHttpError(400, 'No feed URL specified. Supply a valid feed URL in the \'feed\' parameter.');

$feed = $_GET['feed'];

require('src/Core.php');

try
{
	$core = new Core($feed);
	$core->filter();
}
catch (Exception $ex)
{
	$code = $ex->getCode();
	$message = $ex->getMessage();

	if ($code == 0)
		$code = 400;

	fdf_generateHttpError($code, $message);
}
