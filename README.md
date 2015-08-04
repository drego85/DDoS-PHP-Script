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
- Prevent XSS with htmlspecialchars()
- Man/Help for CLI users
- Wiki/FAQ Section
- Introduce the version number, start from 0.1
- Other fix...
   
## Disclaimer

This tool is written on educational purpose, **please** use it on your own good faith.
