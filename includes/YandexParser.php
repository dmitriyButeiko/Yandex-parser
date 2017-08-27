<?php 

	ini_set('safe_mode', false);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	require_once "SimpleHtmlDom.php";
	require_once "Logger.php";
	require_once "HttpHelper.php";
	require_once "YandexHelper.php";

	class YandexParser
	{
		private $searchUrl = "https://yandex.ru/search/";
		//private $mainUrl = "https://yandex.ru/";
		private $cookiesFile = "cookies.txt";
		//private $proxyServer = null;
		private $logsEnabled = false;
		public $logger;

		private function __construct()
		{
			$this->logger = Logger::getInstance();
			$this->yandexHelper = YandexHelper::getInstance();
		}

		public function parseData($requestData)
		{
			$parsedData = array();

			$searchUrl = $this->makeSearchUrl($requestData);

			$this->logger->log("Search url: " . $searchUrl);

			$response = $this->yandexHelper->searchRequest($searchUrl);

			$this->logger->log($response);

			//$newCookies = $this->httpHelper->getCookiesFromResponse();
			/*
				make search request 
				update cookies file
				check response
				if requests to make captcha -> 
			$searchHtml = $this->yandexHelper->searchRequest($searchUrl);
			exit;
			*/
			//$searchHtml = file_get_contents("files/search_result.html");
			
			$parsedData = $this->parseResultHtml($response);

			return $parsedData;
		}

		public static function getParser()
		{
			$instance = null;
			if($instance == null)
			{
				$instance = new YandexParser();
			}  
			return $instance;
		}
		private function updateCookies()
		{
			$this->logger->log("Start updating cookies...");

			$this->logger->log("Cookies updating finished...");
		}

		private function getCookiesString()
		{
			$cookiesStringFromFile = file_get_contents($this->cookiesFile);

			            if(strlen($cookiesString) < 2)
            {
            	$this->logger->log("Cookies file empty.");
            	//echo "Cookies file empty.";
            	//$this->
            }

			if(strlen($cookiesStringFromFile) < 4)
			{
				$this->logger->log("Cookies file empty.");

				$yandexSearchPageHtml = $this->getYandexSearchResultHtml($this->searchUrl);

				//var_dump($yandexSearchPageHtml);

			}
			else
			{
				return $cookiesStringFromFile;
			}
		}

		private function parseResultHtml($resultHtml)
		{
			$result = array();
			$html = str_get_html($resultHtml);

			$resultItems = $html->find("ul.serp-list > li");

			$resultItemsCounter = 0;

			foreach($resultItems as $singleItem)
			{
				$singleItemRole = $singleItem->role;

				//var_dump($singleItemRole);

					if($singleItemRole == "complementary") 
				    {
				    	//echo "Complementary item" . PHP_EOL;

						$result[$resultItemsCounter] = array();
					
						$result[$resultItemsCounter]["position"] = $resultItemsCounter + 1;
						$result[$resultItemsCounter]["title"] = strip_tags($singleItem->find("div > h2 > a", 0)->innertext);

						$result[$resultItemsCounter]["sitelinks"] = array();

						$sitelinksCounter = 0;

						foreach($singleItem->find("div.sitelinks > div > div > a") as $singleSiteLink)
						{
							$result[$resultItemsCounter]["sitelinks"][$sitelinksCounter] = array();

							$result[$resultItemsCounter]["sitelinks"][$sitelinksCounter]["name"] = strip_tags($singleSiteLink->innertext);
							$result[$resultItemsCounter]["sitelinks"][$sitelinksCounter]["url"] = $singleSiteLink->href;

							$sitelinksCounter++;
						}

						$result[$resultItemsCounter]["link"] = strip_tags($singleItem->find("div > div.typo_type_greenurl", 0)->find("div a", 0)->innertext);

						$result[$resultItemsCounter]["description"] = strip_tags($singleItem->find("div > div.clearfix", 0)->find("div", 0)->innertext);

						$phoneBlock = $singleItem->find("div > div.clearfix", 0)->find("div", 1);

						if($phoneBlock)
						{
							$result[$resultItemsCounter]["phone"] = $phoneBlock->find("div", 2)->innertext;
						}

						$worktimeBlock = $singleItem->find("div > 	div.clearfix", 0)->find("div", 1);

						if($worktimeBlock)
						{
							$result[$resultItemsCounter]["workTime"] = $worktimeBlock->find("div", 3)->innertext;
						}

						$resultItemsCounter++;
					}
			}

			return $result;
		}

		private function makeSearchUrl($requestInfo)
		{
			$resultUrl = "";

			$this->logger->log("Making search url...");

			$resultUrl = $this->searchUrl . "?";

			$requestInfo["key"] = str_replace(" ", "+", $requestInfo["key"]);

			$resultUrl .= ("text=" . $requestInfo["key"] . "&");
			$resultUrl .= "lr=" . $requestInfo["region1"];

			if(isset($requestInfo["region2"]) && !is_null($requestInfo["region2"]))
			{
				$resultUrl .= "&";
				$resultUrl .= "rstr=" . $requestInfo["region2"];
			}

			return $resultUrl;
		}
	}
?>