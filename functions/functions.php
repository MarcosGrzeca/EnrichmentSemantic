<?php
ini_set('display_errors', 1);
require_once(dirname(__FILE__) . '/../twitter-api-php/TwitterAPIExchange.php');

function getSettings() {
    return array(
        'oauth_access_token' => "3437475154-b7dqH1B8IK6cnt0Rno19up0xbGESM3NqRonw3U5",
        'oauth_access_token_secret' => "RgYHHYN8pqslPdNsyCgBNPbYCgymvNIeDDZpjRG5kKmpm",
        'consumer_key' => "4erfQe1KH67cmSk8ohVusPaWN",
        'consumer_secret' => "w73tMbfBABA3Zty0S8mA4JIqX6YRu2oCSdEuZQcMgTO138LtZI"
        );
}

function getTweetById($id) {
    $url = 'https://api.twitter.com/1.1/statuses/show.json';
    $getfield = '?id=' . $id;
    $requestMethod = 'GET';
    $twitter = new TwitterAPIExchange(getSettings());
    return $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();

}

function read_file($local_file) {
    $linhas = array();
    $file = fopen($local_file, "r");
    while(!feof($file)) {
        $linhas[] = fread($file, 1024);
        flush();
        sleep(1);
    }
    fclose($file);
    return $linhas;
}

function read_file_csv($local_file, $separator = ";") {
    $linhas = array();
    $file = fopen($local_file, "r");

    $ind = 0;
    while($data = fgetcsv($file, 5000, "$separator")) {
        $linhas[] = $data;
        $ind++;
        if ($ind > 100) {
            //break;
        }
    }
    fclose($file);
    return $linhas;
}

function query($sql, $die = true) {
    try {
        $mys = Connection::get()->query($sql);
        if ($mys) {
            return $mys;
        } else {
            debug($sql);
            throw new Exception(Connection::get()->error, mysqli_errno(Connection::get()));
        }
    } catch (Exception $e) {
        if ($die) {
            debug("CATCH");
            debug($sql);

            var_export($sql);
            die($e->getMessage());
        } else {
            throw $e;
        }
        
    }
}

