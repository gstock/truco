<?php
require_once dirname(__FILE__) . '/../classes/player_hand.php';
require_once dirname(__FILE__) . '/../classes/card.php';
require_once dirname(__FILE__) . '/../classes/game.php';

$hand = new PlayerHand(array('JE','QB','KO'));
assert($hand->getEnvido() == 0);
$hand = new PlayerHand(array('JE','QB','KE'));
assert($hand->getEnvido() == 20);
$hand = new PlayerHand(array('7E','QB','KE'));
assert($hand->getEnvido() == 27);
$hand = new PlayerHand(array('7C','QB','KE'));
assert($hand->getEnvido() == 7);
$hand = new PlayerHand(array('7C','5C','KE'));
assert($hand->getEnvido() == 32);

try {
	Card::higher('9C', '1E');
	assert(FALSE);
} catch (Exception $e) {
}

assert(Card::higher('1E', '1B') == -1);
assert(Card::higher('7O', '7E') == 1);
assert(Card::higher('3E', '3B') == 0);
assert(Card::higher('2C', '1C') == -1);
assert(Card::higher('2C', '3C') == 1);
assert(Card::higher('7C', '7B') == 0);

$game = new Game(array(new PlayerHand('1E', '1B', '4E'), new PlayerHand('7O', '5E', '4C')), 0);
assert($game->play('4E') == TRUE);
assert($game->play('5E') == TRUE);
assert($game->play('4C') == TRUE);
assert($game->play('1B') == TRUE);
assert($game->has_winner() == FALSE);
assert($game->play('1E') == TRUE);
assert($game->has_winner() && $game->winner == 0);

$game = new Game(array(new PlayerHand('1E', '1B', '4E'), new PlayerHand('7O', '5E', '4C')), 0);
assert($game->play('1E') == TRUE);
assert($game->play('5E') == TRUE);
assert($game->play('1B') == TRUE);
assert($game->play('4C') == TRUE);
assert($game->has_winner() && $game->winner == 0);

$game = new Game(array(new PlayerHand('1E', '4C', '4E'), new PlayerHand('7O', '5E', '1B')), 1);
assert($game->play('5E') == TRUE);
assert($game->play('1E') == TRUE);
assert($game->play('4C') == TRUE);
assert($game->play('1B') == TRUE);
assert($game->play('7O') == TRUE);
assert($game->play('4E') == TRUE);
assert($game->has_winner() && $game->winner == 1);

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '5E')), 0);
assert($game->play('3E') == TRUE);
assert($game->play('3O') == TRUE);
assert($game->play('3C') == TRUE);
assert($game->play('3B') == TRUE);
assert($game->play('4E') == TRUE);
assert($game->play('5E') == TRUE);
assert($game->has_winner() && $game->winner == 1);

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '5E')), 0);
assert($game->play('3E') == TRUE);
assert($game->play('3O') == TRUE);
assert($game->play('4E') == TRUE);
assert($game->play('5E') == TRUE);
assert($game->has_winner() && $game->winner == 1);

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '5E')), 1);
assert($game->play('3O') == TRUE);
assert($game->play('3E') == TRUE);
assert($game->play('5E') == TRUE);
assert($game->play('4E') == TRUE);
assert($game->has_winner() && $game->winner == 1);

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '5E')), 0);
assert($game->play('4E') == TRUE);
assert($game->play('5E') == TRUE);
assert($game->play('3O') == TRUE);
assert($game->play('3E') == TRUE);
assert($game->has_winner() && $game->winner == 1);

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 0);
assert($game->play('4E') == TRUE);
assert($game->play('4C') == TRUE);
assert($game->play('3E') == TRUE);
assert($game->play('3O') == TRUE);
assert($game->play('3C') == TRUE);
assert($game->play('3B') == TRUE);
assert($game->has_winner() && $game->winner == 0);

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 1);
assert($game->play('4C') == TRUE);
assert($game->play('4E') == TRUE);
assert($game->play('3O') == TRUE);
assert($game->play('3E') == TRUE);
assert($game->play('3B') == TRUE);
assert($game->play('3C') == TRUE);
assert($game->has_winner() && $game->winner == 1);

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 1);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::NO_QUIERO) == TRUE);
assert($game->has_winner() == FALSE);
assert($game->points == array(0,1));

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 1);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::QUIERO) == TRUE);
assert($game->has_winner() == FALSE);
assert($game->points == array(2,0));

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 1);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::ENVIDO) == FALSE);
assert($game->sing(Game::QUIERO) == TRUE);
assert($game->has_winner() == FALSE);
assert($game->points == array(4,0));

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 1);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::REAL_ENVIDO) == TRUE);
assert($game->sing(Game::QUIERO) == TRUE);
assert($game->has_winner() == FALSE);
assert($game->points == array(7,0));

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 1);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::NO_QUIERO) == TRUE);
assert($game->has_winner() == FALSE);
assert($game->points == array(2,0));

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 1);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::REAL_ENVIDO) == TRUE);
assert($game->sing(Game::NO_QUIERO) == TRUE);
assert($game->has_winner() == FALSE);
assert($game->points == array(0,3));

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 0);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::REAL_ENVIDO) == TRUE);
assert($game->sing(Game::FALTA_ENVIDO) == TRUE);
assert($game->sing(Game::NO_QUIERO) == TRUE);
assert($game->has_winner() == FALSE);
assert($game->points == array(0,4));

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 0);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::REAL_ENVIDO) == TRUE);
assert($game->sing(Game::FALTA_ENVIDO) == TRUE);
assert($game->sing(Game::QUIERO) == TRUE);
assert($game->has_winner() == TRUE);
assert($game->points[0] >= 30);
assert($game->points[1] < $game->points[0]);

$game = new Game(array(new PlayerHand('3E', '3C', '4B'), new PlayerHand('3O', '3B', '4C')), 0);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::QUIERO) == TRUE);
assert($game->has_winner() == FALSE);
assert($game->points == array(2,0));

$game = new Game(array(new PlayerHand('3E', '3C', '4B'), new PlayerHand('3O', '3B', '4C')), 1);
assert($game->sing(Game::ENVIDO) == TRUE);
assert($game->sing(Game::QUIERO) == TRUE);
assert($game->has_winner() == FALSE);
assert($game->points == array(0,2));

$game = new Game(array(new PlayerHand('3E', '3C', '4B'), new PlayerHand('3O', '3B', '4C')), 1);
assert($game->sing(Game::TRUCO) == TRUE);
assert($game->sing(Game::NO_QUIERO) == TRUE);
assert($game->has_winner() == TRUE);
assert($game->points == array(0,1));

$game = new Game(array(new PlayerHand('3E', '3C', '4E'), new PlayerHand('3O', '3B', '4C')), 1);
assert($game->play('4C') == TRUE);
assert($game->play('4E') == TRUE);
assert($game->play('3O') == TRUE);
assert($game->play('3E') == TRUE);
assert($game->sing(Game::TRUCO) == TRUE);
assert($game->play('3C') == FALSE);
assert($game->sing(Game::QUIERO) == TRUE);
assert($game->play('3B') == TRUE);
assert($game->play('3C') == TRUE);
assert($game->has_winner() && $game->winner == 1);
assert($game->points==array(0,2));

