<?php 

require_once("../../config.php");

set_time_limit(290);

$enriquecimento = ["mention",
"Hashtag",
"url",
"/religion and spirituality/hinduism",
"/religion and spirituality/buddhism",
"Download  Pilot",
"highest score",
"Miley Cyrus",
"giveaway",
"Billy Ray Cyrus",
"/technology and computing/mp3 and midi",
"/art and entertainment/movies and tv/children's",
"/technology and computing/software",
"/food and drink",
"media",
"/religion and spirituality/christianity",
"/technology and computing/internet technology/web search",
"/health and fitness/addiction/alcoholism",
"/food and drink/beverages/alcoholic beverages/cocktails and beer",
"/sports/bowling",
"/food and drink/beverages",
"/sports/cricket",
"/food and drink/beverages/alcoholic beverages",
"/food and drink/beverages/alcoholic beverages/wine",
"Saint Patrick's Day",
"/food and drink/beverages/non alcoholic beverages/soft drinks",
"/society/dating",
"Quantity",
"Saint Patrick",
"/business and industrial/business operations/business plans",
"/society/unrest and war",
"#media",
"/food and drink/beverages/non alcoholic beverages/bottled water",
"bottle",
"/health and fitness/drugs",
"/family and parenting/children",
"mention Thanks",
"/technology and computing/internet technology/email",
"/sports/table tennis and ping-pong",
"drugs",
"/health and fitness/addiction",
"/health and fitness/addiction/smoking addiction",
"/shopping/retail/outlet stores",
"Shamrock",
"/religion and spirituality/islam",
"Irish folklore",
"https",
"/society/social institution/divorce",
"/business and industrial/advertising and marketing/advertising",
"store",
"/automotive and vehicles/minivan",
"/automotive and vehicles/boats and watercraft",
"/art and entertainment/movies and tv/movies/reviews",
"gt",
"last night",
"/art and entertainment/books and literature",
"Person",
"shots",
"/food and drink/food",
"/technology and computing/programming languages/javascript",
"Drug",
"wine",
"#mention #media",
"Starbucks",
"/law, govt and politics/politics/elections/presidential elections",
"/law, govt and politics",
"text",
"Alcoholic beverage",
"St. Patrick's Day",
];

echo "<pre>";

foreach ($enriquecimento as $key => $value) {
	$result = query("SELECT * FROM `chat_tweets_nlp` WHERE palavra = '" . escape($value) .  "' LIMIT 1");

	if (getNumRows($result) == 0) {
		print($value);
		echo "<br/>";
	}
}