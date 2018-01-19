<?php
ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "3437475154-b7dqH1B8IK6cnt0Rno19up0xbGESM3NqRonw3U5",
    'oauth_access_token_secret' => "RgYHHYN8pqslPdNsyCgBNPbYCgymvNIeDDZpjRG5kKmpm",
    'consumer_key' => "4erfQe1KH67cmSk8ohVusPaWN",
    'consumer_secret' => "w73tMbfBABA3Zty0S8mA4JIqX6YRu2oCSdEuZQcMgTO138LtZI"
);

/** URL for REST request, see: https://dev.twitter.com/docs/api/1.1/ **/
$url = 'https://api.twitter.com/1.1/blocks/create.json';
$requestMethod = 'POST';

/** POST fields required by the URL above. See relevant docs as above **/
$postfields = array(
    'screen_name' => 'usernameToBlock', 
    'skip_status' => '1'
);

/** Perform a POST request and echo the response **/
$twitter = new TwitterAPIExchange($settings);
/*echo $twitter->buildOauth($url, $requestMethod)
             ->setPostfields($postfields)
             ->performRequest();*/

/** Perform a GET request and echo the response **/
/** Note: Set the GET field BEFORE calling buildOauth(); **/
/*$url = 'https://api.twitter.com/1.1/followers/ids.json';
$getfield = '?screen_name=Marcos Grzeça';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
echo $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
*/

$url = 'https://api.twitter.com/1.1/statuses/show.json';
$getfield = '?id=464519507931721728';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
echo $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();

/*
$url = 'https://api.twitter.com/1.1/geo/id/94965b2c45386f87.json';
$getfield = '?id=94965b2c45386f87';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
echo $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
*/
