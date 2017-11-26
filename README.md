# bristle
A simple web gui for [snort](https://www.snort.org/). Snort is an Intrusion Detection System and alarms when dangerous activity is happening in your network.

## Features
 - List events and view their payload and ports
 - View activity over different time periods in charts
 - Top 5 statistics

## TODO list
The project is in an early state and these features are planned:
* More info about IP/TCP/UDP/ICMP packets in extended view of events
* Bunch together duplicate events
* Filter options for events
* Make "most common/frequent" follow time periods in dashboard
* Connect to snort knowledge database
* Option to limit access with login
* Link the source IP to open AbuseIPDB or other security website

## Getting started
Prerequisities: [snort](https://www.snort.org/), [barnyard2](https://github.com/firnsy/barnyard2), php, mysql, any webserver
 1. Clone the repository and copy  all files except git files to your www directory.
 2. Rename conf.php.example to conf.php
 3. Change the content in conf.php in accordance to your database setup. Bristle is using the same database as barnyard2.

## Contributing
Don't hesitate asking questions about install or other problems. I will also gladly receive requests about new functionality.
