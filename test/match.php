<?php
require dirname(__FILE__) . '/../classes/match.php';
require dirname(__FILE__) . '/client/naive.php';
require dirname(__FILE__) . '/client/envido.php';
require dirname(__FILE__) . '/client/person.php';

$match = new Match();
$cards = array(0 => array(), 1 => array());
$clients = array(new PersonTrucoClient(), new NaiveTrucoClient());
$show_log = FALSE;
while (($player = $match->get_active_player()) !== NULL) {
	$stack = $match->get_stack_for_player($player);
	if ($show_log)
		echo "S->$player: " , json_encode($stack);

	$command = $clients[$player]->process_message($stack);
	if ($show_log)
		echo "\n" , $player , '->S' , json_encode($command) , "\n";

	$match->process($command);
}

foreach (array(0,1) as $player) {
	$stack = $match->get_stack_for_player($player);
	if ($show_log)
		echo "S->$player: " , json_encode($stack) , "\n";
}
