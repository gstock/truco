<?php
require_once dirname(__FILE__) . '/truco_client.php';

class PersonTrucoClient implements TrucoClient {
	public $cards;

	function process_message($message) {
		foreach ($message as $data) {
			$command = key($data);
			$value = current($data);
			if ($command == 'newhand') {
				$this->cards = $value['cards'];
			}
			if ($command == 'sing') {
				$has_sing = TRUE;
			}
		}

		echo json_encode($message) , "\n";
		echo '1: ' . $this->cards[0] , ' ';
		echo '2: ' . $this->cards[1] , ' ';
		echo '3: ' . $this->cards[2] , ' ';
		echo '4: Envido ';
		echo '5: Real Envido ';
		echo '6: Falta Envido ';
		echo '7: Truco ';
		echo '8: Retruco ';
		echo '9: Quiero Vale Cuatro ';
		echo '10: Quiero ';
		echo '11: No Quiero ';
		echo '12: Me Voy Al Mazo ';
		echo "\n";

		do {
			$command = (int)fgets(STDIN);
		} while($command <= 0);

		if ($command < 4) {
			return array('card' => $this->cards[$command-1]);
		}
		return array('sing' => $command-3);
	}
}
