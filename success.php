<!DOCTYPE html>
<html>
    <head>
        <title>Your Information</title>
        <link rel="stylesheet" href="frcdashstyles.css">
    </head>
    <body>
        <div id="phpHolder">
            <?php
                //This function is responsible for sorting our data, printing it in a human-readable format, and storing more info about a match. It needs an array that's been decoded from JSON for most of that, and a team key so it can see what alliance we're in
                function getAllMatches($array, $team_key) {
                    //Set up arrays for every type of match (necessary for sorting matches by type)
                    $quals = array();
                    $eighths = array();
                    $quarters = array();
                    $semis = array();
                    $finals = array();
                    $none = array();
                    
                    $quals_nums = array();
                    $eighths_nums = array();
                    $quarters_nums = array();
                    $semis_nums = array();
                    $finals_nums = array();
                    $none_nums = array();
                    
                    foreach($array as $checked_array) {
                        $match_number = $checked_array['match_number'];
                        //Check the match's type from the data TBA gave us
                        switch($checked_array['comp_level']) {
                            case "qm":
                                //Check if our alliance is blue or red
                                if(in_array($team_key, $checked_array['alliances']['blue']['team_keys'])){
                                    //Add our match number and alliance color to the match type's array as an array
                                    $match_info = array($match_number, "blue");
                                    array_push($quals, $match_info);
                                    array_push($quals_nums, $match_number);
                                }
                                elseif(in_array($team_key, $checked_array['alliances']['red']['team_keys'])){
                                    $match_info = array($match_number, "red");
                                    array_push($quals, $match_info);
                                    array_push($quals_nums, $match_number);
                                }
                                break;
                            case "ef":
                                if(in_array($team_key, $checked_array['alliances']['blue']['team_keys'])){
                                    $match_info = array($match_number, "blue");
                                    array_push($eighths, $match_info);
                                    array_push($eighths_nums, $match_number);
                                }
                                elseif(in_array($team_key, $checked_array['alliances']['red']['team_keys'])){
                                    $match_info = array($match_number, "red");
                                    array_push($eighths, $match_info);
                                    array_push($eighths_nums, $match_number);
                                }
                                break;
                            case "qf":
                                if(in_array($team_key, $checked_array['alliances']['blue']['team_keys'])){
                                    $match_info = array($match_number, "blue");
                                    array_push($quarters, $match_info);
                                    array_push($quarters_nums, $match_number);
                                }
                                elseif(in_array($team_key, $checked_array['alliances']['red']['team_keys'])){
                                    $match_info = array($match_number, "red");
                                    array_push($quarters, $match_info);
                                    array_push($quarters_nums, $match_number);
                                }
                                break;
                            case "sf":
                                if(in_array($team_key, $checked_array['alliances']['blue']['team_keys'])){
                                    $match_info = array($match_number, "blue");
                                    array_push($semis, $match_info);
                                    array_push($semis_nums, $match_number);
                                }
                                elseif(in_array($team_key, $checked_array['alliances']['red']['team_keys'])){
                                    $match_info = array($match_number, "red");
                                    array_push($semis, $match_info);
                                    array_push($semis_nums, $match_number);
                                }
                                break;
                            case "f":
                                if(in_array($team_key, $checked_array['alliances']['blue']['team_keys'])){
                                    $match_info = array($match_number, "blue");
                                    array_push($finals, $match_info);
                                    array_push($finals_nums, $match_number);
                                }
                                elseif(in_array($team_key, $checked_array['alliances']['red']['team_keys'])){
                                    $match_info = array($match_number, "red");
                                    array_push($finals, $match_info);
                                    array_push($finals_nums, $match_number);
                                }
                                break;
                            default:
                                //If there's no match type, add the match number to its own category that we can deal with later
                                $match_type = "No match type ";
                                array_push($none, $match_number);
                                array_push($none_nums, $match_number);
                                break;
                        }
                    }
                    //Let's sort some stuff with an unnecessarily long function because PHP sucks (not as much as JS though, you can quote me).
                    $matches = array($quals, $eighths, $quarters, $semis, $finals, $none);
                    $match_nums = array($quals_nums, $eighths_nums, $quarters_nums, $semis_nums, $finals_nums, $none_nums);
                    $matches_sorted = array();
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
                    //Here, we can print out all of our values
                    foreach($matches_sorted[0] as $match) {
                        //If the match's info has "blue" or "red" in it, add a class to the text that corresponds to the color. CSS can then deal with that and color the text accordingly.
                        if($match[1] == "blue") {
                            echo "<p class='bluealliance'>" . "Qualifiers Match " . $match[0] . "</p>";
                        }
                        elseif($match[1] == "red") {
                            echo "<p class='redalliance'>" . "Qualifiers Match " . $match[0] . "</p>";
                        }
                    }
                    foreach($matches_sorted[1] as $match) {
                        if($match[1] == "blue") {
                            echo "<p class='bluealliance'>" . "Eighths Finals Match " . $match[0] . "</p>";
                        }
                        elseif($match[1] == "red") {
                            echo "<p class='redalliance'>" . "Eighths Finals Match " . $match[0] . "</p>";
                        }
                    }
                    foreach($matches_sorted[2] as $match) {
                        if($match[1] == "blue") {
                            echo "<p class='bluealliance'>" . "Quarterfinals Match " . $match[0] . "</p>";
                        }
                        elseif($match[1] == "red") {
                            echo "<p class='redalliance'>" . "Quarterfinals Match " . $match[0] . "</p>";
                        }
                    }
                    foreach($matches_sorted[3] as $match) {
                        if($match[1] == "blue") {
                            echo "<p class='bluealliance'>" . "Semifinals Match " . $match[0] . "</p>";
                        }
                        elseif($match[1] == "red") {
                            echo "<p class='redalliance'>" . "Semifinals Match " . $match[0] . "</p>";
                        }
                    }
                    foreach($matches_sorted[5] as $match) {
                        if($match[1] == "blue") {
                            echo "<p class='bluealliance'>" . "Finals Match " . $match[0] . "</p>";
                        }
                        elseif($match[1] == "red") {
                            echo "<p class='redalliance'>" . "Finals Match " . $match[0] . "</p>";
                        }
                    }
                    foreach($matches_sorted[5] as $match) {
                        if($match[1] == "blue") {
                            echo "<p class='bluealliance'>" . "[No Type Specified] Match " . $match[0] . "</p>";
                        }
                        elseif($match[1] == "red") {
                            echo "<p class='redalliance'>" . "[No Type Specified] Match " . $match[0] . "</p>";
                        }
                    }
                }
                //Here, stuff actually runs.
                //If the team_key and event_key came from the input page, do stuff
                if($_POST["team_key"] && $_POST["event_key"]) {
                    //Assign our team key and event key to easily-usable variables
                    $team_key = $_POST["team_key"];
                    $event_key = $_POST["event_key"];
                    $api_key = "QszNNcJIpRcbbF8UIiU5WmqByHfIGaNFrTVcYR39DPlKft0Axtf31BXUL5rCmre4";
                    //Separate the api key from the url so we can show it to the user (in the pits) without someone stealing my account
                    $request_url = "https://www.thebluealliance.com/api/v3/team/".$team_key."/event/".$event_key."/matches/simple?X-TBA-Auth-Key=";
                    $full_url = $request_url . $api_key;
                    //Request the json from TBA
                    $response = file_get_contents($full_url);
                    //Convert all of the JSON arrays into PHP arrays
                    $decoded_array = json_decode($response, true);
                    
                    //print out all of our data
                    echo "<p>Your Team Key: <span class=keys>$team_key</span></p>";
                    echo "<p>Your Event Key: <span class=keys>$event_key</span></p>";
                    echo "<p>Request URL: $request_url (authkey hidden)</p>";
                    //If the decoding was a success, do the following
                    if($decoded_array) {
                        echo "<p>All matches in query:</p>";
                        //Call our function from earlier giving it our decoded array and team key
                        getAllMatches($decoded_array, $team_key);
                    }
                    //Otherwise, tell the user there was no data.
                    else {
                        echo "<p>No data was returned from The Blue Alliance.</p>";
                    }
                }
                //Otherwise, go straight back to the input page
                else {
                    header("Location: index.html");
                }
            ?>
        </div>
    </body>
</html>
