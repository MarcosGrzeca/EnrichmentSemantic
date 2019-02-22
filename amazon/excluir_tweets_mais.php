<?php 

require_once("../config.php");

set_time_limit(280);

do {
    $tweets = query("SELECT count(*) as total, user_id FROM chat_tweets WHERE drunk = 'A' GROUP BY user_id HAVING(total) > 7 LIMIT 1000");
    
    $rows = getNumRows($tweets);
    foreach (getRows($tweets) as $key => $value) {
        if ($value["user_id"] > 0) {

            $limite = $value["total"] - 7;
            $sql = "DELETE FROM chat_tweets WHERE drunk = 'A' AND user_id = '" . $value["user_id"] . "' LIMIT " . $limite;
            query($sql);
        }
    }
    sleep(5);
} while ($rows > 0);
?>