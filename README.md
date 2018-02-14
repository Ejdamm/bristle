# bristle
A simple web gui for [snort](https://www.snort.org/). Snort is an Intrusion Detection System and alarms when dangerous activity is happening in your network.

## Features
 - List events and view their protocol headers and signature info
 - View activity over different time periods in charts
 - Top 5 statistics
 - Direct link from events to AbuseIPDB and Snort rule docs

## TODO list and known issues
The project is in an early state and these features are planned:
* Bunch together duplicate events
* More filter options for events
* Option to limit access with login
* Some styling issues in event page for smaller screens
* Decrease the need of reloading the hole page
* Display IPv6 addresses correctly
* Collapse an event view when an expanded event is clicked again

## Getting started
Prerequisities: [snort](https://www.snort.org/), [barnyard2](https://github.com/firnsy/barnyard2), php, mysql, and any webserver will do.  
[snort.org](https://www.snort.org/documents) have a lot of useful documentation on how to install Snort (and sometimes the other prerequisities too!) on different systems. Bristle have been testet for Snort 2.9.9.x
 1. Clone the repository and copy  all files except git files to your www directory.
 2. Rename conf.php.example to conf.php
 3. Change the content in conf.php in accordance to your database setup. Bristle is using the same database as barnyard2.

## Demo
[Sergio](https://github.com/sergioMITM) have been kind enough to host a live Demo of bristle for a snort instance running against a free internet proxy. https://sergiomitm.com/bristle/index.php

## Contributing
Don't hesitate asking questions about install or other problems. I will also gladly receive requests about new functionality.
