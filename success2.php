<!DOCTYPE html>
<html>
    <head>
        <title>Match Countdown</title>
        <link rel="stylesheet" href="dashstyles.css">
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
                $next_match;
                $team_key;
                function addMatchInfo(&$output_array, $checked_array, $match_type) {
                    global $team_key;
                    if(in_array($team_key, $checked_array['alliances']['blue']['team_keys'])){
                        $match_info = array($checked_array['match_number'], "Blue", array_search($team_key, $checked_array['alliances']['blue']['team_keys'])+1, $checked_array['alliances']['red']['team_keys'], $checked_array['alliances']['blue']['team_keys'], $checked_array['predicted_time'], $checked_array['key'], $match_type);
                        array_push($output_array, $match_info);
                    }
                    elseif(in_array($team_key, $checked_array['alliances']['red']['team_keys'])){
                        $match_info = array($checked_array['match_number'], "Red", array_search($team_key, $checked_array['alliances']['red']['team_keys'])+1, $checked_array['alliances']['red']['team_keys'], $checked_array['alliances']['blue']['team_keys'], $checked_array['predicted_time'], $checked_array['key'], $match_type);
                        array_push($output_array, $match_info);
                    }
                }
                function printMatch($match, $match_type) {
                    if($match[1] == "Blue") {
                        echo "\n<p class='match bluealliance' display='inline-block', onclick='openMatchData(" . '"' . $match[6] . '"' . ");'>" . $match_type . " Match " . $match[0] . "</p>";
                    }
                    elseif($match[1] == "Red") {
                        echo "\n<p class='match redalliance' display='inline-block', onclick='openMatchData(" . '"' . $match[6] . '"' . ");'>" . $match_type . " Match " . $match[0] . "</p>";
                    }
                }
                function printMatchInfo($match) {
                    global $team_key;
                    echo "\n<div id='". $match[6] ."' class='matchinfo' style='display:none;'>";
                    echo "<p>Match " . $match[0] . " info:</p>";
                    echo "<p>Alliance Position: " . $match[1] . " " . $match[2] . "</p>";
                    echo "<table>";
                    echo "<tr>";
                    for($i = 0; $i < sizeof($match[3]); $i++) {
                        if($match[3][$i] == $team_key) {
                            echo "<th class='redalliance'>Red ".($i+1)."</th>";
                        }
                        else {
                            echo "<th>Red ".($i+1)."</th>";
                        }
                    }
                    for($i = 0; $i < sizeof($match[4]); $i++) {
                        if($match[4][$i] == $team_key) {
                            echo "<th class='bluealliance'>Blue ".($i+1)."</th>";
                        }
                        else {
                            echo "<th>Blue ".($i+1)."</th>";
                        }
                    }
                    echo "</tr>";
                    echo "<tr>";
                    for($i = 0; $i < sizeof($match[3]); $i++) {
                        if($match[3][$i] == $team_key) {
                            echo "<td class='redalliance'>".str_replace("frc", "", $match[3][$i])."</td>";
                        }
                        else {
                            echo "<td>".str_replace("frc", "", $match[3][$i])."</td>";
                        }
                    }
                    for($i = 0; $i < sizeof($match[4]); $i++) {
                        if($match[4][$i] == $team_key) {
                            echo "<td class='bluealliance'>".str_replace("frc", "", $match[4][$i])."</td>";
                        }
                        else {
                            echo "<td>".str_replace("frc", "", $match[4][$i])."</td>";
                        }
                    }
                    echo "</tr>";
                    echo "</table>";
                    // echo "<p>Alliance Members: "; print $match[3][0] ." ". $match[3][1] ." ". $match[3][2]; echo "</p>";
                    echo "<p>Match Time: " . date("M d H:i:s", $match[5]) . "</p>";
                    echo "</div>";
                }
                function getAllMatches($array) {
                    global $quals, $eighths, $quarters, $semis, $finals, $none, $team_key;
                    foreach($array as $checked_array) {
                        $match_number = $checked_array['match_number'];
                        switch($checked_array['comp_level']) {
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
                    global $matches_sorted;
                    function itemsort(&$array, $key) {
                        $sorter=array();
                        $ret=array();
                        reset($array);
                        foreach ($array as $ii => $va) {
                            $sorter[$ii]=$va[$key];
                        }
                        asort($sorter);
                        foreach ($sorter as $ii => $va) {
                            $ret[$ii]=$array[$ii];
                        }
                        $array=$ret;
                    }
                    foreach($matches as $unsorted) {
                        itemsort($unsorted, "0");
                        array_push($matches_sorted, $unsorted);
                    }
					foreach($matches_sorted as $matches) {
						foreach($matches as $match) {
                        	printMatch($match, end($match));
                        	printMatchInfo($match);
                    	}
					}
                }
                function countDown() {
					global $matches_sorted;
                    foreach(array_reverse($matches_sorted) as $match_type){
						if(!empty($match_type)){
							$match_time = end($match_type)[5];
							return(date("M d, Y H:i:s", $match_time));
							break;
						}
					}
                }
                function virtualKettering() {
                    global $team_key, $matches_sorted;
                    echo "<div id='ketteringWrapper'>";
                    echo "<table id='kettering'>";
                    echo "<tr>";
                    echo "<th>Match</th>";
                    for($i = 0; $i < sizeof($matches_sorted[0][0][3]); $i++) {
                        echo "<th>Red ".($i+1)."</th>";
                    }
                    for($i = 0; $i < sizeof($matches_sorted[0][0][4]); $i++) {
                        echo "<th>Blue ".($i+1)."</th>";
                    }
                    echo "<th>Predicted Time</th>";
                    echo "</tr>";
                    
                    foreach($matches_sorted as $matches) {
                        foreach($matches as $match) {
                            echo "<tr>";
                            echo "<td>".$match[7]." ".$match[0]."</td>";
                            for($i = 0; $i < sizeof($match[3]); $i++) {
                                if($match[3][$i] == $team_key) {
                                    echo "<td class='redalliance'>".str_replace("frc", "", $match[3][$i])."</td>";
                                }
                                else {
                                    echo "<td>".str_replace("frc", "", $match[3][$i])."</td>";
                                }
                            }
                            for($i = 0; $i < sizeof($match[4]); $i++) {
                                if($match[4][$i] == $team_key) {
                                    echo "<td class='bluealliance'>".str_replace("frc", "", $match[4][$i])."</td>";
                                }
                                else {
                                    echo "<td>".str_replace("frc", "", $match[4][$i])."</td>";
                                }
                            }
                            echo "<td>".date("M d H:i:s", $match[5])."</td>";
                            echo "</tr>";
                        }
                    }
                    echo "</table>";
                    echo "</div>";
                }
                function getNextMatch() {
                    global $matches_sorted, $next_match;
                    foreach($matches_sorted as $matches) {
                        foreach(array_reverse($matches) as $match) {
                            if($match[5] > time()) {
                                $next_match = $match;
                            }
                        }
                    }
                    if(empty($next_match)) {
                        $next_match = $matches_sorted[0][0];
                        foreach($matches_sorted as $match_type) {
                            if(!empty($match_type)) {
                                $next_match = end($match_type);
                            }
                        }
                    }
                }
                function nextMatchPanel() {
                    global $next_match, $team_key;
                    getNextMatch();
                    echo "<div id='nextpanel'>";
                    echo "<p class='nextpanel'>Next Match: ".$next_match[7]." ".$next_match[0]."</p>";
                    echo "<p class='nextpanel'>".$next_match[1]." ".$next_match[2]."</p>";
                    if($next_match[1] == "Red") {
                        echo "<p id='bumper' class='redbg'>".str_replace("frc", "", $team_key)."</p>";
                    }
                    elseif($next_match[1] == "Blue") {
                        echo "<p id='bumper' class='bluebg'>".str_replace("frc", "", $team_key)."</p>";
                    }
                    echo "</div>";
                }
                function statusPanel($api_key, $event_key) {
                    global $team_key;
                    $request_url = "https://www.thebluealliance.com/api/v3/team/".$team_key."/event/".$event_key."/stuatus?X-TBA-Auth-Key=".$api_key;
                    $response = file_get_contents($request_url);
                    $decoded_array = json_decode($response, true);
                    $status_array = array($decoded_array['qual']);
                    
                }
                date_default_timezone_set("America/North_Dakota/Center");
                if(isset($_POST["team_key"]) && isset($_POST["event_key"])) {
					if(strpos($_POST["team_key"], "frc") !== false) {
						$team_key = $_POST["team_key"];
					}
					else {
                    	$team_key = "frc" . $_POST["team_key"];
					}
                    $event_key = $_POST["event_key"];
                    $api_key = "QszNNcJIpRcbbF8UIiU5WmqByHfIGaNFrTVcYR39DPlKft0Axtf31BXUL5rCmre4";
                    $request_url = "https://www.thebluealliance.com/api/v3/team/".$team_key."/event/".$event_key."/matches/simple?X-TBA-Auth-Key=";
                    $full_url = $request_url . $api_key;
                    $response = file_get_contents($full_url);
                    $decoded_array = json_decode($response, true);
                    // echo "<p>Your Team Key: <span class=keys>$team_key</span></p>";
                    // echo "<p>Your Event Key: <span class=keys>$event_key</span></p>";
                    // echo "<p>Request URL: " . $request_url . "(authkey hidden)</p>";
                    if($decoded_array) {
                        // echo "<p>All matches in query:</p>";
						echo "<div id='matches'>";
                        getAllMatches($decoded_array);
						echo "</div>";
						echo "<div id='counterDiv'><h1 id='counter'></h1></div>";
                        echo "<img src='logo.png' />";
                    }
                    else {
                        echo "<p>No data was returned from The Blue Alliance.</p>";
                    }
                }
				elseif(isset($_POST["team_key"]) && isset($_POST["latest"])) {
					if(strpos($_POST["team_key"], "frc") !== false) {
						$team_key = $_POST["team_key"];
					}
					else {
                    	$team_key = "frc" . $_POST["team_key"];
					}
                    $api_key = "";
					$event_request_url = "https://www.thebluealliance.com/api/v3/team/".$team_key."/events/simple?X-TBA-Auth-Key=".$api_key;
					$event_response = file_get_contents($event_request_url);
					$decoded_event = json_decode($event_response, true);
					$event_key = end($decoded_event)["key"];
                    $request_url = "https://www.thebluealliance.com/api/v3/team/".$team_key."/event/".$event_key."/matches/simple?X-TBA-Auth-Key=";
                    $full_url = $request_url . $api_key;
                    $response = file_get_contents($full_url);
                    $decoded_array = json_decode($response, true);
                    // echo "<p>Your Team Key: <span class=keys>$team_key</span></p>";
                    // echo "<p>Your Event Key: <span class=keys>$event_key</span></p>";
                    // echo "<p>Request URL: " . $request_url . "(authkey hidden)</p>";
                    if($decoded_array) {
                        echo "<p>All matches in query:</p>";
						echo "<div id='matches'>";
                        getAllMatches($decoded_array);
						echo "</div>";
						echo "<div id='counterDiv'><h1 id='counter'></h1></div>";
                    }
                    else {
                        echo "<p>No data was returned from The Blue Alliance.</p>";
                    }
				}
                else {
                    header("Location: index.html");
                }
                nextMatchPanel();
                virtualKettering();
                
            ?>
        </div>
		<script>
			var matchTime = "<?php echo countDown();?>";
			var countDownDate = new Date(matchTime).getTime();
			var x = setInterval(function() {
			  var now = new Date().getTime();
			  var distance = countDownDate - now;
			  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			  document.getElementById("counter").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
			}, 1000);
			function openMatchData(match) {
				var x = document.getElementById(match);
				if (x.style.display === "none") {
					x.style.display = "block";
				} 
				else {
					x.style.display = "none";
				}
			}
		</script>
    </body>
</html>
