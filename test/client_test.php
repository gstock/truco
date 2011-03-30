#!/usr/bin/php –q
<?php

include("Connection.php");

$host="127.0.0.1";
$port = 4001;
// open a client connection
$con = new Connection($host,$port);
$con->connect();
$con->setTimeout(5);

if (!$con)
{
	echo "Error: could not open socket connection";
}
else
{
	$con->send("Hola ");
	sleep(2);
	$con->send("Manola.. \n");
	while (true)
	{
		sleep(3);
	}
	
	// get the welcome message
	$result = $con->recv();
	
	echo $result."\n";
	// write the user string to the socket

	$result = $con->recv();
	// close the connection
	$con->close();
	
	echo $result."\n";
	
}

?>