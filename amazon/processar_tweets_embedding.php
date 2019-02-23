<?php 

require_once("../config.php");

set_time_limit(290);

$tweets = query("SELECT * FROM tweets_amazon WHERE textEmbedding IS NULL LIMIT 500");

$ind = 0;
foreach (getRows($tweets) as $key => $value) {

    try {
        $users = array();
        $urls = array();
        $media = array();

        $tweet = json_decode($value["content"]);
        $textOriginal = $tweet->text;
        $texto = $tweet->text;
            
        if (isset($tweet->entities->user_mentions)) {
            foreach ($tweet->entities->user_mentions as $key => $user) {
                $users[] = "@" . $user->screen_name;
            }
            if ($users) {
                $texto = str_ireplace($users, "#mention", $texto);
            }
        }

        if (isset($tweet->entities->urls)) {
            foreach ($tweet->entities->urls as $key => $url) {
                $urls[] = $url->url;
            }
            if ($urls) {
                $texto = str_ireplace($urls, "#url", $texto);
            }
        }

        if (isset($tweet->entities->media)) {
            foreach ($tweet->entities->media as $key => $url) {
                $media[] = $url->url;
            }
            if ($media) {
                $texto = str_ireplace($media, "#media", $texto);
            }
        }

        //remover alongamentos       
        while (preg_match('/(.)\\1{2}/', $texto)) {
            $texto = preg_replace('/(.)\\1{2}/', '$1$1', $texto);
        }

        $fieldsAtualizado = ["textEmbedding" => $texto];
        update("tweets_amazon", $value["id"], $fieldsAtualizado);
    } catch (Exception $e) {
        debug("ERRO");
        debug($e->getMessage());        
    }
    $ind++;
}
?>