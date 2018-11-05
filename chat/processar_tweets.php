<?php 

require_once("../config.php");

set_time_limit(290);

$tweets = query("SELECT * FROM chat_tweets WHERE processado = 0 LIMIT 5000");

$ind = 0;
foreach (getRows($tweets) as $key => $value) {
    //Emoticons
    //Remoção alongamentos
    //Text embeddings
    //Remoção URL
    //Hashtags

    try {
        $hashTags = array();
        $users = array();
        $urls = array();
        $media = array();

        if ($value["situacao"] == 2) {
            $textoOriginal = $value["textoOriginal"];
            $texto = $value["textoOriginal"];

            $hashTags = extrairHashtags($texto);
            clear($texto);
        } else {
            $tweet = json_decode($value["tweetResource"]);
            $textoOriginal = $tweet->text;
            $texto = $tweet->text;
            
            if (isset($tweet->entities->hashtags)) {
                foreach ($tweet->entities->hashtags as $key => $hashTag) {
                    $hashTags[] = "#" . $hashTag->text;
                }
                if ($hashTags) {
                    $texto = str_ireplace($hashTags, "", $texto);
                }
            }

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

            $data = date("Y-m-d H:i:s", strtotime($tweet->created_at));
            $dataConvertida = date("Y-m-d H:i:s", strtotime("-4 hours", strtotime($tweet->created_at)));
            $diaSemana = date("D", strtotime($dataConvertida));
            $hora = date("H", strtotime($dataConvertida));
        }

        // CHECK POLARIETY EMOTICONS
        $totalPositivo = 0;
        $totalNegativo = 0;
        if (checkEmoji2($textoOriginal)) {
            $emoticons = getEmoticons($textoOriginal);
            foreach ($emoticons as $key => $emonn) {
                $retorno = getPolaridadeEmoticon($emonn);
                if ($retorno["polarity"] > 0) {
                    $totalPositivo += $retorno["polarity"];
                    $qtdPos++;
                } else {
                    $totalNegativo += $retorno["polarity"];
                    if ($retorno["name"] != null) {
                        $qtdNeg++;
                    }
                }
                $possuiEmoticon = 1;
            }
        }

        //remover alongamentos       
        while (preg_match('/(.)\\1{2}/', $texto)) {
            $texto = preg_replace('/(.)\\1{2}/', '$1$1', $texto);
        }

        $fieldsAtualizado = ["processado" => 1, "textParser" => $texto, "hashtags" => implode(",", $hashTags)];
        // , "emoticonPos" => $totalPositivo, "emoticonNeg" => $totalNegativo

        if ($value["situacao"] == 1) {
            // $fieldsAtualizado["textoOriginal"] = $textoOriginal;
            $fieldsAtualizado["diaSemana"] = $diaSemana;
            $fieldsAtualizado["hora"] = $hora;
            $fieldsAtualizado["data"] = $dataConvertida;
        }

        update("chat_tweets", $value["id"], $fieldsAtualizado);
    } catch (Exception $e) {
        debug("ERRO");
        debug($e->getMessage());        
    }
    $ind++;
}

function extrairHashtags(&$texto) {
    // $re = '/(\s|^|\(|\[)\#\S+/';
    $re = '/(?<!\w)#\w+/';
    
    preg_match_all($re, $texto, $hashtagsSuja, PREG_SET_ORDER, 0);

    $texto = preg_replace($re, "", $texto);
    // $texto = preg_replace('/([\s])\1+/', ' ', $texto);
    $texto = preg_replace('/\s+(?=\s)/', '', $texto);
    $texto = trim($texto);

    // $hashtags = [];
    // foreach ($hashtagsSuja as $valueMaior) {
    //     foreach ($valueMaior as $value) {
    //         $value = trim($value);
    //         if (in_array($value, ["", "(", "]"])) {
    //             continue;
    //         }
    //         $value = str_replace("(", "", $value);
    //         $value = str_replace(")", "", $value);
    //         $value = str_replace("[", "", $value);
    //         $value = str_replace("]", "", $value);
    //         if (!in_array($value, $hashtags)) {
    //             $hashtags[] = $value;
    //         }
    //     }
    // }

    $hashtags = [];
    foreach ($hashtagsSuja as $valueMaior) {
        foreach ($valueMaior as $value) {
            if (!in_array($value, $hashtags)) {
                $hashtags[] = $value;
            }
        }
    }
    return $hashtags;
}

function clear(&$texto) {
    $re = '/(?<!\w)@\w+/';
    $texto = preg_replace($re, "", $texto);
    $regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
    $texto = preg_replace($regex, "", $texto);
    $texto = preg_replace('/\s+(?=\s)/', '', $texto);
}


