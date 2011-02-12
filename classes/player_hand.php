<?php
class PlayerHand {
	public $cards;

	public function __construct($cards = array()) {
		$this->cards = is_array($cards) ? $cards : func_get_args();
	}

	public function getEnvido() {
		$cards_by_suit = array();
		foreach ($this->cards as $c) $cards_by_suit[$c[1]][] = (int)$c[0];
		if (count($cards_by_suit) == 3) {
			$max = 0;
			foreach ($cards_by_suit as $c) {
				$max = max($max, $c[0]);
			}
			return $max;
		} else if (count($cards_by_suit) == 2) {
			foreach ($cards_by_suit as $c) {
				if (count($c) == 2) return 20 + $c[0] + $c[1];
			}
		} else if (count($cards_by_suit) == 1) {
			$c = current($cards_by_suit);
			return 20 + $c[0] + $c[1] + $c[2] - min($c);
		}
	}

	public function remove($card) {
		$pos = array_search($card, $this->cards);
		if ($pos === FALSE) return FALSE;
		unset($this->cards[$pos]);
		return TRUE;
	}
}
