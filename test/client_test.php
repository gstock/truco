#!/usr/bin/php –q
<?php

function send($s, $m)
{
	if (!strpos($m,"\n"))
		$m .= "\n";
	socket_write($s, $m);
}

function recv($s)
{
	return trim(socket_read($s, 1024,PHP_NORMAL_READ));
}

$host="127.0.0.1";
$port = 4001;
// open a client connection
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

$timeout = array('sec'=>4,'usec'=>0);
socket_set_option($socket,tcp,SO_RCVTIMEO,$timeout);

socket_connect($socket, $host, $port);

if (!$socket)
{
	echo "Error: could not open socket connection";
}
else
{
	// get the welcome message
	$result = recv($socket);
	echo $result."\n";
	// write the user string to the socket

	//send($socket, "pepito");
	// get the result
	$result = recv($socket);
	// close the connection
	socket_close ($socket);
	
	echo $result."\n";
	
}

?>