function checkEmoji($str) 
{
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    preg_match($regexEmoticons, $str, $matches_emo);
    if (!empty($matches_emo[0])) {
        return false;
    }
    
    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    preg_match($regexSymbols, $str, $matches_sym);
    if (!empty($matches_sym[0])) {
        return false;
    }

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    preg_match($regexTransport, $str, $matches_trans);
    if (!empty($matches_trans[0])) {
        return false;
    }
   
    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    preg_match($regexMisc, $str, $matches_misc);
    if (!empty($matches_misc[0])) {
        return false;
    }

    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    preg_match($regexDingbats, $str, $matches_bats);
    if (!empty($matches_bats[0])) {
        return false;
    }

    return true;
}

function getEmoticons($str) {
    $emoticons = array();
    preg_match_all('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', $str, $extrat);
    return $extrat[0];
}

function checkEmoji2($str){
    if ($str != removeEmoji($str))  {
        return true;
    }
    return false;
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

function getPolaridadeEmoticon($emoticon) {
    $emot = '[{"name": "100", "emoji": "💯", "polarity": 3 }, {"name": "angry", "emoji": "😠", "polarity": -3 }, {"name": "anguished", "emoji": "😧", "polarity": -3 }, {"name": "astonished", "emoji": "😲", "polarity": 2 }, {"name": "black_heart", "emoji": "🖤", "polarity": 3 }, {"name": "blue_heart", "emoji": "💙", "polarity": 3 }, {"name": "blush", "emoji": "😊", "polarity": 2 }, {"name": "broken_heart", "emoji": "💔", "polarity": -3 }, {"name": "clap", "emoji": "👏", "polarity": 3 }, {"name": "clown_face", "emoji": "🤡", "polarity": 0 }, {"name": "cold_sweat", "emoji": "😰", "polarity": -2 }, {"name": "confounded", "emoji": "😖", "polarity": -2 }, {"name": "confused", "emoji": "😕", "polarity": -2 }, {"name": "cowboy_hat_face", "emoji": "🤠", "polarity": 2 }, {"name": "crossed_fingers", "emoji": "🤞", "polarity": 2 }, {"name": "cry", "emoji": "😢", "polarity": -2 }, {"name": "crying_cat_face", "emoji": "😿", "polarity": -2 }, {"name": "cupid", "emoji": "💘", "polarity": 3 }, {"name": "disappointed", "emoji": "😞", "polarity": -2 }, {"name": "disappointed_relieved", "emoji": "😥", "polarity": -1 }, {"name": "dizzy_face", "emoji": "😵", "polarity": -1 }, {"name": "drooling_face", "emoji": "🤤", "polarity": 0 }, {"name": "expressionless", "emoji": "😑", "polarity": 0 }, {"name": "face_with_head_bandage", "emoji": "🤕", "polarity": -2 }, {"name": "face_with_thermometer", "emoji": "🤒", "polarity": -1 }, {"name": "fearful", "emoji": "😨", "polarity": -2 }, {"name": "flushed", "emoji": "😳", "polarity": -2 }, {"name": "frowning", "emoji": "😦", "polarity": -1 }, {"name": "frowning_face", "emoji": "☹️", "polarity": -2 }, {"name": "fu", "emoji": "🖕", "polarity": -4 }, {"name": "ghost", "emoji": "👻", "polarity": -1 }, {"name": "gift_heart", "emoji": "💝", "polarity": 3 }, {"name": "green_heart", "emoji": "💚", "polarity": 3 }, {"name": "grimacing", "emoji": "😬", "polarity": -2 }, {"name": "grin", "emoji": "😁", "polarity": 2 }, {"name": "grinning", "emoji": "😀", "polarity": 2 }, {"name": "handshake", "emoji": "🤝", "polarity": 1 }, {"name": "heart", "emoji": "❤️", "polarity": 3 }, {"name": "heart_eyes", "emoji": "😍", "polarity": 3 }, {"name": "heart_eyes_cat", "emoji": "😻", "polarity": 3 }, {"name": "heartbeat", "emoji": "💓", "polarity": 3 }, {"name": "heartpulse", "emoji": "💗", "polarity": 3 }, {"name": "hugs", "emoji": "🤗", "polarity": 2 }, {"name": "hushed", "emoji": "😯", "polarity": -1 }, {"name": "imp", "emoji": "👿", "polarity": -4 }, {"name": "innocent", "emoji": "😇", "polarity": 3 }, {"name": "joy", "emoji": "😂", "polarity": 3 }, {"name": "joy_cat", "emoji": "😹", "polarity": 3 }, {"name": "kiss", "emoji": "💋", "polarity": 2 }, {"name": "kissing", "emoji": "😗", "polarity": 2 }, {"name": "kissing_cat", "emoji": "😽", "polarity": 2 }, {"name": "kissing_closed_eyes", "emoji": "😚", "polarity": 2 }, {"name": "kissing_heart", "emoji": "😘", "polarity": 3 }, {"name": "kissing_smiling_eyes", "emoji": "😙", "polarity": 2 }, {"name": "laughing", "emoji": "😆", "polarity": 1 }, {"name": "lips", "emoji": "👄", "polarity": 2 }, {"name": "lying_face", "emoji": "🤥", "polarity": -2 }, {"name": "mask", "emoji": "😷", "polarity": -1 }, {"name": "money_mouth_face", "emoji": "🤑", "polarity": 0 }, {"name": "nauseated_face", "emoji": "🤢", "polarity": -2 }, {"name": "nerd_face", "emoji": "🤓", "polarity": -1 }, {"name": "neutral_face", "emoji": "😐", "polarity": 0 }, {"name": "no_mouth", "emoji": "😶", "polarity": 0 }, {"name": "ok_hand", "emoji": "👌", "polarity": 2 }, {"name": "open_mouth", "emoji": "😮", "polarity": -2 }, {"name": "pensive", "emoji": "😔", "polarity": -1 }, {"name": "persevere", "emoji": "😣", "polarity": -2 }, {"name": "pouting_cat", "emoji": "😾", "polarity": -4 }, {"name": "pray", "emoji": "🙏", "polarity": 1 }, {"name": "punch", "emoji": "👊", "polarity": -1 }, {"name": "purple_heart", "emoji": "💜", "polarity": 3 }, {"name": "rage", "emoji": "😡", "polarity": -4 }, {"name": "raised_hands", "emoji": "🙌", "polarity": 4 }, {"name": "relaxed", "emoji": "☺️", "polarity": 2 }, {"name": "relieved", "emoji": "😌", "polarity": 2 }, {"name": "revolving_hearts", "emoji": "💞", "polarity": 3 }, {"name": "rofl", "emoji": "🤣", "polarity": 4 }, {"name": "roll_eyes", "emoji": "🙄", "polarity": -1 }, {"name": "scream", "emoji": "😱", "polarity": -3 }, {"name": "scream_cat", "emoji": "🙀", "polarity": -3 }, {"name": "shit", "emoji": "💩", "polarity": -3 }, {"name": "skull", "emoji": "💀", "polarity": -2 }, {"name": "skull_and_crossbones", "emoji": "☠️", "polarity": -2 }, {"name": "sleeping", "emoji": "😴", "polarity": 0 }, {"name": "sleepy", "emoji": "😪", "polarity": 0 }, {"name": "slightly_frowning_face", "emoji": "🙁", "polarity": -1 }, {"name": "slightly_smiling_face", "emoji": "🙂", "polarity": 1 }, {"name": "smile", "emoji": "😄", "polarity": 2 }, {"name": "smile_cat", "emoji": "😸", "polarity": 2 }, {"name": "smiley", "emoji": "😃", "polarity": 2 }, {"name": "smiley_cat", "emoji": "😺", "polarity": 2 }, {"name": "smiling_imp", "emoji": "😈", "polarity": -3 }, {"name": "smirk", "emoji": "😏", "polarity": 2 }, {"name": "smirk_cat", "emoji": "😼", "polarity": 2 }, {"name": "sneezing_face", "emoji": "🤧", "polarity": -2 }, {"name": "sob", "emoji": "😭", "polarity": -3 }, {"name": "sparkling_heart", "emoji": "💖", "polarity": 3 }, {"name": "stuck_out_tongue", "emoji": "😛", "polarity": 1 }, {"name": "stuck_out_tongue_closed_eyes", "emoji": "😝", "polarity": 0 }, {"name": "stuck_out_tongue_winking_eye", "emoji": "😜", "polarity": -1 }, {"name": "sunglasses", "emoji": "😎", "polarity": 1 }, {"name": "sweat", "emoji": "😓", "polarity": -1 }, {"name": "sweat_smile", "emoji": "😅", "polarity": 2 }, {"name": "thinking", "emoji": "🤔", "polarity": -1 }, {"name": "thumbsdown", "emoji": "👎", "polarity": -2 }, {"name": "thumbsup", "emoji": "👍", "polarity": 2 }, {"name": "tired_face", "emoji": "😫", "polarity": -2 }, {"name": "triumph", "emoji": "😤", "polarity": 0 }, {"name": "two_hearts", "emoji": "💕", "polarity": 3 }, {"name": "unamused", "emoji": "😒", "polarity": -2 }, {"name": "upside_down_face", "emoji": "🙃", "polarity": 0 }, {"name": "v", "emoji": "✌️", "polarity": 2 }, {"name": "weary", "emoji": "😩", "polarity": -2 }, {"name": "wink", "emoji": "😉", "polarity": 3 }, {"name": "worried", "emoji": "😟", "polarity": -3 }, {"name": "yellow_heart", "emoji": "💛", "polarity": 3 }, {"name": "yum", "emoji": "😋", "polarity": 3 }, {"name": "zipper_mouth_face", "emoji": "🤐", "polarity": -1 }]';
    $emot = json_decode($emot, true);

    foreach ($emot as $key => $emo) {
        if ($emo["emoji"] == $emoticon) {
            return $emo;
        }
    }
    return array("polarity" => 0, "name" => null);
}
?>
?>