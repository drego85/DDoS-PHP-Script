# DDoS-PHP-Script

Script to perform a DDoS UDP flood by PHP

## Usage

From web server (visit the page):

`http://127.0.0.1/ddos.php?pass=apple&host=TARGET&port=PORT&time=SECONDS&packet=NUMBER&bytes=NUMBER`

From terminal:

`php ./ddos.php host=TARGET port=PORT time=SECONDS packet=NUMBER bytes=NUMBER`

## Parameters

<pre>help	Print this help summary page
host	REQUIRED specify IP or HOSTNAME
pass	REQUIRED only if used from webserver
port	OPTIONAL if not specified a random ports will be selected
time	OPTIONAL seconds to keep the DDoS alive, required if packet is not used
packet	OPTIONAL number of packets to send to the target, required if time is not used
bytes	OPTIONAL size of the packet to send, defualt: 65000
format	OPTIONAL output format, (text,json,xml), default: text
output	OPTIONAL logfile, save the output to file
verbose	OPTIONAL 0: debug, 1:info, 2:notice, 3:warning, 4:error, default: info

Note: 	If both time and packet are specified, only time will be used
</pre>

## Requirements
- PHP 5.4 version or greater

## To-Do List
- Introduce a logging function to file

## Credits

* [Andrea Draghetti](https://twitter.com/AndreaDraghetti) is the creator of the project

Special thanks:
* [@TheZer0](https://github.com/TheZ3ro) to support for coding;
* [@Smaury](https://github.com/smaury) to support for coding;
* [@moty66](https://github.com/moty66) to support for coding;
* [@AxissXs](https://github.com/AxissXs) to support for coding.

## License

GNU General Public License version 2.0 (GPLv2)


## Disclaimer

This tool is written for educational purpose only, **please** use it on your own good faith.
