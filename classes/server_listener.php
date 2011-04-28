#!/usr/bin/php –q
<?php

include("Connection.php");
include("PairConnection.php");

$db = NULL;

function setupDB() {
    global $db;
    
    
    if ($db = new SQLiteDatabase('truko.sqlite')) { 
        $row = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
        if (count($row) == 0)
        {
            $db->query("create table users(id integer primary key,
                                           name varchar(255),
                                           email varchar(255),
                                           login varchar(255),
                                           api_key varchar(255));");
            
            $db->query("create table match(id integer primary key,
                                           user_id_1 integer,
                                           user_id_2 integer,
                                           winner integer);");                               
        
            $db->query("insert into users(name, email, login, api_key) values ('Gaby','','gaby','aaa');
                        insert into users(name, email, login, api_key) values ('Cris','','cris','bbb');
                        insert into users(name, email, login, api_key) values ('Seppo','','seppo','ccc');
                        insert into users(name, email, login, api_key) values ('Lucia','','lucia','ddd');
                        insert into match(user_id_1,user_id_2) values(1,2);
                        insert into match(user_id_1,user_id_2) values(3,4);
            ");
        
            $rows = $db->query("select * from users;");
            
            foreach($rows as $row)
            {
                //var_dump($row);
            }
        
        }
    } else {
        die('NO DB');
    }
}

// Set time limit to indefinite execution 
set_time_limit (0); 

setupDB();


// Set the ip and port we will listen on 
$address = '192.168.1.111'; 
$port = 4003; 

$con = new Connection($address,$port);
$con->bind();
$con->listen();
$pidP = getmypid();


$pairCon = null;

while(true)
{
	echo "Waiting for connections... $pidP\n";	
	$clientCon = $con->accept(); 
	
	if ($clientCon)
	{
		echo "Client connected...\n";
		$clientCon->setTimeout(5);
		
		$login = json_decode($clientCon->recv(),true);
		
		$rows = $db->query("Select * from users where login = '{$login['login']}' and api_key = '{$login['key']}'");
		var_dump($rows);
		if ($rows && count($rows) == 1)
		{
		    if(!$pairCon) {
    			echo "New paircon... {$login['login']}\n";
    			$pairCon = new PairConnection(2);
    			$pairCon->addClient($clientCon);
    		} elseif (!$pairCon->isFull()) {
    			$pairCon->addClient($clientCon);
			
    			//DO THE FORK
    			echo "2 clients, forking ... {$login['login']}\n";
    			$pid = pcntl_fork();
			
    			if ($pid == -1) {
    			     die('could not fork');
    			} else if ($pid) {
    			     // we are the parent
    				$pairCon = null;
    			} else {
    			     $pairCon->run();
    			     die();
    			}
			
    		} else {
    			echo "Error?...\n";
    			echo "Max: ".$pairCon->max_clients."\n";
    			echo "Count: " . count($pairCon->clients) . "\n";
    			$pairCon = null;
    		}
		}
		else
		{
		    echo "Invalid user: {$login['login']}";
		}
	}
	sleep(1);
}

$con->close();

?>