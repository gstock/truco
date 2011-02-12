<?php
class Card {
	static private $ordering = array(
		'1E', '1B', '7E', '7O', '3.', '2.', '1.', 'K.', 'Q.', 'J.', '7.', '6.', '5.', '4.'
	); // cards will be evaluated in order

	static public function higher($card1, $card2) {
		$v1 = $v2 = NULL;
		foreach (self::$ordering as $k => $c) {
			if ($v1 === NULL && preg_match('/^' . $c . '$/', $card1)) $v1 = $k;
			if ($v2 === NULL && preg_match('/^' . $c . '$/', $card2)) $v2 = $k;
			if ($v1 !== NULL && $v2 !== NULL) break;
		}
		if ($v1 === NULL) throw new Exception('Unknown card \'' . $card1 . '\'');
		if ($v2 === NULL) throw new Exception('Unknown card \'' . $card2 . '\'');
		if ($v1 == $v2) return 0;
		else if ($v1 < $v2) return -1;
		else return 1;
	}
}

