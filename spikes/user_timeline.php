<?php
require_once("../config.php");

$tweets = query("SELECT DISTINCT(idUser) as idUser FROM semantic_tweets_alcolic st WHERE idUser IS NOT NULL AND NOT EXISTS (SELECT stt.id FROM semantic_tweets_alcolic stt WHERE st.idUser = stt.idUser AND stt.drunk = 'A') LIMIT 10");

foreach (getRows($tweets) as $key => $value) {
	try {

		$maxId = 0;
		$ind = 0;
		do {
			$timeline = json_decode(getUserTimeLine($value["idUser"], $maxId), true);		
			foreach ($timeline as $key => $tweet) {
				$id = $tweet["id_str"];
				$link = "https://twitter.com/" . $tweet["user"]["screen_name"] . "/status/" . $id;
				$text = $tweet["text"];
				$maxId = $id;
				$idUsuario = $tweet["user"]["id_str"];
				$ind++;
				try {
	                insert("semantic_tweets_alcolic", array("id", "tweet", "link", "situacao", "content", "drunk", "idUser"), array($id, $text, $link, 1, json_encode($tweet), "A", $idUsuario), false);
	            } catch (Exception $e) {
	                if ($e->getCode() == 1062) {
	                    continue;
	                }
	            }
			}
		} while (count($timeline) >= 199);
	} catch (Exception $e) {
		debug($user_id);
		debug($value["id"]);
		debug($get . " --- " . $num);
		print_r($e->getMessage());
		die;
	}
}

function getUserTimeLine($id, $since_id = 0) {
    $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    //$getfield = '?user_id=' . $id . "&count=500";
    $getfield = '?user_id=' . $id . "&count=500";

    if ($since_id > 0) {
    	$getfield .= "&max_id=" . $since_id;
    }
    $requestMethod = 'GET';
    $twitter = new TwitterAPIExchange(getSettings());
    return $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();

}
?>