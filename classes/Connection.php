<?php

class Connection
{

	var $address;
	var $port;
	var $socket;
	var $read_buffer = "";

	function __construct() 
    { 
        $a = func_get_args(); 
        $i = func_num_args(); 
        if (method_exists($this,$f='__construct'.$i)) { 
            call_user_func_array(array($this,$f),$a); 
        } 
    }

	function __construct1($socket_) {
		$this->socket = $socket_;
	}

	function __construct2($address_, $port_) {
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$this->address = $address_;
		$this->port = $port_;
	}
	
	public function bind() {
		socket_bind($this->socket, $this->address, $this->port);
	}
	
	public function connect() {
		return @socket_connect($this->socket, $this->address, $this->port);
	}
	
	public function listen() {
		socket_listen($this->socket);
	}
	
	public function accept() {
		$client = @socket_accept($this->socket);
		if ($client)
			return new Connection($client);
		return FALSE; 
	}
	
	public function setTimeout($timeout_) {
		$timeout = array('sec'=>$timeout_,'usec'=>0);
		socket_set_option($this->socket,SOL_SOCKET,SO_RCVTIMEO,$timeout);
	}
	
	public function recv()
	{
		$r = socket_read($this->socket, 1024,PHP_BINARY_READ);
		$pos = strpos($r,"\n");
		if ($pos === FALSE)
		{
			$this->read_buffer .= $r;
			return FALSE;
		}
		else
		{
			$line = $this->read_buffer . substr($r,0,$pos+1);
			$this->read_buffer = "";
			return $line;
		}
	}
	
	public function send($m)
	{
		if (!strpos($m,"\n"))
			$m .= "\n";
		@socket_write($this->socket, $m);
	}
	
	public function close() {
		socket_close($this->socket);
	}
}

?>
