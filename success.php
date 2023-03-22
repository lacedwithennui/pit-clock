<!DOCTYPE html>
<html>

<head>
    <title>Match Countdown</title>
    <link rel="stylesheet" href="dashstyles.css">
    <link rel="icon" href="favicon.ico">
    <script src="./scripts.js"></script>
</head>

<body>
    <div id="phpHolder">
        <?php
        $quals = array();
        $eighths = array();
        $quarters = array();
        $semis = array();
        $finals = array();
        $none = array();
        $matches_sorted = array();
        $ranks = array();
        $next_match;
        $team_key;
        $event_key;
        $api_key = json_decode(file_get_contents("./credentials.json"), true)["apiKey"];
        $zone_from_js = $_POST["tz_string"];
        date_default_timezone_set($zone_from_js);
        function addMatchInfo(&$output_array, $checked_array, $match_type)
        {
            global $team_key;
            if (in_array($team_key, $checked_array['alliances']['blue']['team_keys'])) {
                $match_info = array($checked_array['match_number'], "Blue", array_search($team_key, $checked_array['alliances']['blue']['team_keys']) + 1, $checked_array['alliances']['red']['team_keys'], $checked_array['alliances']['blue']['team_keys'], $checked_array['predicted_time'], $checked_array['key'], $match_type);
                array_push($output_array, $match_info);
            } elseif (in_array($team_key, $checked_array['alliances']['red']['team_keys'])) {
                $match_info = array($checked_array['match_number'], "Red", array_search($team_key, $checked_array['alliances']['red']['team_keys']) + 1, $checked_array['alliances']['red']['team_keys'], $checked_array['alliances']['blue']['team_keys'], $checked_array['predicted_time'], $checked_array['key'], $match_type);
                array_push($output_array, $match_info);
            }
        }
        function printMatch($match, $match_type)
        {
            if ($match[1] == "Blue") {
                echo "\n<p class='match bluealliance' display='inline-block', onclick='openMatchData(" . '"' . $match[6] . '"' . ");'>" . $match_type . " Match " . $match[0] . "</p>";
            } elseif ($match[1] == "Red") {
                echo "\n<p class='match redalliance' display='inline-block', onclick='openMatchData(" . '"' . $match[6] . '"' . ");'>" . $match_type . " Match " . $match[0] . "</p>";
            }
        }
        function printMatchInfo($match)
        {
            global $team_key, $ranks;
            echo "\n<div id='" . $match[6] . "' class='matchinfo' style='display:none;'>";
            // echo "<p>Match " . $match[0] . " info:</p>";
            echo "<p>Alliance Position: " . $match[1] . " " . $match[2] . "</p>";
            echo "<table>";
            echo "<tr>";
            for ($i = 0; $i < sizeof($match[3]); $i++) {
                if ($match[3][$i] == $team_key) {
                    echo "<th class='redalliance'>Red " . ($i + 1) . "</th>";
                } else {
                    echo "<th>Red " . ($i + 1) . "</th>";
                }
            }
            for ($i = 0; $i < sizeof($match[4]); $i++) {
                if ($match[4][$i] == $team_key) {
                    echo "<th class='bluealliance'>Blue " . ($i + 1) . "</th>";
                } else {
                    echo "<th>Blue " . ($i + 1) . "</th>";
                }
            }
            echo "</tr>";
            echo "<tr>";
            for ($i = 0; $i < sizeof($match[3]); $i++) {
                if ($match[3][$i] == $team_key) {
                    echo "<td class='redalliance'>" . str_replace("frc", "", $match[3][$i]) . "</td>";
                } else {
                    echo "<td>" . str_replace("frc", "", $match[3][$i]) . "</td>";
                }
            }
            for ($i = 0; $i < sizeof($match[4]); $i++) {
                if ($match[4][$i] == $team_key) {
                    echo "<td class='bluealliance'>" . str_replace("frc", "", $match[4][$i]) . "</td>";
                } else {
                    echo "<td>" . str_replace("frc", "", $match[4][$i]) . "</td>";
                }
            }
            echo "</tr>";
            echo "<tr>";
            for ($i = 0; $i < sizeof($match[3]); $i++) {
                echo "<td>" . $ranks[$match[3][$i]]["qual"]["ranking"]["rank"] . "</td>";
            }
            for ($i = 0; $i < sizeof($match[4]); $i++) {
                echo "<td>" . $ranks[$match[4][$i]]["qual"]["ranking"]["rank"] . "</td>";
            }
            echo "</tr>";
            echo "</table>";
            // echo "<p>Alliance Members: "; print $match[3][0] ." ". $match[3][1] ." ". $match[3][2]; echo "</p>";
            echo "<p>Match Time: " . date("M d H:i:s", $match[5]) . "</p>";
            echo "</div>";
        }
        function getAllMatches($array)
        {
            global $quals, $eighths, $quarters, $semis, $finals, $none, $team_key, $event_key, $api_key, $ranks;
            foreach ($array as $checked_array) {
                $match_number = $checked_array['match_number'];
                switch ($checked_array['comp_level']) {
                    case "qm":
                        addMatchInfo($quals, $checked_array, "Qualifiers");
                        break;
                    case "ef":
                        addMatchInfo($eighths, $checked_array, "Eighths Finals");
                        break;
                    case "qf":
                        addMatchInfo($quarters, $checked_array, "Quarterfinals");
                        break;
                    case "sf":
                        addMatchInfo($semis, $checked_array, "Semifinals");
                        break;
                    case "f":
                        addMatchInfo($finals, $checked_array, "Finals");
                        break;
                    default:
                        addMatchInfo($none, $checked_array, "Not Specified");
                        break;
                }
            }
            $matches = array($quals, $eighths, $quarters, $semis, $finals, $none);
            $ranks = json_decode(
                file_get_contents(
                    "https://www.thebluealliance.com/api/v3/event/" . $event_key . "/teams/statuses?X-TBA-Auth-Key=" . $api_key
                ),
                true
            );
            global $matches_sorted;
            function itemsort(&$array, $key)
            {
                $sorter = array();
                $ret = array();
                reset($array);
                foreach ($array as $ii => $va) {
                    $sorter[$ii] = $va[$key];
                }
                asort($sorter);
                foreach ($sorter as $ii => $va) {
                    $ret[$ii] = $array[$ii];
                }
                $array = $ret;
            }
            foreach ($matches as $unsorted) {
                itemsort($unsorted, "0");
                array_push($matches_sorted, $unsorted);
            }
            foreach ($matches_sorted as $matches) {
                foreach ($matches as $match) {
                    printMatch($match, end($match));
                    printMatchInfo($match);
                }
            }
        }
        function countDown()
        {
            global $next_match;
            getNextMatch();
            return date("M d, Y H:i:s", $next_match[5]);
        }
        function virtualKettering()
        {
            global $team_key, $matches_sorted, $ranks;
            echo "<div id='ketteringWrapper'>";
            echo "<table id='kettering'>";
            echo "<tr>";
            echo "<th>Match</th>";
            for ($i = 0; $i < sizeof($matches_sorted[0][0][3]); $i++) {
                echo "<th>Red " . ($i + 1) . "</th>";
            }
            for ($i = 0; $i < sizeof($matches_sorted[0][0][4]); $i++) {
                echo "<th>Blue " . ($i + 1) . "</th>";
            }
            echo "<th>Predicted Time</th>";
            echo "</tr>";

            foreach ($matches_sorted as $matches) {
                foreach ($matches as $match) {
                    echo "<tr>";
                    echo "<td class='matchName'>" . $match[7] . " " . $match[0] . "</td>";
                    for ($i = 0; $i < sizeof($match[3]); $i++) {
                        if ($match[3][$i] == $team_key) {
                            echo "<td class='redalliance'>" . str_replace("frc", "", $match[3][$i]) . "</td>";
                        } else {
                            echo "<td>" . str_replace("frc", "", $match[3][$i]) . "</td>";
                        }
                    }
                    for ($i = 0; $i < sizeof($match[4]); $i++) {
                        if ($match[4][$i] == $team_key) {
                            echo "<td class='bluealliance'>" . str_replace("frc", "", $match[4][$i]) . "</td>";
                        } else {
                            echo "<td>" . str_replace("frc", "", $match[4][$i]) . "</td>";
                        }
                    }
                    echo "<td>" . date("M d H:i:s", $match[5]) . "</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td class='rank'></td>";
                    for ($i = 0; $i < sizeof($match[3]); $i++) {
                        echo "<td class='rank'> rnk: " . $ranks[$match[3][$i]]["qual"]["ranking"]["rank"] . "</td>";
                    }
                    for ($i = 0; $i < sizeof($match[4]); $i++) {
                        echo "<td class='rank'> rnk: " . $ranks[$match[4][$i]]["qual"]["ranking"]["rank"] . "</td>";
                    }
                    echo "</tr>";
                }
            }
            echo "</table>";
            echo "</div>";
        }
        function getNextMatch()
        {
            global $matches_sorted, $next_match;
            foreach ($matches_sorted as $matches) {
                foreach (array_reverse($matches) as $match) {
                    if ($match[5] > time()) {
                        $next_match = $match;
                    }
                }
            }
            if (empty($next_match)) {
                $next_match = $matches_sorted[0][0];
                foreach ($matches_sorted as $match_type) {
                    if (!empty($match_type)) {
                        $next_match = end($match_type);
                    }
                }
            }
        }
        function nextMatchPanel()
        {
            global $next_match, $team_key;
            getNextMatch();
            echo "<div id='nextpanel'>";
            echo "<p class='nextpanel'>Next Match: " . $next_match[7] . " " . $next_match[0] . "</p>";
            echo "<p class='nextpanel'>" . $next_match[1] . " " . $next_match[2] . "</p>";
            if ($next_match[1] == "Red") {
                echo "<p id='bumper' class='redbg'>" . str_replace("frc", "", $team_key) . "</p>";
            } elseif ($next_match[1] == "Blue") {
                echo "<p id='bumper' class='bluebg'>" . str_replace("frc", "", $team_key) . "</p>";
            }
            echo "</div>";
        }
        function statusPanel()
        {
            global $api_key, $team_key, $event_key;
            $request_url = "http://www.thebluealliance.com/api/v3/team/" . $team_key . "/event/" . $event_key . "/status?X-TBA-Auth-Key=" . $api_key;
            $response = file_get_contents($request_url);
            $decoded_array = json_decode($response, true);
            // print_r($decoded_array);
            echo "<div id='statuspanel'>";
            if (isset($decoded_array['qual'])) {
                $status_array = array($decoded_array['qual']['ranking']['rank'], $decoded_array['qual']['ranking']['sort_orders'][0]);
                echo "<p>Team " . str_replace("frc", "", $team_key) . " rank: " . $status_array[0] . "</p>";
                echo "<p>Average RP: " . $status_array[1] . "</p>";
            } else {
                echo "<p>Ranking has not been released yet.</p>";
            }
            echo "<p id='currentTime'>Time: </p>";
            echo "</div>";
        }

        if (isset($_POST["team_key"]) && isset($_POST["event_key"])) {
            global $api_key, $team_key, $event_key;
            if (strpos($_POST["team_key"], "frc") !== false) {
                $team_key = $_POST["team_key"];
            } else {
                $team_key = "frc" . $_POST["team_key"];
            }
            $event_key = $_POST["event_key"];
            $request_url = "http://www.thebluealliance.com/api/v3/team/" . $team_key . "/event/" . $event_key . "/matches/simple?X-TBA-Auth-Key=";
            $full_url = $request_url . $api_key;
            $response = file_get_contents($full_url);
            $decoded_array = json_decode($response, true);
            if ($decoded_array) {
                // echo "<p>All matches in query:</p>";
                echo "<div id='sidebar'>";
                echo "<div id='matches'>";
                getAllMatches($decoded_array);
                echo "</div>";
                statusPanel();
                echo "</div>";
                echo "<div id='maincontent'>";
                nextMatchPanel();
                echo "<div id='counterDiv'><h1 id='counter'></h1></div>";
                virtualKettering();
            } else {
                echo "<p>No data was returned from The Blue Alliance.</p>";
            }
        } elseif (isset($_POST["team_key"]) && isset($_POST["latest"])) {
            global $api_key, $team_key, $event_key;
            if (strpos($_POST["team_key"], "frc") !== false) {
                $team_key = $_POST["team_key"];
            } else {
                $team_key = "frc" . $_POST["team_key"];
            }
            $event_request_url = "http://www.thebluealliance.com/api/v3/team/" . $team_key . "/events/simple?X-TBA-Auth-Key=" . $api_key;
            $event_response = file_get_contents($event_request_url);
            $decoded_event = json_decode($event_response, true);
            $event_key = end($decoded_event)["key"];
            $request_url = "http://www.thebluealliance.com/api/v3/team/" . $team_key . "/event/" . $event_key . "/matches/simple?X-TBA-Auth-Key=";
            $full_url = $request_url . $api_key;
            $response = file_get_contents($full_url);
            $decoded_array = json_decode($response, true);
            if ($decoded_array) {
                echo "<div id='sidebar'>";
                echo "<div id='matches'>";
                getAllMatches($decoded_array);
                echo "</div>";
                statusPanel();
                echo "</div>";
                echo "<div id='maincontent'>";
                nextMatchPanel();
                echo "<div id='counterDiv'><h1 id='counter'></h1></div>";
                virtualKettering();
            } else {
                echo "<p>No data was returned from The Blue Alliance.</p>";
            }
        } else {
            header("Location: index.html");
        }
        
        echo "</div>";
        echo "<div id='copy'>";
        echo "<p class='copy'><a href='https://clock.parkerdaletech.com'>clock.parkerdaletech.com</a></p>";
        echo "<p class='copy'><a href='https://github.com/lacedwithennui/pit-clock'>github.com/lacedwithennui/pit-clock</a></p>";
        echo "<p class='copy'>Copyright Hazel Belmont, FRC 5587 Titan Robotics.</p>";
        echo "</div>";
        echo "<img src='logo.png' />";
        ?>
    </div>
    <script>
        var matchTime = "<?php echo countDown(); ?>";
        updateTimer();
        setInterval(updateTimer, 1000);
        setInterval(flicker, 500);
        setInterval(
            'location.reload()'
        , 120000);
    </script>
</body>

</html>