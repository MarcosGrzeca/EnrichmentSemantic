<?php 

require_once("../config.php");

set_time_limit(290);

$tweets = query("SELECT * FROM chat_tweets WHERE situacao = 1 AND user_id = 0");

$ind = 0;
foreach (getRows($tweets) as $key => $value) {
    try {
        $tweet = json_decode($value["tweetResource"], true);
        if ($tweet["user"]["id"] > 0) {
            update("chat_tweets", $value["id"], array("user_id" => $tweet["user"]["id"]));
        }
    } catch (Exception $e) {
        debug("ERRO");
        debug($e->getMessage());        
    }
    $ind++;
}
?>