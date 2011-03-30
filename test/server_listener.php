#!/usr/bin/php –q
<?php

$read_buffer = "";

function send($s, $m)
{
	if (!strpos($m,"\n"))
		$m .= "\n";
	socket_write($s, $m);
}

function recv($s)
{
	global $read_buffer;
	$r = socket_read($s, 1024,PHP_BINARY_READ);
	$pos = strpos($r,"\n");
	if ($pos === FALSE)
	{
		$read_buffer .= $r;
		return FALSE;
	}
	else
	{
		$line = $read_buffer . substr($r,0,$pos+1);
		$read_buffer = "";
		return $line;
	}
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
//socket_set_nonblock($sock);

// Bind the socket to an address/port 
socket_bind($sock, $address, $port) or die('Could not bind to address'); 
// Start listening for connections 
socket_listen($sock); 


echo "Waiting for connections...\n";

$start_port = 5000;

while(true)
{
	
	$client = @socket_accept($sock); 
	echo "Client connected...\n";
	if ($client)
	{
		$timeout = array('sec'=>5,'usec'=>0);
		socket_set_option($client,SOL_SOCKET,SO_RCVTIMEO,$timeout);
		//socket_set_nonblock($client);
		//send($client, "[{'messageId' : 1, 'messageText' : Welcome to Truko Server}]");

		// Read the input from the client &#8211; 1024 bytes 
		echo "Waiting for data...\n";
		$input = recv($client);
		if (!$input)
		{
			echo "No data!... ( $read_buffer )\n";
		}
		else
		{
			echo "Data received...\n";
		}
		
		$input = recv($client);
		if (!$input)
		{
			echo "No data!... ( $read_buffer )\n";
		}
		else
		{
			echo "Data received...\n";
		}
	
		echo "Client $input connected\n";
	
		echo "Assigning port: $start_port\n";
		send($client, "[{'port' : $start_port}]");
		socket_close($client);
	
		$start_port++;
	}
	sleep(1);
}

socket_close($sock);

?>