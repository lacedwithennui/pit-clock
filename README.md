# About: #
This is a countdown clock for your competition pit. It will count down to FIRST's predicted time of your next match, using data from The Blue Alliance's APIv3. The latest release can be found deployed at [clock.parkerdaletech.com](https://clock.parkerdaletech.com). Notable features of this clock include:
* a list of all of your team's matches for the event in the sidebar
* your full match schedule with predicted times, both alliances & their ranks, and what alliance color you are
* a countdown timer for your next match
* a small panel that shows your team's rank and avg. RP, as well as the current time
* big "next match" panel that shows your bumper color and alliance position for your next match
* the current/latest match being played
* (intentionally) obnoxious visual alarm that flashes your alliance color when it's time to queue
* auto refresh every 2 minutes to account for scheduling/delay changes, as well as to update the next match after your team has played
* automatic system time zone detection for traveling teams
* links so other teams can use it and spread the word!

# How to add credentials: #
Make a new json file called credentials.json that contains the following:
```
{"apiKey": "tba_apiKey"}
```
where tba_apiKey is your APIv3 key from The Blue Alliance.

# How to use the input screen: #
Put in your team number or team key (eg. 5587 or frc5587), and your event key. Your event key is part of the TBA link for your event (eg. 2023vaale). If the event has already started, you can check "Use Latest Event" and the program will find the event key for you. Note that this checkbox will not work if you have qualified for a future event that does not have a match schedule released yet. For example, if you are GaCo 1629, who qualified for the 2023 FIRST Championship by winning the Chairman's Award last year, you must manually put in an event key because your newest event will be the 2023 FIRST Championship.

# How to set up a local instance #
I recommend you install PHP using the [chocolatey tool for Windows](https://chocolatey.org/install), clone this project using Visual Studio Code, and then install the `DEVSENSE PHP Profiler` extension or the `DEVSENSE PHP` extension pack. Note that you will have to restart VSC and any open command line interfaces to use PHP once it's installed. When the editor for `success.php` is open, simply hit F5 or Run>Start Debugging and choose "Launch built-in server and debug."