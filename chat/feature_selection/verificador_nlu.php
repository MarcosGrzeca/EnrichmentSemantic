<?php 

require_once("../../config.php");

set_time_limit(290);

$enriquecimento = ["/food and drink/beverages/alcoholic beverages/cocktails and beer", "/food and drink/beverages/alcoholic beverages", "Hashtag", "/religion and spirituality/hinduism", "/food and drink", "/health and fitness/addiction/alcoholism", "beer", "/food and drink/beverages/alcoholic beverages/wine", "alcohol", "/religion and spirituality/buddhism", "Alcoholic beverage", "/religion and spirituality/christianity", "Beer", "Drunk Pilot", "highest score", "Miley Cyrus", "Billy Ray Cyrus", "giveaway", "/law", "/society/unrest and war", "/technology and computing/mp3 and midi", "/art and entertainment/movies and tv/children.s", "/technology and computing/software", "vodka", "/art and entertainment/shows and events", "/family and parenting/children", "/law, govt and politics", "liquor", "/society/dating", "Alcohol", "/business and industrial/business operations/business plans", "Ethanol", "Vodka", "hangover", "/law, govt and politics/politics", "/food and drink/beverages/non alcoholic beverages/coffee and tea", "/style and fashion/body art", "/society/social institution/divorce", "/sports/bowling", "/sports/cricket", "/family and parenting/babies and toddlers/baby clothes", "/style and fashion", "Alcoholism", "Saint Patrick.s Day", "Brewing", "Quantity", "Russia", "Saint Patrick", "/religion and spirituality/islam", "/law, govt and politics/government", "Beer pong", "Drug addiction", "liquor store", "/food and drink/beverages/non alcoholic beverages/soft drinks", "/health and fitness/addiction/substance abuse", "bottle", "Hops", "/art and entertainment/books and literature", "Hangover", "X.media", "/education/homework and study tips", "/religion and spirituality", "/art and entertainment/shows and events/classical concert", "drugs", "/business and industrial/business operations/management/business process", "/sports/table tennis and ping.pong", "/finance/financial news", "Drinking culture", "/news", "Thanks", "/business and industrial/business operations", "Alcohol intoxication", "Public house", "Brewery", "Shamrock", "beers", "/art and entertainment/shows and events/sports event", "/technology and computing/internet technology/web search", "Irish folklore", "/art and entertainment/shows and events/festival", "/sports/baseball", "/travel/business travel", "/sports/football", "Kefir", "green beer", "Drunk", "/society/social institution/marriage", "Distilled beverage", "/finance/investing/funds", "/technology and computing/operating systems", "/society/senior living", "/health and fitness/therapy", "/business and industrial/business news", "/news/national news", "Liquor", "Beer bottle", "night", "/law, govt and politics/politics/elections/presidential elections", "Beer style", "sober", "/art and entertainment/music/music reference", "/business and industrial/construction", "/science/weather", "Remove Intoxicated Drivers", "Addiction", "drunk", "/food and drink/beverages", "/finance/bank", "/law, govt and politics/immigration", "Trump", "mention", "Person", "/society/work/unemployment"];

echo "<pre>";

foreach ($enriquecimento as $key => $value) {
	$result = query("SELECT * FROM `chat_tweets_nlp` WHERE palavra = '" . escape($value) .  "' LIMIT 1");

	if (getNumRows($result) == 0) {
		print($value);
		echo "<br/>";
	}
}