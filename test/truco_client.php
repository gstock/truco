#!/usr/bin/php –q
<?php

require dirname(__FILE__) . '/../classes/Connection.php';
require dirname(__FILE__) . '/client/naive.php';

$login_name = $argv[1];
$key = $argv[2];

$login = array('login' => $login_name, 'key' => $key);

$host="192.168.1.111";
$port = 4003;
// open a client connection
$con = new Connection($host,$port);
if (!$con->connect()) {
	echo "Error: could not open socket connection\n";
	die;
}
$con->setTimeout(5);

$client = new NaiveTrucoClient();
$con->send(json_encode($login));
while (1) {
	$recv = $con->recv();
	echo 'Recv: ' , $recv , "\n";
	$data = json_decode($recv, TRUE);
	if ($data == FALSE) {
		echo "Invalid json: " , $recv , "\n";
		break;
	}
	$send = $client->process_message($data);
	if ($send == FALSE) break;
	echo 'Send: ' , json_encode($send) , "\n";
	$con->send(str_replace("\n", '\n', json_encode($send)));
}
$con->close();
