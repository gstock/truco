<?php
include("Match.php");

class PairConnection 
{
	var $clients = array();
	var $max_clients;
	
	function __construct($max_clients_)
	{
		$this->max_clients = $max_clients_;
	}
	
	public function isFull()
	{
		return $this->max_clients == count($this->clients);
	}
	
	public function addClient($client_)
	{
		$this->clients[] = $client_;
	}
	
	public function run()
	{
		$match = new Match();
		$show_log = TRUE;
		while (($player = $match->get_active_player()) !== NULL) {
			$stack = $match->get_stack_for_player($player);
			if ($show_log)
				echo "S->$player: " , json_encode($stack);

			$this->clients[$player]->send(json_encode($stack));
			$command = json_decode($this->clients[$player]->recv(),true);
			if ($show_log)
				echo "\n" , $player , '->S' , json_encode($command) , "\n";

			$match->process($command);
		}

		foreach (array(0,1) as $player) {
			$stack = $match->get_stack_for_player($player);
			$this->clients[$player]->send(json_encode($stack));
			if ($show_log)
				echo "S->$player: " , json_encode($stack) , "\n";
		}
		
	}
}

?>