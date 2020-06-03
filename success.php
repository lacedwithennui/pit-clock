<!DOCTYPE html>
<html>
    <head>
        <title>Your Information</title>
        <link rel="stylesheet" href="frcdashstyles.css">
        <style>
            p, h1, h2, h3 {
                font-family: Segoe, "Segoe UI", "DejaVu Sans", "Trebuchet MS", Verdana, "sans-serif";
            }
            .keys {
                color: green;
            }
            .bluealliance {
                color: blue;
            }
            .redalliance {
                color: red;
            }
        </style>
    </head>
    <body>
        <div id="phpHolder">
            <?php
                function getAllMatches($array, $team_key) {
                    foreach($array as $checked_array) {
                        switch($checked_array['comp_level']) {
                            case "qm":
                                $match_type = "Qualifiers ";
                                break;
                            case "ef":
                                $match_type = "Eighth Finals ";
                                break;
                            case "qf":
                                $match_type = "Quarterfinals ";
                                break;
                            case "sf":
                                $match_type = "Semifinals ";
                                break;
                            case "f":
                                $match_type = "Finals ";
                                break;
                            default:
                                $match_type = "No match type ";
                                break;
                        }
                        if(in_array($team_key, $checked_array['alliances']['blue']['team_keys'])){
                            echo "<p class='bluealliance'>" . $match_type . $checked_array['match_number'] . "</p>";
                        }
                        elseif(in_array($team_key, $checked_array['alliances']['red']['team_keys'])){
                            echo "<p class='redalliance'>" . $match_type . $checked_array['match_number'] . "</p>";
                        }
                        else {
                            echo "<p>Error: Match not in any alliance, may have been disqualified.</p>";
                        }
                    }
                }
                if(strlen($_POST["team_key"]) > 0) {
                    $team_key = $_POST["team_key"];
                    $event_key = $_POST["event_key"];
                    $api_key = "QszNNcJIpRcbbF8UIiU5WmqByHfIGaNFrTVcYR39DPlKft0Axtf31BXUL5rCmre4";
                    $request_url = "https://www.thebluealliance.com/api/v3/team/".$team_key."/event/".$event_key."/matches/simple?X-TBA-Auth-Key=";
                    $full_url = $request_url . $api_key;
                    $response = file_get_contents($full_url);
                    $decodedArray = json_decode($response, true);
                    
                    echo "<p>Your Team Key: <span class=keys>$team_key</span></p>";
                    echo "<p>Your Event Key: <span class=keys>$event_key</span></p>";
                    echo "<p>Request URL: $request_url (authkey hidden)</p>";
                    if($decodedArray) {
                        echo "<p>All matches in query:</p>";
                        getAllMatches($decodedArray, $team_key);
                    }
                    else {
                        echo "<p>Request decoding failed OR no data was returned.</p>";
                    }
                }
                else {
                    header("Location: index.html");
                }
            ?>
        </div>
    </body>
</html>
