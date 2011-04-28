<?php
require_once dirname(__FILE__) . '/game.php';
require_once dirname(__FILE__) . '/player_hand.php';
class Match {
	public $match_id;
	public $points = array('0' => 0, '1' => 0);
	public $current_game;
	private $winner;

	private $command_stacks = array(0 => array(), 1 => array());

	private static $published_commands = array('card', 'sing', 'info');

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
		foreach (array(0, 1) as $player) {
			$this->command_stacks[$player][] = array('start' => array('clientId' => $player, 'gameId' => $this->match_id));
		}
	}

	public function get_stack_for_player($player) {
		$ret = $this->command_stacks[$player];
		$this->command_stacks[$player] = array();
		return $ret;
	}

	public function create_game() {
		if ($this->current_game !== NULL && $this->current_game->has_winner() == FALSE) return NULL;
		if ($this->current_game !== NULL) {
			$this->points[0] += $this->current_game->points[0];
			$this->points[1] += $this->current_game->points[1];
			$started = $this->current_game->started;
		} else {
			$started = 1;
		}
		if (max($this->points) >= 30) {
			if ($this->points[0] != $this->points[1]) {
				$this->winner = (int)(max($this->points) == $this->points[1]);
				foreach (array(0, 1) as $player)
					$this->command_stacks[$player][] = array('finished' => array('winner' => $this->winner));
				return NULL;
			}
		}
		$cards = self::get_cards();
		$hands = array(
			array_slice($cards,0,3),
			array_slice($cards,3,3)
		);
		shuffle(self::$cards); // delete the evidence; unnecesary safe measure
		$this->current_game = new Game(array(new PlayerHand($hands[0]), new PlayerHand($hands[1])), (int)(!$started));
		foreach (array(0, 1) as $player) {
			$this->command_stacks[$player][] = array('newhand' => array('cards' => $hands[$player], 'points' => $this->points));
		}
		return $this->current_game;
	}

	public function get_active_player() {
		$this->create_game();
		if ($this->current_game === NULL) return NULL;
		return $this->current_game->active_player;
	}

	public function card($card) {
		$return = $this->current_game->play($card);
		if ($return) {
			foreach ($this->command_stacks as $k => $v) {
				$v[] = array('card' => $card);
				$this->command_stacks[$k] = $v;
			}
		}
		return $return;
	}

	public function sing($song) {
		$return = $this->current_game->sing($song);
		if ($return) {
			foreach ($this->command_stacks as $k => $v) {
				$v[] = array('sing' => $song);
				$this->command_stacks[$k] = $v;
			}
		}
		return $return;
	}

	public function info($info) {
		if ($info == 'hand_points')
			return $this->current_game->points;
		return NULL;
	}

	public function process($function) {
		if (!is_array($function)) die('NOT ARRAY');
		$method = key($function);
		if (in_array($method, self::$published_commands)) {
			$this->$method(current($function));
		}
	}
}
