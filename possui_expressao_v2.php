<?php 

require_once("config.php");

set_time_limit(300);

$ind = 0;
do {
    $drunkTwo = array();
    $sober = array();

    $tweets = query("SELECT id, tweet, link FROM semantic_tweets_alcolic WHERE situacao = 1 AND language = 'en' AND indicativoDrunk = -1 LIMIT 1000");
    foreach (getRows($tweets) as $key => $value) {
        if (possuiExpressao($value["tweet"])) {
            $drunkTwo[] = $value["id"];
        } else {
            $sober[] = $value["id"];
        }
    }

    if (count($drunkTwo)) {
        query("UPDATE semantic_tweets_alcolic SET indicativoDrunk = 1 WHERE id IN (" . implode(",", $drunkTwo) . ");");
    }

    if (count($sober)) {
        query("UPDATE semantic_tweets_alcolic SET indicativoDrunk = 0 WHERE id IN (" . implode(",", $sober) . ");");
    }
    $ind++;
} while ($ind < 100);

function possuiExpressao($text) {
    $expressoes = getPatterns();
    foreach ($expressoes as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            return true;
        }
    }
    return false;
}

function getPatterns() {
    //Versão 1
    # return array("/\bblacked\s*out\b/i", "/\bbuzzed\b/i", "/\bcock\s*eyed\b/i", "/\bcockeyed\b/i", "/\bdesignated\s*driver\b/i", "/\bdrunk\b/i", "/\bgassed\b/i", "/\bhammered\b/i", "/\binebriated\b/i", "/\bintoxicated\b/i", "/\bjuiced\b/i", "/\bplastered\b/i", "/\bsauced\b/i", "/\bshit-faced\b/i", "/\bshwasted\b/i", "/\btipsy\b/i", "/\btrashed\b/i", "/\bunder\s*the\s*influence\b/i", "/\bwasted\b/i", "/\bzonked\b/i", "/\bzooted\b/i", "/\b#blackedout\b/i", "/\b#buzzed\b/i", "/\b#cockeyed\b/i", "/\b#cockeyed\b/i", "/\b#designateddriver\b/i", "/\b#drunk\b/i", "/\b#gassed\b/i", "/\b#hammered\b/i", "/\b#inebriated\b/i", "/\b#intoxicated\b/i", "/\b#juiced\b/i", "/\b#plastered\b/i", "/\b#sauced\b/i", "/\b#shit-faced\b/i", "/\b#shwasted\b/i", "/\b#tipsy\b/i", "/\b#trashed\b/i", "/\b#undertheinfluence\b/i", "/\b#wasted\b/i", "/\b#zonked\b/i", "/\b#zooted\b/i");

    //Versão 2
    return array("/\bbesotted\b/i", "/\bblacked\s*out\b/i", "/\bblind\s*drunk\b/i", "/\bblotto\b/i", "/\bbooze\b/i", "/\bbuzzed\b/i", "/\bcock\s*eyed\b/i", "/\bcockeyed\b/i", "/\bcrocked\b/i", "/\bdesignated\s*driver\b/i", "/\bdrink\b/i", "/\bdrunk\b/i", "/\bdrunkard\b/i", "/\bfuddle\b/i", "/\bfuddled\b/i", "/\bgassed\b/i", "/\bhammered\b/i", "/\bhit\s*it\s*up\s*\b/i", "/\bhit\s*the\s*bottle\b/i", "/\binebriate\b/i", "/\binebriated\b/i", "/\bintoxicate\b/i", "/\bintoxicated\b/i", "/\bjuiced\b/i", "/\bloaded\b/i", "/\bpie-eyed\b/i", "/\bpissed\b/i", "/\bpixilated\b/i", "/\bplastered\b/i", "/\bpotty\b/i", "/\bripped\b/i", "/\brummy\b/i", "/\bsauced\b/i", "/\bshit-faced\b/i", "/\bshwasted\b/i", "/\bslopped\b/i", "/\bsloshed\b/i", "/\bsmashed\b/i", "/\bsoak\b/i", "/\bsoaked\b/i", "/\bsot\b/i", "/\bsouse\b/i", "/\bsoused\b/i", "/\bsozzled\b/i", "/\bsquiffy\b/i", "/\bstiff\b/i", "/\btiddly\b/i", "/\btight\b/i", "/\btipsy\b/i", "/\btope\b/i", "/\btrashed\b/i", "/\bunder\s*the\s*influence\b/i", "/\bwasted\b/i", "/\bwet\b/i", "/\bwino\b/i", "/\bzonked\b/i", "/\bzootedbesotted\b/i", "/\b#blackedout\b/i", "/\b#blinddrunk\b/i", "/\b#blotto\b/i", "/\b#booze\b/i", "/\b#buzzed\b/i", "/\b#cockeyed\b/i", "/\b#cockeyed\b/i", "/\b#crocked\b/i", "/\b#designateddriver\b/i", "/\b#drink\b/i", "/\b#drunk\b/i", "/\b#drunkard\b/i", "/\b#fuddle\b/i", "/\b#fuddled\b/i", "/\b#gassed\b/i", "/\b#hammered\b/i", "/\b#hititup\b/i", "/\b#hitthebottle\b/i", "/\b#inebriate\b/i", "/\b#inebriated\b/i", "/\b#intoxicate\b/i", "/\b#intoxicated\b/i", "/\b#juiced\b/i", "/\b#loaded\b/i", "/\b#pie-eyed\b/i", "/\b#pissed\b/i", "/\b#pixilated\b/i", "/\b#plastered\b/i", "/\b#potty\b/i", "/\b#ripped\b/i", "/\b#rummy\b/i", "/\b#sauced\b/i", "/\b#shit-faced\b/i", "/\b#shwasted\b/i", "/\b#slopped\b/i", "/\b#sloshed\b/i", "/\b#smashed\b/i", "/\b#soak\b/i", "/\b#soaked\b/i", "/\b#sot\b/i", "/\b#souse\b/i", "/\b#soused\b/i", "/\b#sozzled\b/i", "/\b#squiffy\b/i", "/\b#stiff\b/i", "/\b#tiddly\b/i", "/\b#tight\b/i", "/\b#tipsy\b/i", "/\b#tope\b/i", "/\b#trashed\b/i", "/\b#undertheinfluence\b/i", "/\b#wasted\b/i", "/\b#wet\b/i", "/\b#wino\b/i", "/\b#zonked\b/i", "/\b#zooted\b/i");

    //Versão 3
    // return array("/\b#blackedout\b/i", "/\b#buzzed\b/i", "/\b#cockeyed\b/i", "/\b#designateddriver\b/i", "/\b#drank\b/i", "/\b#drunk\b/i", "/\b#gassed\b/i", "/\b#hammered\b/i", "/\b#imdrunk\b/i", "/\b#inebriated\b/i", "/\b#intoxicated\b/i", "/\b#juiced\b/i", "/\b#plastered\b/i", "/\b#sauced\b/i", "/\b#shit-faced\b/i", "/\b#shwasted\b/i", "/\b#tipsy\b/i", "/\b#trashed\b/i", "/\b#undertheinfluence\b/i", "/\b#wasted\b/i", "/\b#zonked\b/i", "/\b#zooted\b/i", "/\bblacked\s*out\b/i", "/\bbuzzed\b/i", "/\bcock\s*eyed\b/i", "/\bcockeyed\b/i", "/\bdesignated\s*driver\b/i", "/\bdrunk\b/i", "/\bgassed\b/i", "/\bhammered\b/i", "/\binebriated\b/i", "/\bintoxicated\b/i", "/\bjuiced\b/i", "/\bplastered\b/i", "/\bsauced\b/i", "/\bshit-faced\b/i", "/\bshwasted\b/i", "/\btipsy\b/i", "/\btrashed\b/i", "/\bunder\s*the\s*influence\b/i", "/\bwasted\b/i", "/\bzonked\b/i", "/\bzooted\b/i");

    //Versão 4
    // return array("/\b#besotted\b/i", "/\b#blackedout\b/i", "/\b#blinddrunk\b/i", "/\b#blotto\b/i", "/\b#booze\b/i", "/\b#buzzed\b/i", "/\b#cockeyed\b/i", "/\b#crocked\b/i", "/\b#designateddriver\b/i", "/\b#drank\b/i", "/\b#drink\b/i", "/\b#drunk\b/i", "/\b#drunkard\b/i", "/\b#fuddle\b/i", "/\b#fuddled\b/i", "/\b#gassed\b/i", "/\b#gone\b/i", "/\b#hammered\b/i", "/\b#hititup\b/i", "/\b#hitthebottle\b/i", "/\b#imdrunk\b/i", "/\b#inebriate\b/i", "/\b#inebriated\b/i", "/\b#intoxicate\b/i", "/\b#intoxicated\b/i", "/\b#juiced\b/i", "/\b#loaded\b/i", "/\b#pie-eyed\b/i", "/\b#pissed\b/i", "/\b#pixilated\b/i", "/\b#plastered\b/i", "/\b#potty\b/i", "/\b#ripped\b/i", "/\b#rummy\b/i", "/\b#sauced\b/i", "/\b#shit-faced\b/i", "/\b#shwasted\b/i", "/\b#slopped\b/i", "/\b#sloshed\b/i", "/\b#smashed\b/i", "/\b#soak\b/i", "/\b#soaked\b/i", "/\b#sot\b/i", "/\b#souse\b/i", "/\b#soused\b/i", "/\b#sozzled\b/i", "/\b#squiffy\b/i", "/\b#stiff\b/i", "/\b#tiddly\b/i", "/\b#tight\b/i", "/\b#tipsy\b/i", "/\b#tope\b/i", "/\b#trashed\b/i", "/\b#undertheinfluence\b/i", "/\b#wasted\b/i", "/\b#wet\b/i", "/\b#wino\b/i", "/\b#zonked\b/i", "/\b#zooted\b/i", "/\bbesotted\b/i", "/\bblacked\s*out\b/i", "/\bblind\s*drunk\b/i", "/\bblotto\b/i", "/\bbooze\b/i", "/\bbuzzed\b/i", "/\bcock\s*eyed\b/i", "/\bcockeyed\b/i", "/\bcrocked\b/i", "/\bdesignated\s*driver\b/i", "/\bdrink\b/i", "/\bdrunk\b/i", "/\bdrunkard\b/i", "/\bfuddle\b/i", "/\bfuddled\b/i", "/\bgassed\b/i", "/\bgone\b/i", "/\bhammered\b/i", "/\bhit\s*it\s*up\s*\b/i", "/\bhit\s*the\s*bottle\b/i", "/\binebriate\b/i", "/\binebriated\b/i", "/\bintoxicate\b/i", "/\bintoxicated\b/i", "/\bjuiced\b/i", "/\bloaded\b/i", "/\bpie-eyed\b/i", "/\bpissed\b/i", "/\bpixilated\b/i", "/\bplastered\b/i", "/\bpotty\b/i", "/\bripped\b/i", "/\brummy\b/i", "/\bsauced\b/i", "/\bshit-faced\b/i", "/\bshwasted\b/i", "/\bslopped\b/i", "/\bsloshed\b/i", "/\bsmashed\b/i", "/\bsoak\b/i", "/\bsoaked\b/i", "/\bsot\b/i", "/\bsouse\b/i", "/\bsoused\b/i", "/\bsozzled\b/i", "/\bsquiffy\b/i", "/\bstiff\b/i", "/\btiddly\b/i", "/\btight\b/i", "/\btipsy\b/i", "/\btope\b/i", "/\btrashed\b/i", "/\bunder\s*the\s*influence\b/i", "/\bwasted\b/i", "/\bwet\b/i", "/\bwino\b/i", "/\bzonked\b/i", "/\bzooted\b/i");
}