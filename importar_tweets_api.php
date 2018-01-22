<?php 

require_once("config.php");

estaAtivo("importarTweet");

$tweets = query("SELECT * FROM semantic_tweets WHERE situacao = 0 LIMIT 1000");

$ind = 0;
foreach (getRows($tweets) as $key => $value) {
    try {
        $res = getTweetById($value["id"]);
        //debug($res);
        $resultado = json_decode($res);
        if (isset($resultado->errors)) {
            $situacao = 2;
            foreach ($resultado->errors as $key => $erro) {
                if ($erro->code == "88") {
                    echo "EXCEDEU LIMITEs " . $ind;
                    die;
                    break 2;
                    //sleep(300);
                }
            }
        } else {
            $situacao = 1;
        }
        update("semantic_tweets", $value["id"], array("situacao" => $situacao, "content" => $res));
    } catch (Exception $e) {
        debug("ERRO");
        debug($e->getMessage());        
    }
    $ind++;
}
?>