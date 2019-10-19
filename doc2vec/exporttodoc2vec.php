<?php

require_once("../config.php");

$sql = "SELECT id, q1 as resposta,
					  textoParserRisadaEmoticom,
					  textEmbedding,
					  hashtags,
					  emoticonPos,
					  emoticonNeg,
					  textParser,
					  hora,
					  erroParseado as numeroErros,
					  (
						SELECT GROUP_CONCAT(DISTINCT(REPLACE(type, 'http://dbpedia.org/class/', '')))
						FROM
						  ( SELECT c.resource AS resource,
								  tn.idTweetInterno
						  FROM tweets_nlp tn
						  JOIN conceito c ON c.palavra = tn.palavra
						  WHERE c.sucesso = 1
						  UNION ALL SELECT c.resource AS resource,
										   tn.idTweetInterno
						  FROM tweets_gram tn
						  JOIN conceito c ON c.palavra = tn.palavra
						  WHERE c.sucesso = 1
						  GROUP BY 1,
								   2 ) AS louco
						 JOIN resource_type ty ON ty.resource = louco.resource
						 WHERE louco.idTweetInterno = t.idInterno
						 AND ty.type IN ('http://dbpedia.org/class/yago/Property104916342', 'http://dbpedia.org/class/yago/Manner104928903', 'http://dbpedia.org/class/yago/WikicatBeerStyles', 'http://dbpedia.org/class/yago/Attribute100024264', 'http://dbpedia.org/class/yago/Agent114778436', 'http://dbpedia.org/class/yago/Drug103247620', 'http://dbpedia.org/ontology/Beverage', 'http://dbpedia.org/class/yago/WikicatDrugs', 'http://dbpedia.org/class/yago/Carcinogen114793812', 'http://dbpedia.org/class/yago/WikicatDrugsActingOnTheNervousSystem', 'http://dbpedia.org/class/yago/WikicatIARCGroup1Carcinogens', 'http://dbpedia.org/class/yago/Substance100020090', 'http://dbpedia.org/ontology/Food', 'http://www.ontologydesignpatterns.org/ont/dul/DUL.owl#FunctionalSubstance', 'http://www.wikidata.org/entity/Q2095', 'http://dbpedia.org/class/yago/Matter100020827', 'http://dbpedia.org/class/yago/Abstraction100002137', 'http://dbpedia.org/class/yago/DrugOfAbuse103248958', 'http://dbpedia.org/class/yago/Fluid114939900', 'http://dbpedia.org/class/yago/WikicatDistilledBeverages', 'http://dbpedia.org/class/yago/Liquid114940386', 'http://dbpedia.org/class/yago/CausalAgent100007347', 'http://dbpedia.org/class/yago/Beverage107881800', 'http://dbpedia.org/class/yago/Alcohol107884567', 'http://dbpedia.org/class/yago/Food100021265', 'http://www.w3.org/2002/07/owl#Thing', 'http://umbel.org/umbel/rc/AilmentCondition', 'http://dbpedia.org/class/yago/YagoLegalActorGeo', 'http://dbpedia.org/class/yago/YagoPermanentlyLocatedEntity', 'http://dbpedia.org/class/yago/Substance100019613', 'http://www.wikidata.org/entity/Q12136', 'http://dbpedia.org/ontology/Disease', 'http://dbpedia.org/class/yago/Location100027167', 'http://dbpedia.org/class/yago/Part113809207', 'http://dbpedia.org/class/yago/AdministrativeDistrict108491826', 'http://dbpedia.org/class/yago/YagoGeoEntity', 'http://dbpedia.org/class/yago/District108552138', 'http://dbpedia.org/class/yago/WikicatEthnicGroups', 'http://dbpedia.org/class/yago/Region108630985', 'http://dbpedia.org/ontology/EthnicGroup', 'http://dbpedia.org/class/yago/WikicatEthnicGroupsInTheUnitedStates', 'http://dbpedia.org/class/yago/WikicatEthnicGroupsInCanada', 'http://www.wikidata.org/entity/Q41710', 'http://dbpedia.org/class/yago/Wine107891726', 'http://dbpedia.org/class/yago/WikicatWineStyles', 'http://dbpedia.org/class/yago/SparklingWine107893528', 'http://dbpedia.org/class/yago/WikicatSparklingWines')
					   ) AS types,
					  (
						SELECT GROUP_CONCAT(tn.palavra)
						FROM tweets_nlp tn
						WHERE tn.idTweetInterno = t.idInterno
            AND palavra IN ('/food and drink/beverages/alcoholic beverages/cocktails and beer','/food and drink/beverages/alcoholic beverages/wine','beer','/food and drink','Alcoholic beverage','wine','Beer','/science/chemistry','/health and fitness/disease/cold and flu','/shopping/gifts/party supplies','party','/art and entertainment/movies and tv/movies','alcohol','/health and fitness/addiction','/law, govt and politics/law enforcement/police','/health and fitness/addiction/alcoholism','Wine','Music video','SHOT','NEW MUSIC VIDEO','J.CLANCY','shot','LIFE','/sports/golf','/food and drink/beverages','/society/sex','/sports/tennis','Party','club','Dinosaur Bar-B-Que','Public house','/art and entertainment/music','/sports/polo','/health and fitness/addiction/substance abuse','tequila','/travel/specialty travel/vineyards','Ale','vodka','/sports/basketball','/food and drink/cuisines/mexican cuisine','/art and entertainment/movies and tv/comedies','/art and entertainment/dance','Ethanol','Beer pong','/pets/reptiles','England','liquor','Club', '#url')
						GROUP BY tn.idTweetInterno) AS entidades
					  FROM tweets t
						WHERE textparser <> ''
						AND id <> 462478714693890048
						AND q1 IS NOT NULL
						ORDER by id";

$myfile = fopen("doc2vec.csv", "w") or die("Unable to open file!");

$tweets = query($sql);

echo getNumRows($tweets);

foreach (getRows($tweets) as $tweet) {
	// $tweet["textEmbedding"] = preg_replace('/[\n|\r|\n\r|\r\n]{2,}/',' ', $tweet["textEmbedding"]);
	// preg_replace( "/\r|\n/", "", $tweet["textEmbedding"]);
	$string = trim(preg_replace('/\s+/', ' ', $tweet["textEmbedding"]));

	$linha = ["id" => $tweet["id"], "sentiment" => $tweet["resposta"], "review" => $string];
	fputcsv($myfile, $linha, "\t");
}

fclose($myfile);