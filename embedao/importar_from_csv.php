<?php 
require_once("../config.php");

set_time_limit(0);

echo "<pre>";

$files = glob("planilhas/tweetarchivist/*.csv");
foreach ($files as $key => $value) {
    $value = trim($value);
    echo $value . PHP_EOL;
        
    if (($handle = fopen($value, "r")) !== FALSE) {
        $cont = 0;
        while (($data = fgetcsv($handle, 4000, ";")) !== FALSE) {
            if ($cont != 0) {
                if ($data[5] != "en") {
                    continue;
                }
                try {
                    insert("embeddao", array("id", "texto", "origem"), array($data[0], $data[4], "archivist"), false);
                } catch (Exception $e) {
                    if ($e->getCode() == 1062) {
                        continue;
                    }
                }
            }
            $cont++;
            if ($cont > 25) {
                break;
            }
        }
    }
}