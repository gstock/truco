<?php
class Game {
	private $hands;
	public $active_player;
	public $played_hands;
	public $started;
	public $winner;

	public function __construct($hands = array(), $start = 0) {
		$this->hands = $hands;
		$this->active_player = $this->started = $start;
		$this->played_hands = array(array(), array());
	}

	public function play($card) {
		if ($this->has_winner()) return FALSE;
		if ($this->hands[$this->active_player]->remove($card) == FALSE) return FALSE;
		$this->played_hands[$this->active_player][] = $card;
		if (count($this->played_hands[0]) != count($this->played_hands[1])) {
			$this->active_player = (int)(!$this->active_player);
		} else {
			if ($this->has_winner()) {
				$this->active_player = NULL;
			} else {
				$higher = Card::higher($this->played_hands[0][count($this->played_hands[0]) - 1], $this->played_hands[1][count($this->played_hands[0]) - 1]);
				if ($higher < 0) $this->active_player = 0;
				else if ($higher > 0) $this->active_player = 1;
				else $this->active_player = $this->started;
			}
		}
		return TRUE;
	}

	public function has_winner() {
		if ($this->winner !== NULL) return TRUE;
		$c = count($this->played_hands[0]);
		if ($c * count($this->played_hands[1]) == 6) {
			if ($this->played_hands[$c == 3 ? 0 : 1][2] == '1E') {
				$this->winner = $c == 3 ? 0 : 1;
				return TRUE;
			}
		}
		if ($c != count($this->played_hands[1])) return FALSE;
		if ($c <= 1) return FALSE;

		$first = Card::higher($this->played_hands[0][0], $this->played_hands[1][0]);
		$second = Card::higher($this->played_hands[0][1], $this->played_hands[1][1]);
		if ($c == 2) {
			if ($first * $second == -1) return FALSE;
			if ($first == 0 && $second == 0) return FALSE;
			$this->winner = $first + $second > 0 ? 1 : 0;
			return TRUE;
		}

		$third = Card::higher($this->played_hands[0][2], $this->played_hands[1][2]);
		if ($third == 0)
			if ($first != 0) $this->winner = $first > 0 ? 1 : 0;
			else $this->winner = $this->started;
		else
			$this->winner = $third > 0 ? 1 : 0;
		return TRUE;
	}
}

