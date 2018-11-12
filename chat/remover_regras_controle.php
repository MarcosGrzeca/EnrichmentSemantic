<?php 

require_once("../config.php");

set_time_limit(280);

$ind = 0;
do {
    $drunkTwo = array();
    $sober = array();

    $tweets = query("SELECT id, textParser FROM chat_tweets WHERE textSemPalavrasControle IS NULL LIMIT 100");
    $num = getNumRows($tweets);
    foreach (getRows($tweets) as $key => $value) {
        $textoRetornado = removerExpressao($value["textParser"]);
        update("chat_tweets", $value["id"], array("textSemPalavrasControle" => $textoRetornado));
    }
    $ind++;
    sleep(2);
} while ($num > 0);

function removerExpressao($text) {
    $expressoes = getPatterns();
    foreach ($expressoes as $pattern) {
        $text = preg_replace($pattern, "", $text);
    }
    return $text;
}

function getPatterns() {
    // return array("/\bshit\s*faced\b/i", "/\bkeg\s*beer\b/i", "/\bturn\s*up\b/i", "/\bturnt\s*up\b/i", "/\blit\s*up\b/i", "/\bpoo\s*pooed\b/i", "/\bpoo-?pooed\b/i", "/\bbar\s*hop\b/i", "/\bbeer\s*goggles\b/i", "/\btoes?\s*up\b/i", "/\bboot\s*and\s*rally\b/i", "/\bbeer\s*pong\b/i", "/\bbeer\s*belly\b/i", "/\bflip\s*cup\b/i", "/\bbud\s*light\b/i", "/\bnight\s*club\b/i", "/\bdrinking\s*games?\b/i", "/\bshit-?faced\b/i", "/\bfucked\s+up\b/i", "/\bdrunk\b/i", "/\balcohol\b/i", "/\bparty\b/i", "/\bbooze\b/i", "/\bliquor\b/i", "/\bvodka\b/i", "/\bhangover\b/i", "/\bwasted\b/i", "/\btequila\b/i", "/\bcocktail\b/i", "/\bwhiske?y\b/i", "/\bscotch\b/i", "/\brum\b/i", "/\bplastered\b/i", "/\bsloshed\b/i", "/\bhammered\b/i", "/\btrashed\b/i", "/\btipsy\b/i", "/\bbuzzed\b/i", "/\bbeer\b/i", "/\bshot\b/i", "/\bbrew\b/i", "/\bwine\b/i", "/\bbar\b/i", "/\bchampagne\b/i", "/\blager\b/i", "/\bclub\b/i", "/\bpub\b/i", "/\balcoholic\b/i", "/\bbottles?\b/i", "/\bcrown\b/i", "/\bbinge\b/i", "/\bboozy\b/i", "/\blean\b/i", "/\bhennessy\b/i", "/\bHenee\b/i", "/\bkegger\b/i", "/\bciroc\b/i", "/\bcognac\b/i", "/\byac\b/i", "/\byak\b/i", "/\bhammed\b/i", "/\b#drunk\b/i", "/\b#drank\b/i", "/\b#imdrunk\b/i");
    return array("/\bdrunk\b/i", "/\b#drunk\b/i", "/\balcohol\b/i", "/\b#alcohol\b/i", "/\bbeer\b/i", "/\b#beer\b/i", "/\bliquor\b/i", "/\b#liquor\b/i", "/\bvodka\b/i", "/\b#vodka\b/i", "/\bhangover\b/i", "/\b#hangover\b/i", "/\bbeers\b/i", "/\bliquors\b/i");
}