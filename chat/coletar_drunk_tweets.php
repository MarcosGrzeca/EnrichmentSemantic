<?php 

require_once("../config.php");

set_time_limit(290);

$tweets = query("SELECT * FROM chat_tweets WHERE situacao = 0 LIMIT 1000");

$ind = 0;
foreach (getRows($tweets) as $key => $value) {
    try {
        $res = getTweetById($value["id"]);
        debug($res);
        $resultado = json_decode($res);
        if (isset($resultado->errors)) {
            $situacao = 2;
            foreach ($resultado->errors as $key => $erro) {
                if ($erro->code == "88") {
                    echo "EXCEDEU LIMITEs " . $ind;
                    die;
                    break 2;
                }
            }
        } else {
            $situacao = 1;
        }
        update("chat_tweets", $value["id"], array("situacao" => $situacao, "tweetResource" => $res));
    } catch (Exception $e) {
        debug("ERRO");
        debug($e->getMessage());        
    }
    $ind++;
}
?>