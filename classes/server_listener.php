#!/usr/bin/php –q
<?php

include("Connection.php");
include("PairConnection.php");

// Set time limit to indefinite execution 
set_time_limit (0); 

// Set the ip and port we will listen on 
$address = '127.0.0.1'; 
$port = 4001; 

$con = new Connection($address,$port);
$con->bind();
$con->listen();
$pidP = getmypid();


$pairCon = null;

while(true)
{
	echo "Waiting for connections... $pidP\n";	
	$clientCon = $con->accept(); 
	
	if ($clientCon)
	{
		echo "Client connected...\n";
		$clientCon->setTimeout(5);
		
		if(!$pairCon) {
			echo "New paircon...\n";
			$pairCon = new PairConnection(2);
			$pairCon->addClient($clientCon);
		} elseif (!$pairCon->isFull()) {
			$pairCon->addClient($clientCon);
			
			//DO THE FORK
			echo "2 clients, forking ...\n";
			$pid = pcntl_fork();
			
			if ($pid == -1) {
			     die('could not fork');
			} else if ($pid) {
			     // we are the parent
				$pairCon = null;
			} else {
			     $pairCon->run();
			}
			
		} else {
			echo "Error?...\n";
			$pairCon = null;
		}
		
	}
	sleep(1);
}

$con->close();

?>