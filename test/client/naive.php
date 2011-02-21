<?php
require_once dirname(__FILE__) . '/truco_client.php';
require_once dirname(__FILE__) . '/../../classes/game.php';

class NaiveTrucoClient implements TrucoClient {
	private $cards = array();
	function process_message($message) {
		$has_sing = FALSE;
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
		if ($has_sing) return array('sing' => Game::NO_QUIERO);

		return array('card' => array_pop($this->cards));
	}
}
