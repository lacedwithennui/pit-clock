var hours;
var minutes;
var seconds;

function convertTime(date) {
    // console.log(Intl.DateTimeFormat().resolvedOptions().timeZone);
    return new Date((typeof date === "date" ? date : new Date(date)).toLocaleString());
}

function getTimeZone() {
    return Intl.DateTimeFormat().resolvedOptions().timeZone;
}

function updateTimer() {
    var countDownDate = new Date(matchTime).getTime();
    var now = new Date().getTime();
    var distance = countDownDate - now;
    if(distance < 0) {
        hours = 0;
        minutes = 0;
        seconds = 0;
    }
    else {
        hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        seconds = Math.floor((distance % (1000 * 60)) / 1000);
    }

    document.getElementById("counter").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
    document.getElementById("currentTime").innerHTML = "Time: " + new Date().toLocaleTimeString();
}

function flicker() {
    if (hours == 0 && minutes <= 17 && (seconds > 55 || (seconds <= 30 && seconds > 25)) && document.body.style.backgroundColor == "white") {
        document.body.style.backgroundColor = window.getComputedStyle(document.getElementById("bumper")).backgroundColor;
    }
    else {
        document.body.style.backgroundColor = "white"
    }
}

// updateTimer();
// setInterval(updateTimer, 1000);
// setInterval(flicker, 500);
// setInterval(function () {
//     location.reload();
// }, 60000);

function openMatchData(match) {
    var x = document.getElementById(match);
    if (x.style.display === "none") {
        x.style.display = "block";
    }
    else {
        x.style.display = "none";
    }
}