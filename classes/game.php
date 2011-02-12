<?php
class Game {
	private $hands;
	public $active_player;
	public $played_hands;
	public $started;
	public $winner;

	public $singed;
	public $points;

	static public const ENVIDO = 1;
	static public const REAL_ENVIDO = 2;
	static public const FALTA_ENVIDO = 3;
	static public const TRUCO = 4;
	static public const RETRUCO = 5;
	static public const VALE_CUATRO = 6;
	static public const QUIERO = 7;
	static public const NO_QUIERO = 8;
	static public const ME_VOY_AL_MAZO = 9;

	public function __construct($hands = array(), $start = 0) {
		$this->hands = $hands;
		$this->active_player = $this->started = $start;
		$this->played_hands = array(array(), array());
		$this->singed = array();
		$this->points = array(0,0);
	}

	private function last_song() {
		if (count($this->singed) == 0) return NULL;
		$last = end($this->singed);
		return $last['song'];
	}

	private function count_song($song) {
		$c = 0;
		foreach ($this->singed as $s) if ($s['song'] == $song) ++$c;
		return $c;
	}

	public function get_truco_points() {
		if ($this->count_song(self::VALE_CUATRO)) return 4;
		if ($this->count_song(self::RETRUCO)) return 3;
		if ($this->count_song(self::TRUCO)) return 2;
		return 1;
	}

	public function sing($song) {
		if ($this->has_winner()) return FALSE;
		if ($song == self::ME_VOY_AL_MAZO) {
			$this->winner = (int)(!$this->active_player);
			return TRUE;
		}

		if ($song >= self::QUIERO) {
			if ($this->last_song() >= self::QUIERO) return FALSE;
			if ($this->last_song() >= self::TRUCO) {
				if ($song == self::NO_QUIERO) {
					$this->winner = (int)(!$this->active_player);
					$this->points[$this->winner] += $this->get_truco_points();
					return TRUE;
				} else {
					$this->calculate_active_player();
				}
			} else if ($this->last_song() >= self::ENVIDO) {
				if ($song == self::QUIERO) {
					$this->play_envido();
				} else {
					$this->points[(int)(!$this->active_player)] += 1;
				}
				$this->calculate_active_player();
			} else {
				return FALSE;
			}
		}
	}

	private function play_envido() {
		$envidos = array($this->hands[0]->getEnvido(), $this->hands[1]->getEnvido());
		$winner = NULL;
		if ($envidos[0] == $envidos[1]) {
			$winner = $this->started;
		} else if ($envidos[0] > $envidos[1]) {
			$winner = 0;
		} else {
			$winner = 1;
		}

		$this->points[$winner] += $this->count_song(self::ENVIDO) * 2 + $this->count_song(self::REAL_ENVIDO) * 3 + $this->count_song(self::FALTA_ENVIDO) * 30;
	}

	public function play($card) {
		if ($this->last_song() < self::QUIERO) return FALSE;
		if ($this->has_winner()) return FALSE;
		if ($this->hands[$this->active_player]->remove($card) == FALSE) return FALSE;
		$this->played_hands[$this->active_player][] = $card;
		$this->calculate_active_player();
		return TRUE;
	}

	public function calculate_active_player() {
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

