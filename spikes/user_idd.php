<?php
require_once("config.php");

$tweets = query("SELECT * FROM tweets WHERE user_id = 0");

foreach (getRows($tweets) as $key => $value) {
	try {

		$usuario = json_decode($value["texto"], true);
		$tweteer = getUserById($usuario["user"]["id"]);
		$tweteer = json_decode($tweteer, true);
	    
		if (isset($tweteer["errors"])) {
			foreach ($tweteer["errors"] as $key => $erro) {
				debug("ERRO");
				debug($erro["code"]);
				if ($erro["code"] == "50" || $erro["code"] == "63") {
					//User not found - processar do tweet
					$tweteer = $usuario["user"];
					break;
				}
				if ($erro["code"] == "88") {
					echo "EXCEDEU LIMITEs " . $ind;
					//break 2;
					//sleep(300);
				}
				throw new Exception(json_encode($tweteer["errors"]), 1);
			}
		}

		$user_id = $tweteer["id"];
		$description = $tweteer["description"];
		$location = $tweteer["location"];
		$name = $tweteer["name"];
		$screen_name = $tweteer["screen_name"];
		$lang = $tweteer["lang"];
		$url = $tweteer["url"];
		$time_zone = $tweteer["time_zone"];
		$profile_image_url = $tweteer["profile_image_url"];
		$profile_image_url = str_replace("_normal", "_400x400", $profile_image_url);

		$get = "SELECT * FROM user WHERE id = " . escape($user_id);
		$ret = query($get);
		
		$num = getNumRows($ret);

		if ($num == 0) {
			$insert = "INSERT INTO `user` (id, profile_url, location, name, description, screen_name, lang, url) VALUES ('" . escape($user_id) . "', '" . escape($profile_image_url) . "', '" . escape($location) . "', '" . escape($name) . "', '"  . escape($description) . "', '"  . escape($screen_name) . "', '"  . escape($lang) . "', '"  . escape($url) . "')";
			query($insert, false);
		}

		$inser = "UPDATE tweets SET user_id = '" . escape($user_id) . "' WHERE id = '" . $value["id"] . "'";
		$ret = query($inser);
	} catch (Exception $e) {
		debug($user_id);
		debug($value["id"]);
		debug($get . " --- " . $num);
		print_r($e->getMessage());
		die;
	}
}

function getUserById($id) {
    $url = 'https://api.twitter.com/1.1/users/show.json';
    $getfield = '?user_id=' . $id;
    $requestMethod = 'GET';
    $twitter = new TwitterAPIExchange(getSettings());
    return $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();

}
?>