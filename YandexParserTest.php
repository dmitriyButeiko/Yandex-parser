<?php 

	require_once "includes/YandexParser.php";

	$yandexParser = YandexParser::getParser();

	$requestData = array(
		"key" => "ремонт+офисов+краснодар",
		"region1" => "143",
		"region2" => null
	);


	$parsedData = $yandexParser->parseData($requestData);

	var_dump($parsedData);

?>