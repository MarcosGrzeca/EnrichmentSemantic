<?php 

require_once("../../config.php");

set_time_limit(290);

$types = ["http://dbpedia.org/class/yago/Manner104928903", "http://dbpedia.org/class/yago/WikicatBeerStyles", "http://dbpedia.org/class/yago/Property104916342", "http://dbpedia.org/class/yago/WikicatVirtualCommunities", "http://dbpedia.org/class/yago/Gathering107975026", "http://dbpedia.org/class/yago/Community108223802", "http://dbpedia.org/ontology/RecordLabel", "http://dbpedia.org/class/yago/Attribute100024264", "http://dbpedia.org/class/yago/SocialGroup107950920", "http://dbpedia.org/class/yago/Group100031264", "http://dbpedia.org/class/yago/Abstraction100002137", "http://dbpedia.org/class/yago/WikicatChildActors", "http://dbpedia.org/class/yago/WikicatPeopleFromNashville", "http://dbpedia.org/class/yago/WikicatMusiciansFromNashville", "http://dbpedia.org/class/yago/WikicatAmericanChildActresses", "http://dbpedia.org/class/yago/WikicatAmericanChildActors", "http://dbpedia.org/class/yago/WikicatHollywoodRecordsArtists", "http://dbpedia.org/class/yago/WikicatWaltDisneyRecordsArtists", "http://dbpedia.org/class/yago/WikicatAmericanVoiceActresses", "http://dbpedia.org/class/yago/WikicatActressesFromNashville", "http://dbpedia.org/class/yago/WikicatFascinationRecordsArtists", "http://dbpedia.org/class/yago/WikicatChildPopMusicians", "http://dbpedia.org/class/yago/WikicatActorsFromTennessee", "http://dbpedia.org/class/yago/WikicatActressesFromTennessee", "http://dbpedia.org/class/yago/WikicatMusiciansFromTennessee", "http://dbpedia.org/class/yago/WikicatPeopleFromTennessee", "http://dbpedia.org/class/yago/WikicatAmericanPianists", "http://dbpedia.org/class/yago/WikicatAmericanTelevisionActors", "http://dbpedia.org/class/yago/Pianist110430665", "http://dbpedia.org/class/yago/WikicatAmericanVoiceActors", "http://dbpedia.org/class/yago/WikicatAmericanChildSingers", "http://dbpedia.org/class/yago/WikicatLGBTRightsActivistsFromTheUnitedStates", "http://dbpedia.org/class/yago/WikicatAmericanFemaleDancers", "http://dbpedia.org/class/yago/WikicatAmericanActresses", "http://dbpedia.org/class/yago/Wikicat21st.centuryActresses", "http://dbpedia.org/class/yago/WikicatAmericanFemalePopSingers", "http://dbpedia.org/class/yago/WikicatAmericanGuitarists", "http://dbpedia.org/class/yago/WikicatAmericanFilmActresses", "http://dbpedia.org/class/yago/Dancer109989502", "http://dbpedia.org/class/yago/Guitarist110151760", "http://dbpedia.org/class/yago/WikicatRCARecordsArtists", "http://dbpedia.org/class/yago/WikicatAmericanDanceMusicians", "http://dbpedia.org/class/yago/WikicatAmericanTelevisionActresses", "http://dbpedia.org/class/yago/WikicatWomen", "http://dbpedia.org/class/yago/WikicatAmericanFemaleSingers", "http://dbpedia.org/class/yago/Actress109767700", "http://dbpedia.org/class/yago/Wikicat21st.centuryAmericanActresses", "http://dbpedia.org/class/yago/WikicatAmericanSingers", "http://dbpedia.org/class/yago/WikicatActors", "http://dbpedia.org/class/yago/Wikicat21st.centuryActors", "http://dbpedia.org/class/yago/WikicatEnglish.languageSingers", "http://dbpedia.org/class/yago/WikicatAmericanActors", "http://dbpedia.org/class/yago/Female109619168", "http://dbpedia.org/class/yago/Woman110787470", "http://dbpedia.org/class/yago/WikicatPopSingers", "http://dbpedia.org/class/yago/WikicatAmericanHipHopSingers", "http://dbpedia.org/class/yago/WikicatAmericanPopSingers", "http://dbpedia.org/class/yago/WikicatAmericanMusicians", "http://dbpedia.org/class/yago/Actor109765278", "http://dbpedia.org/class/yago/Wikicat21st.centuryAmericanSingers", "http://dbpedia.org/class/yago/Performer110415638", "http://dbpedia.org/class/yago/Musician110339966", "http://dbpedia.org/class/yago/Musician110340312", "http://dbpedia.org/class/yago/Singer110599806", "http://dbpedia.org/class/yago/Entertainer109616922", "http://dbpedia.org/class/yago/Artist109812338", "http://umbel.org/umbel/rc/MusicalPerformer", "http://dbpedia.org/class/yago/Militant110315837", "http://dbpedia.org/class/yago/Disputant109615465", "http://dbpedia.org/class/yago/Reformer110515194", "http://dbpedia.org/class/yago/LivingThing100004258", "http://dbpedia.org/class/yago/Organism100004475", "http://dbpedia.org/class/yago/CausalAgent100007347", "http://dbpedia.org/class/yago/Person100007846", "http://dbpedia.org/class/yago/WikicatAmericanPeople", "http://dbpedia.org/class/yago/Adult109605289", "http://schema.org/Person", "http://www.wikidata.org/entity/Q5", "http://xmlns.com/foaf/0.1/Person", "http://www.wikidata.org/entity/Q215627", "http://www.ontologydesignpatterns.org/ont/dul/DUL.owl#NaturalPerson", "http://dbpedia.org/class/yago/YagoLegalActorGeo", "http://dbpedia.org/class/yago/WikicatLivingPeople", "http://dbpedia.org/class/yago/Creator109614315", "http://dbpedia.org/class/yago/YagoLegalActor", "http://dbpedia.org/ontology/Person", "http://dbpedia.org/ontology/Agent", "http://dbpedia.org/ontology/Building", "http://dbpedia.org/class/yago/WikicatNorthAsianCountries", "http://dbpedia.org/class/yago/WikicatNortheastAsianCountries", "http://dbpedia.org/class/yago/WikicatStatesAndTerritoriesEstablishedIn862", "http://dbpedia.org/class/yago/WikicatMemberStatesOfTheCommonwealthOfIndependentStates", "http://dbpedia.org/class/yago/WikicatSlavicCountriesAndTerritories", "http://dbpedia.org/class/yago/WikicatStatesAndTerritoriesEstablishedIn1991", "http://dbpedia.org/class/yago/WikicatRussian.speakingCountriesAndTerritories", "http://dbpedia.org/class/yago/WikicatCountriesInEurope", "http://dbpedia.org/class/yago/WikicatEastAsianCountries", "http://dbpedia.org/class/yago/WikicatCentralAsianCountries", "http://umbel.org/umbel/rc/AilmentCondition", "http://dbpedia.org/ontology/Disease", "http://www.wikidata.org/entity/Q12136", "http://dbpedia.org/class/yago/WikicatFederalCountries", "http://dbpedia.org/class/yago/WikicatCountries", "http://dbpedia.org/class/yago/WikicatMemberStatesOfTheUnitedNations", "http://www.wikidata.org/entity/Q6256", "http://dbpedia.org/ontology/Country", "http://schema.org/Country", "http://dbpedia.org/class/yago/Country108544813", "http://www.w3.org/2002/07/owl#Thing", "http://dbpedia.org/class/yago/Host110187130", "http://dbpedia.org/class/yago/WikicatAmericanVideoGameDesigners", "http://dbpedia.org/class/yago/WikicatAmericanRadioProducers", "http://dbpedia.org/class/yago/WikicatPeopleFromNewYork", "http://dbpedia.org/class/yago/Executive110069645", "http://dbpedia.org/class/yago/Region108630985", "http://dbpedia.org/class/yago/AdministrativeDistrict108491826", "http://dbpedia.org/class/yago/District108552138", "http://dbpedia.org/ontology/PopulatedPlace", "http://dbpedia.org/class/yago/WikicatAmericanTelevisionHosts", "http://dbpedia.org/class/yago/WikicatPeopleFromManhattan", "http://dbpedia.org/class/yago/WikicatAmericanChiefExecutives", "http://dbpedia.org/ontology/Place", "http://dbpedia.org/class/yago/Location100027167", "http://dbpedia.org/class/yago/Employee110053808", "http://dbpedia.org/class/yago/WikicatAmericanTelevisionProducers", "http://dbpedia.org/class/yago/Businessman109882007", "http://dbpedia.org/class/yago/Businessperson109882716", "http://dbpedia.org/class/yago/Owner110388924", "http://schema.org/Place", "http://dbpedia.org/ontology/Location", "http://dbpedia.org/class/yago/Maker110284064", "http://dbpedia.org/class/yago/Manufacturer110292316", "http://dbpedia.org/class/yago/WikicatUnitedStatesFootballLeagueExecutives", "http://dbpedia.org/class/yago/WikicatTheTrumpOrganizationEmployees", "http://dbpedia.org/class/yago/WikicatTelevisionProducersFromNewYork", "http://dbpedia.org/class/yago/WikicatPeopleFromQueens", "http://dbpedia.org/class/yago/WikicatPeopleFromPalmBeach", "http://dbpedia.org/class/yago/WikicatFordhamUniversityAlumni", "http://dbpedia.org/class/yago/WikicatConspiracyTheorists", "http://dbpedia.org/class/yago/WikicatBusinessEducators", "http://dbpedia.org/class/yago/WikicatBoardGameDesigners", "http://dbpedia.org/class/yago/WikicatAmericanRealityTelevisionProducers", "http://dbpedia.org/class/yago/WikicatAmericanInvestors", "http://dbpedia.org/class/yago/WikicatAmericanGameShowHosts", "http://dbpedia.org/class/yago/WikicatAmericanFinancialLiteracyActivists", "http://dbpedia.org/class/yago/WikicatAmericanFinancialCommentators", "http://dbpedia.org/class/yago/WikicatAmericanBeautyPageantOwners", "http://dbpedia.org/class/yago/WikicatAmericanAirlineChiefExecutives", "http://dbpedia.org/class/yago/Trader110720453", "http://dbpedia.org/class/yago/Theorist110706812", "http://dbpedia.org/class/yago/StockTrader110657835", "http://dbpedia.org/class/yago/Solicitor110623354", "http://dbpedia.org/class/yago/Socialite110619409", "http://dbpedia.org/class/yago/Observer110369528", "http://dbpedia.org/class/yago/Billionaire110529684", "http://dbpedia.org/class/yago/Applicant109607280", "http://dbpedia.org/class/yago/Hotelier110187990", "http://dbpedia.org/class/yago/WikicatAmericanBillionaires", "http://dbpedia.org/class/yago/WikicatAmericanTelevisionDirectors", "http://dbpedia.org/class/yago/Financier110090020", "http://dbpedia.org/class/yago/WikicatAmericanSocialites", "http://dbpedia.org/class/yago/WikicatAmericanPoliticalFundraisers", "http://dbpedia.org/class/yago/WikicatWritersFromNewYorkCity", "http://dbpedia.org/class/yago/Fundraiser110116478", "http://dbpedia.org/class/yago/InvestmentAdviser110215815", "http://dbpedia.org/class/yago/RichPerson110529231", "http://dbpedia.org/class/yago/WikicatAmericanStockTraders", "http://dbpedia.org/class/yago/WikicatAmericanInvestmentAdvisors", "http://dbpedia.org/class/yago/WikicatAmericanHoteliers", "http://dbpedia.org/class/yago/WikicatChiefExecutives", "http://dbpedia.org/class/yago/WikicatAmericanBusinessWriters", "http://dbpedia.org/class/yago/Merchant110309896", "http://dbpedia.org/class/yago/Petitioner110420031", "http://dbpedia.org/class/yago/WikicatNewYorkMilitaryAcademyAlumni", "http://dbpedia.org/class/yago/Adviser109774266", "http://dbpedia.org/class/yago/WikicatWritersFromFlorida", "http://dbpedia.org/class/yago/WikicatAmericanFinanciers", "http://dbpedia.org/class/yago/Head110162991", "http://dbpedia.org/class/yago/Administrator109770949", "http://www.w3.org/2003/01/geo/wgs84_pos#SpatialThing", "http://dbpedia.org/class/yago/Participant110401829", "http://dbpedia.org/class/yago/Blogger109860415", "http://dbpedia.org/class/yago/WikicatAmericanRestaurateurs", "http://dbpedia.org/class/yago/WikicatAmericanBloggers", "http://dbpedia.org/class/yago/Restaurateur110524869", "http://dbpedia.org/class/yago/WikicatFast.foodChainsOfTheUnitedStates", "http://dbpedia.org/class/yago/WikicatPeopleFromNewYorkCity", "http://dbpedia.org/class/yago/WikicatParticipantsInAmericanRealityTelevisionSeries", "http://dbpedia.org/class/yago/Specialist110631941", "http://dbpedia.org/class/yago/InteriorDesigner110210648", "http://dbpedia.org/class/yago/Director110014939", "http://dbpedia.org/class/yago/WikicatRomano.BritishSaints", "http://dbpedia.org/class/yago/WikicatWritersOfCaptivityNarratives", "http://dbpedia.org/class/yago/WikicatPre.diocesanBishopsInIreland", "http://dbpedia.org/class/yago/WikicatMedievalIrishWriters", "http://dbpedia.org/class/yago/WikicatMedievalIrishSaints", "http://dbpedia.org/class/yago/WikicatIrishSaints"];

echo "<pre>";

foreach ($types as $key => $value) {
	$result = query("SELECT * FROM `resource_type` WHERE type = '" . escape($value) .  "' LIMIT 1");

	if (getNumRows($result) == 0) {
		print($value);
		echo "<br/>";
	}
}