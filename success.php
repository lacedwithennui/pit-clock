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
                function addMatchInfo(&$output_array, $team_key, $checked_array, $match_type) {
                    if(in_array($team_key, $checked_array['alliances']['blue']['team_keys'])){
                        $match_info = array($checked_array['match_number'], "Blue", array_search($team_key, $checked_array['alliances']['blue']['team_keys'])+1, $checked_array['alliances']['blue']['team_keys'], $checked_array['predicted_time'], $checked_array['key'], $match_type);
                        array_push($output_array, $match_info);
                    }
                    elseif(in_array($team_key, $checked_array['alliances']['red']['team_keys'])){
                        $match_info = array($checked_array['match_number'], "Red", array_search($team_key, $checked_array['alliances']['red']['team_keys'])+1, $checked_array['alliances']['red']['team_keys'], $checked_array['predicted_time'], $checked_array['key'], $match_type);
                        array_push($output_array, $match_info);
                    }
                }
                function printMatch($match, $match_type) {
                    if($match[1] == "Blue") {
                        echo "\n<p class='match bluealliance' onclick='openMatchData(" . '"' . $match[5] . '"' . ");'>" . $match_type . " Match " . $match[0] . "</p>";
                    }
                    elseif($match[1] == "Red") {
                        echo "\n<p class='match redalliance' onclick='openMatchData(" . '"' . $match[5] . '"' . ");'>" . $match_type . " Match " . $match[0] . "</p>";
                    }
                }
                function printMatchInfo($match) {
                    echo "\n<div id='". $match[5] ."' class='matchinfo'>";
                    echo "<p>Match " . $match[0] . " info:</p>";
                    echo "<p>Alliance Color &amp; Predicted Position: " . $match[1] . " " . $match[2] . "</p>";
                    echo "<p>Alliance Members: "; print $match[3][0] ." ". $match[3][1] ." ". $match[3][2]; echo "</p>";
                    echo "<p>Match Time: " . date("M d H:i:s", $match[4]) . "</p>";
                    echo "</div>";
                }
                function getAllMatches($array, $team_key) {
                    global $quals, $eighths, $quarters, $semis, $finals, $none;
                    foreach($array as $checked_array) {
                        $match_number = $checked_array['match_number'];
                        switch($checked_array['comp_level']) {
                            case "qm":
                                addMatchInfo($quals, $team_key, $checked_array, "Qualifiers");
                                break;
                            case "ef":
                                addMatchInfo($eighths, $team_key, $checked_array, "Eighths Finals");
                                break;
                            case "qf":
                                addMatchInfo($quarters, $team_key, $checked_array, "Quarterfinals");
                                break;
                            case "sf":
                                addMatchInfo($semis, $team_key, $checked_array, "Semifinals");
                                break;
                            case "f":
                                addMatchInfo($finals, $team_key, $checked_array, "Finals");
                                break;
                            default:
                                addMatchInfo($none, $team_key, $checked_array, "Not Specified");
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
							$match_time = end($match_type)[4];
							return(date("M d, Y H:i:s", $match_time));
							break;
						}
					}
                }
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
                    echo "<p>Your Team Key: <span class=keys>$team_key</span></p>";
                    echo "<p>Your Event Key: <span class=keys>$event_key</span></p>";
                    echo "<p>Request URL: " . $request_url . "(authkey hidden)</p>";
                    if($decoded_array) {
                        echo "<p>All matches in query:</p>";
						echo "<div id='matches'>";
                        getAllMatches($decoded_array, $team_key);
						echo "</div>";
						echo "<div id='counterDiv'><h1 id='counter'></h1></div>";
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
                    echo "<p>Your Team Key: <span class=keys>$team_key</span></p>";
                    echo "<p>Your Event Key: <span class=keys>$event_key</span></p>";
                    echo "<p>Request URL: " . $request_url . "(authkey hidden)</p>";
                    if($decoded_array) {
                        echo "<p>All matches in query:</p>";
						echo "<div id='matches'>";
                        getAllMatches($decoded_array, $team_key);
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
