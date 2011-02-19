<?php
require_once dirname(__FILE__) . '/game.php';
require_once dirname(__FILE__) . '/player_hand.php';
class Match {
	public $match_id;
	public $points = array('0' => 0, '1' => 0);
	public $current_game;

	static private $cards;

	static private function get_cards() {
		if (self::$cards === NULL) {
			for ($a = 1; $a <= 10; ++$a) {
				$card = $a;
				if ($card == 8) $card = 'J';
				if ($card == 9) $card = 'Q';
				if ($card == 10) $card = 'K';
				foreach (array('O','C','E','B') as $b)
					self::$cards[] = $card . $b;
			}
		}
		shuffle(self::$cards);
		return self::$cards;
	}

	public function __construct() {
		$this->match_id = md5(microtime());
	}

	public function create_game() {
		if ($this->current_game !== NULL && $this->current_game->has_winner() == FALSE) return NULL;
		if ($this->current_game !== NULL) {
			$this->points[0] += $this->current_game->point[0];
			$this->points[1] += $this->current_game->point[1];
			$started = $this->current_game->started;
		} else {
			$started = 1;
		}
		if (max($this->points) >= 30) return NULL;
		$cards = self::get_cards();
		$this->current_game = new Game(array(new PlayerHand(array_slice($cards,0,3)), new PlayerHand(array_slice($cards,3,3))), (int)(!$started));
		return $this->current_game;
	}

	public function get_active_player() {
		$this->create_game();
		if ($this->current_game === NULL) return NULL;
		return $this->current_game->active_player;
	}

	public function card($card) {
		return $this->current_game->play($card);
	}

	public function sing($song) {
		return $this->current_game->sing($song);
	}

	public function info($info) {
		if ($info == 'hand_points')
			return $this->current_game->points;
		return NULL;
	}
}
