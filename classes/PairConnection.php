<?php

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
		while (true)
		{
			$pid = getmypid();
			echo "Running Pair Con $pid \n";
			sleep(2);
		}
	}
}

?>