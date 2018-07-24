<?php 
require_once("config.php");

echo "<pre>";

$files = glob("planilhas/alcolicevents/*.csv");
foreach ($files as $key => $value) {
    $value = trim($value);
    if (!in_array($value, ["planilhas/alcolicevents/parte41.csv", "planilhas/alcolicevents/parte42.csv", "planilhas/alcolicevents/parte43.csv"])) {
        continue;
    }
    echo $value . PHP_EOL;
        
    if (($handle = fopen($value, "r")) !== FALSE) {
        $cont = 0;
        while (($data = fgetcsv($handle, 2000, ";")) !== FALSE) {
            if ($cont != 0) {
                if ($data[8] > 0) {
                    try {
                        insert("semantic_tweets_alcolic", array("id", "tweet", "link", "drunk"), array($data[8], $data[4], $data[9], "N"), false);                        
                    } catch (Exception $e) {
                        if ($e->getCode() == 1062) {
                            continue;
                        }
                    }
                } else {
                    $aTmp = explode("/", $data[count($data) - 1]);
                    if ($aTmp[count($aTmp) - 1] > 0) {
                        try {
                            insert("semantic_tweets_alcolic", array("id", "tweet", "link", "drunk"), array($aTmp[count($aTmp) - 1], $data[4], $data[count($data) - 1], "N"), false);
                        } catch (Exception $e) {
                            if ($e->getCode() == 1062) {
                                continue;
                            }
                        }
                    } else {
                        debug($value);
                        var_dump($data);
                       die;
                    }
                }
            }
            $cont++;
        }
    }
}

die;

if (($handle = fopen("planilhas/amazonpartecompleto.csv", "r")) !== FALSE) {
	$cont = 1;

    while (($data = fgetcsv($handle, 2000, ";")) !== FALSE) {
        if ($cont != 0) {
            if ($data[8] > 0) {
	    	//  insert("semantic_tweets_alcolic", array("id", "tweet", "link"), array($data[8], $data[4], $data[9]));
            } else {
                var_dump($data);
                //insert("semantic_tweets", array("id", "tweet", "link"), array($data[9], $data[4] . ";" . $data[5], $data[10]));
            }
	    }
    	$cont++;
    	if ($cont > 50) {
    		break;
    	}
    }
}

?>