function getRows($result) {
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getNumRows($result) {
    return $result->num_rows;
}

function debug($param, $tipo = "") {
    if ($tipo == "I") {
        ChromePhp::info($param);
    } else {
        ChromePhp::log($param);
    }
}

function escape($palavra) {
    return mysqli_real_escape_string(Connection::get(), $palavra);
}

function estatistica($ary, $operacao, $tipo,$formato = 5){

        // $operacao

            // 1 = media
            // 2 = mediana
            // 3 = 1° quartil
            // 4 = 3° quartil


        // $tipo

            // 1 = 'auto'   = Verifica automaticamento qual opção se encaixa (usar quando não se sabe o numero total de indices da matriz)
            // 2 = 'normal' = SE o elemento central é 1 elemento (resto da divisao = 1)
            // 3 = 'duplo'  = SE o elemento central são 2 elementos (resto da divisao = 0)


        // $formato

            // Opção da quantidade da casas decimais no resultado



        // Ordena os valores do array
    sort($ary);

        // Total de indices do array 
    $arySize = sizeof($ary); 

        // Soma os elementos do array
    $arySoma = array_sum($ary); 

        // Média aritmética
    $media = ($arySoma / $arySize);



        //     MEDIANA (elemento central da matriz)
        //  Nesse ponto trabalhamos com as chaves da matriz e não com os valores
        //  Depois de achada a chave central ai sim é retornado o valor para o $resultado
    switch($tipo){
            case 1: // 1 = AUTO(verifica se o numero de indices da matriz é par ou ímpar)
            if($arySize % 2 == 1){
                    $central = $arySize / 2; // como entrou aqui é porque é ímpar, divide o numero total / 2
                    $central = $central - 0.5; // como os indices da matriz só podem ser decimais(inteiros), e o resultado nesse caso será um float, retira-se 0.5 
                    $mediana = $ary[$central]; // passa o valor da matriz contido no indice central
                }
                else {
                    $central = $arySize / 2; // nesse caso o total de indices da matriz é par
                    $x1 = $central--; // pega um indice abaixo do indice central
                    $x2 = $central++; // pega um indice acima do indice central
                    $mediana = ($ary[$x1] + $ary[$x2]) / 2; // soma o VALOR contido em cada indice e divide / 2
                }
                break;
            // Os cálculos abaixo são iguais aos realizados acima porém permitindo a escolha do TIPO que se deseja 
            // 2 = NORMAL(para matrizes com total de indices ímpar)    
                case 2: 
                $central = $arySize / 2;
                $central = $central - 0.5;
                $mediana = $ary[$central];
                break;
            // 3 = DUPLO(para matrizes com total de indices par)    
                case 3:
                $central = $arySize / 2;
                $x1 = $central--;
                $x2 = $central++;
                $mediana = ($ary[$x1] + $ary[$x2]) / 2;
                break;
                
            }


        // 1° QUARTIL (elemento central está entre o INICIO e o MEIO da matriz)
        // usa-se o mesmo princípio de cálculo da mediana
            switch($tipo){
                case 1:
                if($central % 2 == 1){
                    $q1Central = $central / 2;
                    $q1Central = $q1Central - 0.5;
                    $q1 = $ary[$q1Central];
                }
                else {
                    $q1Central = $central / 2;
                    $x1 = $q1Central--;
                    $x2 = $q1Central++;
                    $q1 = ($ary[$x1] + $ary[$x2]) / 2;
                }
                break;
                
                case 2:
                $q1Central = $central / 2;
                $q1Central = $q1Central - 0.5;
                $q1 = $ary[$q1Central];
                break;

                case 3:
                $q1Central = $central / 2;
                $x1 = $q1Central--;
                $x2 = $q1Central++;
                $q1 = ($ary[$x1] + $ary[$x2]) / 2;
                break;
            }


        // 3° QUARTIL (elemento central entre o MEIO e o FINAL da matriz)
        // usa-se o mesmo princípio de cálculo da mediana
            switch($tipo){
                case 1;
                if($central % 2 == 1){
                    $q3Size = $central / 2;
                    $q3Central = $arySize - $q3Size;
                    $q3Central = ($q3Central+1) - 0.5;
                    $q3 = $ary[$q3Central];
                }
                else {
                    $q3Size = $central / 2;
                    $q3Central = $arySize - $q3Size;
                    $x1 = $q3Central--;
                    $x2 = $q3Central++;
                    $q3 = ($ary[$x1] + $ary[$x2]) / 2;
                }
                break;

                case 2:
                $q3Size = $central / 2;
                $q3Central = $arySize - $q3Size;
                $q3Central = ($q3Central+1) - 0.5;
                $q3 = $ary[$q3Central];
                break;
                
                case 3:
                $q3Size = $central / 2;
                $q3Central = $arySize - $q3Size;
                $x1 = $q3Central--;
                $x2 = $q3Central++;
                $q3 = ($ary[$x1] + $ary[$x2]) / 2;
                break;
            }

        // RESULTADOS
        // Retorna o resultado de acordo com tipo passado para função
            switch($operacao){

                case 1:
                $resultado = number_format($media,$formato);
                break;

                case 2:
                $resultado = number_format($mediana,$formato);
                break;
                
                case 3:
                $resultado = number_format($q1,$formato);
                break;
                
                case 4:
                $resultado = number_format($q3,$formato);
                break;

            }
            return $resultado;
        }


function converterEnconding($xmlE){
   $encoding[0] = "UTF-8";
   $encoding[1] = "Windows-1252";
   $encoding[2] = "ISO-8859-1";
   $encoding[3] = "ISO-8859-15";
   $encoding[4] = "Windows-1251";
   
   if (mb_detect_encoding($xmlE, $encoding) != "UTF-8"){
        $xmlE = utf8_encode($xmlE);
   }
   return $xmlE;
}

function insert($table, $headers, $values, $die = true) {
    $valores = array();
    foreach ($values as $key => $value) {
        $valores[] = "'" . escape($value) . "'";
    }
    $sql = "INSERT INTO `" . $table . "` (" . implode(",", $headers) . ") VALUES (" . implode(",", $valores) . ")";
    query($sql, $die);
}

function update($table, $id, $values) {
    $valores = array();
    $sql = "UPDATE `" . $table . "` SET ";
    $primeiro = true;
    foreach ($values as $key => $value) {
        if (!$primeiro) {
            $sql .= ", ";
        } else {
            $primeiro = false;
        }
        $sql .= $key . " = '" . escape($value) . "'";
    }
    $sql .= " WHERE id = '" . $id . "';";
    query($sql);
}

function fatalError($texto) {

}

function estaAtivo($nome) {
    $tweets = query("SELECT * FROM painel WHERE nome = '" . escape($nome) . "' AND ativo = 1");
    if (getNumRows($tweets)) {
        return true;
    }
    die("Painel inativo");    
}