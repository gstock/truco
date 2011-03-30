#!/usr/bin/php –q
<?php

require dirname(__FILE__) . '/../classes/Connection.php';
require dirname(__FILE__) . '/client/naive.php';

$host="127.0.0.1";
$port = 4001;
// open a client connection
$con = new Connection($host,$port);
if (!$con->connect()) {
	echo "Error: could not open socket connection\n";
	die;
}
$con->setTimeout(5);

$client = new NaiveTrucoClient();
while (1) {
	$recv = $con->recv();
	$data = json_decode($recv);
	if ($data == FALSE) {
		echo "Invalid json: " , $recv , "\n";
		break;
	}
	$send = $client->process_message($recv);
	if ($send == FALSE) break;
	$con->send(str_replace("\n", '\n', json_encode($send)));
}
$con->close();
