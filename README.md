# DDoS-PHP-Script
Script to perform a DDoS UDP Flood by PHP

## Usage

On a server (visit the page):

`http://127.0.0.1/ddos.php?pass=apple&host=DDoSTarget&port=PORT&time=SECOND&packet=NUM`

From a terminal:

`php ./ddos.php host=DDoSTarget port=PORT time=SECOND packet=NUM`

<br>
- **host** parameter is *always REQUIRED*
- **pass** parameter is *required only on a server*
- If you leave out the **port** parameter, a random port will be selected
- You can use the **time** parameter (how much seconds keep the DDoS alive) 
- You can use the **packet** parameter (how much packets send to the target)
   - If both are used, only ***time*** will be checked
   - If none is used the script will exit with *"Missing Parameters"* Error.

## To-Do List
- Man/Help for CLI users
- Introduce the version number, start from 0.1
- Introduce a logging function, which uses the correct printing method based on the use in cli or web mode
- Other fix...
   
##Credits
[Andrea Draghetti](https://twitter.com/AndreaDraghetti) is the creator of the project, I want thank:
* [@TheZer0](https://github.com/TheZ3ro) to support for coding;
* [@Smaury](https://github.com/smaury) to support for coding;
* [@moty66](https://github.com/moty66) to support for coding.

##License
GNU General Public License version 2.0 (GPLv2)


## Disclaimer

This tool is written on educational purpose, **please** use it on your own good faith.
