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

// Set time limit to indefinite execution 
set_time_limit (0); 

// Set the ip and port we will listen on 
$address = '127.0.0.1'; 
$port = 4001; 
$max_clients = 10; 

// Array that will hold client information 
$clients = Array(); 

// Create a TCP Stream socket 
$sock = socket_create(AF_INET, SOCK_STREAM, 0);


// Bind the socket to an address/port 
socket_bind($sock, $address, $port) or die('Could not bind to address'); 
// Start listening for connections 
socket_listen($sock); 


$timeout = array('sec'=>1,'usec'=>0);
socket_set_option($sock,SOL_SOCKET,SO_RCVTIMEO,$timeout);

echo "Waiting for connections...\n";

$start_port = 5000;

while(true)
{
	
	$client = socket_accept($sock); 

	//send($client, "[{'messageId' : 1, 'messageText' : Welcome to Truko Server}]");

	// Read the input from the client &#8211; 1024 bytes 
	$input = recv($client);
	
	echo "Client $input connected\n";
	
	echo "Assigning port: $start_port\n";
	send($client, "[{'port' : $start_port}]");
	socket_close($client);
	
	$start_port++;
	
}

socket_close($sock);

?>