<?php 

require_once("../config.php");

set_time_limit(290);

$tweets = query("SELECT * FROM chat_tweets WHERE processado = 1 AND erros = -1 LIMIT 200");

$ind = 0;
 foreach (getRows($tweets) as $key => $value) {
    $totalErros = 0;
    $originalText = $value["textParser"];
    $texto = clear($value["textParser"]);

    try {
        $spell = spell($texto);
        $spellJSON = json_decode($spell);

        if (count($spellJSON->corrections)) {
            $totalErros = 0;
            foreach ($spellJSON->corrections as $palavraErro => $palavrasSimilares) {
                if (ehErro($palavraErro, $palavrasSimilares, $originalText)) {
                    $totalErros++;                  
                    //debug(array("erro" => true, "palavra" => $palavraErro, "similiares" => $palavrasSimilares));
                } else {
                    // replace_full($originalText, $palavraErro);
                    //$totalErros--;
                    //debug(array("erro" => false, "palavra" => $palavraErro, "similiares" => $palavrasSimilares));
                }
            }
        } else {
            //debug("TEXTO CORRETO");
        }
        update("chat_tweets", $value["id"], array("erros" => $totalErros, "jsonErros" => $spell, "textParser" => $originalText));
    } catch (Exception $e) {
        debug($e->getMessage());
    }
    if ($ind > 10) {
        //break;
    }
    $ind++;
}

function spell($text) {
  $token = "lGazgCQaIgmshqsTCM14e16ZFWoXp1eRDWOjsnvwvYEM3SUdn1";
  if (rand(0, 1)) {
    $token = "9v2CvSfHZAmshXDNOhNV3qHyQeaap1Ggt0hjsneNotKCh7n7Ja";
  }

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://montanaflynn-spellcheck.p.mashape.com/check/?text=" . urlencode($text),
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
      //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Cache-Control: no-cache",
        "X-Mashape-Key: " . $token
    ),
  ));
    
  $response = curl_exec($curl);
  $err = curl_error($curl);
  $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

  curl_close($curl);

  if ($err) {
    throw new Exception($err, 1);
  } else {
    if ($httpcode == 200) {
        return $response;
    }
    throw new Exception($response, 1);
  }
}

function replaceRisos($texto) {
    $texto = " " . $texto . " ";
    $textAnt = "";
    while ($textAnt != $texto) {
        $textAnt = $texto;
        $texto = preg_replace("/[^\w][hakeHAKE]{3,}[^\w]/", ' ', $texto);
    }
    return trim($texto);
}

function clear($texto) {
    // $texto = strtolower($texto);
    $texto = str_ireplace("#mention", "", $texto);
    $texto = str_ireplace("#url", "", $texto);
    $texto = str_ireplace("#media", "", $texto);
    $texto = str_ireplace("\n", "", $texto);
    $texto = removeEmoji($texto);
    $texto = removeTextualEmojis($texto);
    $texto = str_ireplace("?", " ", $texto);
    $texto = str_ireplace("!", " ", $texto);
    $texto = str_ireplace('"', " ", $texto);
    $texto = replaceRisos($texto);

    //Remover urls
    $regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@";
    $texto = preg_replace($regex, '', $texto);

    return trim($texto);
}

function replace_full(&$text, $error, $replacement) {
    $pattern = "/\b" . $error . "\b/i";
    if (preg_match_all($pattern, $text, $matches)) {
        if (count($matches) == 1) {
            $text = preg_replace($pattern, $replacement, $text);
        }
    }
 }

