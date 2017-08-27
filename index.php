<?php 

	require_once "includes/YandexParser.php";

	$yandexParser = YandexParser::getParser();


	// allocating additional memory for php 

	// $yandexParser->logger->enableLogs(false); to disable logs
	$yandexParser->logger->enableLogs(false);

	$requestData = array(
		"key" => "ремонт офисов краснодар",
		"region1" => "35",
		"region2" => "35"
	);


	$parsedData = $yandexParser->parseData($requestData);

	var_dump($parsedData);

?>