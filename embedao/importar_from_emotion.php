<?php 
require_once("../config.php");

set_time_limit(0);

echo "<pre>";

$files = glob("planilhas/emotion/*.csv");
foreach ($files as $key => $value) {
    $value = trim($value);
    echo $value . PHP_EOL;
        
    if (($handle = fopen($value, "r")) !== FALSE) {
        $cont = 0;
        while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
            if ($cont != 0) {
                try {
                    insert("embeddao", array("id", "texto", "origem"), array($data[1], $data[5], "emotion"), false);
                } catch (Exception $e) {
                    if ($e->getCode() == 1062) {
                        continue;
                    }
                }
            }
            $cont++;
        }
    }
}