function ehErro($palavraComErro, $sugestoes = array(), &$originalText) {
    $girias = array("LOL", "OMG", "ILY", "LMAO", "WTF", "PPL", "IDK", "TBH", "BTW", "THX", "SMH", "FFS", "AMA", "FML", "TBT", "JK", "IMO", "YOLO", "ROFL", "MCM", "IKR", "FYI", "BRB", "GG", "IDC", "TGIF", "NSFW", "ICYMI", "STFU", "WCW", "IRL", "BFF", "OOTD", "FTW", "Txt", "HMU", "HBD", "TMI", "NM", "GTFO", "NVM", "DGAF", "FBF", "DTF", "FOMO", "SMFH", "OMW", "POTD", "LMS", "GTG", "ROFLMAO", "TTYL", "AFAIK", "LMK", "PTFO", "SFW", "HMB", "TTYS", "FBO", "TTYN");
    $redesSociais = array("facebook", "youtube", "whatsapp", "snapchat", "twitter", "instagram", "snapchats");

    $errosConhecidos = array("crossfit", "mardigras", "mardi", "gras", "mard", "gra");

    if (strlen($palavraComErro) <= 2) {
        return false;
    }

    // debug("ER:: " . $palavraComErro);

    if (in_array(strtoupper($palavraComErro), $girias)) {
        return false;
    }
    if (in_array(strtolower($palavraComErro), $redesSociais)) {
        return false;
    }
    if (in_array(strtolower($palavraComErro), $errosConhecidos)) {
        return false;
    }

    $palavraComErro = strtolower($palavraComErro);

    foreach ($sugestoes as $keySugestao => $sugestao) {
        $sugestao = strtolower($sugestao);

        if ($sugestao == $palavraComErro) {
            return false;
        }

        if (strlen($palavraComErro) >= strlen($sugestao)) {
            if (levenshtein($palavraComErro, $sugestao, 1, 2, 1) == 1) {
                $keyWordTwo = 0;
                $sucesso = true;

                for ($keyW = 0; $keyW < strlen($palavraComErro); $keyW++) {
                    if (isset($sugestao[$keyWordTwo]) && $palavraComErro[$keyW] == $sugestao[$keyWordTwo]) {
                        $keyWordTwo++;
                    } else if ($keyWordTwo > 0 && $palavraComErro[$keyW] == $sugestao[($keyWordTwo - 1)]) {
                    } else {
                        $sucesso = false;
                    }
                }
                if ($sucesso) {
//                  debug("CASE levenshtein(str1, str2)");
                    replace_full($originalText, $palavraComErro, $sugestao);
                    return false;
                }
            }
        }
    }
    return true;
}

function removeEmoji($text) {

    $clean_text = "";

    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);

    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text = preg_replace($regexDingbats, '', $clean_text);

    $clean_text = preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $clean_text);
    return $clean_text;
}

function removeTextualEmojis($text) {
    $textualEmojis = array("<3", "</3", ":')", ":'-)", ":D", ":-D", "=D", ":)", ":-)", "=]", "=)", ":]", "':)", "':-)", "'=)", "':D", "':-D", "'=D", ">:)", ">;)", ">:-)", ">=)", ";)", ";-)", "*-)", "*)", ";-]", ";]", ";D", ";^)", "':(", "':-(", "'=(", ":*", ":-*", "=*", ":^*", ">:P", "X-P", "x-p", ">:[", ":-(", ":(", ":-[", ":[", "=(", ">:(", ">:-(", ":@", ":'(", ":'-(", ";(", ";-(", ">.<", "D:", ":$", "=$", "#-)", "#)", "%-)", "%)", "X)", "X-)", "*\\0/*", "\\0/", "*\\O/*", "\\O/", "O:-)", "0:-3", "0:3", "0:-)", "0:)", "0;^)", "O:-)", "O:)", "O;-)", "O=)", "0;-)", "O:-3", "O:3", "B-)", "B)", "8)", "8-)", "B-D", "8-D", "-_-", "-__-", "-___-", ">:\\", ">:/", ":-/", ":-.", ":/", ":\\", "=/", "=\\", ":L", "=L", ":P", ":-P", "=P", ":-p", ":p", "=p", ":-Þ", ":Þ", ":þ", ":-þ", ":-b", ":b", "d:", ":-O", ":O", ":-o", ":o", "O_O", ">:O", ":-X", ":X", ":-#", ":#", "=X", "=x", ":x", ":-x", "=#");
    foreach ($textualEmojis as $key => $value) {
        $text = str_ireplace($value, "", $text);
    }
    return $text;
}