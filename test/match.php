<?php
require dirname(__FILE__) . '/../classes/match.php';

$match = new Match();
$cards = array(0 => array(), 1 => array());
while (($player = $match->get_active_player()) !== NULL) {
	// server logic
	echo 'Player ' , $player , "\n";
	$stack = $match->get_stack_for_player($player);
	echo json_encode($stack);

	// client logic
	foreach ($stack as $data) {
		$command = key($data);
		$value = current($data);
		if ($command == 'newhand') {
			$cards[$player] = $value['cards'];
		}
	}
	$command = array('card' => array_pop($cards[$player]));
	echo "\n" , json_encode($command) , "\n";

	// server logic
	$match->process($command);
}

// server logic
foreach (array(0,1) as $player) {
	echo 'Player ' , $player , "\n";
	$stack = $match->get_stack_for_player($player);
	echo json_encode($stack) , "\n";
}
