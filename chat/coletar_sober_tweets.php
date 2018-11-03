<?php
require_once("../config.php");

set_time_limit(290);

$tweets = query("   SELECT DISTINCT(user_id) as user_id
                    FROM chat_tweets st
                    WHERE user_id > 0
                    AND NOT EXISTS (
                        SELECT stt.id
                        FROM chat_tweets stt
                        WHERE st.user_id = stt.user_id
                        AND stt.drunk = 'A'
                    ) LIMIT 20");

foreach (getRows($tweets) as $key => $value) {
    try {
        $maxId = 0;
        $ind = 0;
        // do {
            $timeline = json_decode(getUserTimeLine($value["user_id"], $maxId), true);
            foreach ($timeline as $key => $tweet) {
                if ($tweet["lang"] != "en") {
                    continue;
                }

                $idRetweet = 0;
                if (isset($tweet["retweeted_status"])) {
                    $idRetweet = $tweet["retweeted_status"]["id_str"];
                    debug($idRetweet);
                }

                $id = $tweet["id_str"];
                $link = "https://twitter.com/" . $tweet["user"]["screen_name"] . "/status/" . $id;
                $text = $tweet["text"];
                $maxId = $id;
                $idUsuario = $tweet["user"]["id_str"];
                $user_name = $tweet["user"]["screen_name"];
                $ind++;

                try {
                    insert("chat_tweets",
                        array("id", "textoOriginal", "link", "situacao", "tweetResource",       "drunk",    "user_id",   "user_name",    "retweetid"),
                        array($id,  $text,           $link,  1,         json_encode($tweet),    "A",        $idUsuario, $user_name,     $idRetweet),
                        false);
                } catch (Exception $e) {
                    if ($e->getCode() == 1062) {
                        continue;
                    }
                }
            }
        // } while (count($timeline) >= 199);
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