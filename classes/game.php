<?php
require_once dirname(__FILE__) . '/card.php';

class Game {
	private $hands;
	public $active_player;
	public $played_hands;
	public $started;
	public $winner;

	public $singed;
	public $points;

	const ENVIDO = 1;
	const REAL_ENVIDO = 2;
	const FALTA_ENVIDO = 3;
	const TRUCO = 4;
	const RETRUCO = 5;
	const VALE_CUATRO = 6;
	const QUIERO = 7;
	const NO_QUIERO = 8;
	const ME_VOY_AL_MAZO = 9;

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
		$player = $this->active_player;
		if ($this->do_sing($song)) {
			$this->singed[] = array('player' => $player, 'song' => $song);
			$this->calculate_active_player();
			return TRUE;
		}
		return FALSE;
	}
	public function do_sing($song) {
		if ($this->has_winner()) return FALSE;
		if ($song == self::ME_VOY_AL_MAZO) {
			$this->winner = (int)(!$this->active_player);
			$this->points[$this->winner] += $this->get_truco_points();
			return TRUE;
		}

		if ($song >= self::QUIERO) {
			if ($this->last_song() >= self::QUIERO) return FALSE;
			if ($this->last_song() >= self::TRUCO) {
				if ($song == self::NO_QUIERO) {
					$this->winner = (int)(!$this->active_player);
					$this->points[$this->winner] += $this->get_truco_points() - 1;
				}
			} else if ($this->last_song() >= self::ENVIDO) {
				if ($song == self::QUIERO) {
					$winner = $this->play_envido();
					if ($this->count_song(self::FALTA_ENVIDO) > 0) {
						$this->winner = $winner;
					}
				} else {
					$this->points[(int)(!$this->active_player)] += $this->count_song(self::ENVIDO) + $this->count_song(self::REAL_ENVIDO) + $this->count_song(self::FALTA_ENVIDO);
				}
			} else {
				return FALSE;
			}
			return TRUE;
		} else if ($song == self::TRUCO) {
			if ($this->last_song() === NULL || $this->last_song >= self::QUIERO) return TRUE;
			return FALSE;
		} else if ($song > self::TRUCO) {
			if ($this->count_song($song) > 0) return FALSE;
			foreach ($this->singed as $s) {
				if ($s['song'] == $song-1) return $s['player'] != $this->active_player;
			}
			return FALSE;
		} else if ($song >= self::ENVIDO) {
			if ($this->count_song(self::QUIERO) > 0 || $this->count_song(self::NO_QUIERO) > 0) return FALSE;
			if ($song == self::ENVIDO && ($this->count_song(self::REAL_ENVIDO) > 0 || $this->count_song(self::FALTA_ENVIDO) > 0)) return FALSE;
			if ($song == self::ENVIDO && $this->count_song(self::ENVIDO) > 1) return FALSE;
			if ($song == self::REAL_ENVIDO && $this->count_song(self::FALTA_ENVIDO) > 0) return FALSE;
			if ($song > self::ENVIDO && $this->count_song($song) > 0) return FALSE;
			return TRUE;
		}
		return FALSE;
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
		return $winner;
	}

	public function play($card) {
		if ($this->last_song() !== NULL && $this->last_song() < self::QUIERO) return FALSE;
		if ($this->has_winner()) return FALSE;
		if ($this->hands[$this->active_player]->remove($card) == FALSE) return FALSE;
		$this->played_hands[$this->active_player][] = $card;
		$this->calculate_active_player();
		return TRUE;
	}

	public function calculate_active_player() {
		if ($this->has_winner()) {
			$this->active_player = NULL;
		} else {
			if ($this->last_song() !== NULL && $this->last_song() < self::QUIERO) {
				$last_singed = end($this->singed);
				$this->active_player = (int)(!$last_singed['player']);
			} else if (count($this->played_hands[0]) != count($this->played_hands[1])) {
				$this->active_player = count($this->played_hands[0]) < count($this->played_hands[1]) ? 0 : 1;
			} else if (count($this->played_hands[0]) * count($this->played_hands[1]) == 0) {
				if (count($this->played_hands[0]) + count($this->played_hands[1]) == 0)
					$this->active_player = $this->started;
				else
					$this->active_player = (int)(!$this->started);
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
				$this->points[$this->winner] += $this->get_truco_points();
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
			$this->points[$this->winner] += $this->get_truco_points();
			return TRUE;
		}

		$third = Card::higher($this->played_hands[0][2], $this->played_hands[1][2]);
		if ($third == 0)
			if ($first != 0) $this->winner = $first > 0 ? 1 : 0;
			else $this->winner = $this->started;
		else
			$this->winner = $third > 0 ? 1 : 0;
		$this->points[$this->winner] += $this->get_truco_points();
		return TRUE;
	}
}

