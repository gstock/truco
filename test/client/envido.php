<?php
require_once dirname(__FILE__) . '/truco_client.php';
require_once dirname(__FILE__) . '/../../classes/player_hand.php';
require_once dirname(__FILE__) . '/../../classes/game.php';

class EnvidoTrucoClient implements TrucoClient {
	private $cards = array();
	function process_message($message) {
		$is_new_hand = FALSE;
		$has_sing = FALSE;
		foreach ($message as $data) {
			$command = key($data);
			$value = current($data);
			if ($command == 'newhand') {
				$this->cards = $value['cards'];
				$is_new_hand = TRUE;
			}
			if ($command == 'sing') {
				$has_sing = TRUE;
			}
		}

		if ($has_sing) return array('sing' => Game::NO_QUIERO);
		if ($is_new_hand) {
			$my_hand = new PlayerHand($this->cards);
			$my_envido = $my_hand->getEnvido();

			if ($my_envido > 26) return array('sing' => Game::ENVIDO);
		}

		return array('card' => array_pop($this->cards));
	}
}
