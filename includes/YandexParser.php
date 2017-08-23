<?php 

	require_once "SimpleHtmlDom.php";

	class YandexParser
	{
		private $searchUrl = "https://yandex.ru/search/";

		private function __construct()
		{

		}

		public function parseData($requestData)
		{
			$parsedData = array();

			$searchUrl = $this->makeSearchUrl($requestData);

			//$searchHtml = $this->getYandexSearchResultHtml($searchUrl);

			$searchResultHtml = file_get_contents("files/search_result.html");

			$parsedData = $this->parseResultHtml($searchResultHtml);

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

		private function getYandexSearchResultHtml($url)
		{
    		$ch = curl_init();
           // $cookie_file = "cookie.txt";
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
            //curl_setopt($ch, CURLOPT_HEADER, 1);
            //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
			//curl_setopt($ch, CURLOPT_PROXY, '85.26.146.169:80');
            //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
                'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:52.0) Gecko/20100101 Firefox/52.0',
                'Host: yandex.ua'
            ));    

            $html = htmlentities(curl_exec($ch));
            curl_close($ch);

            return $html;
		}

		private function parseResultHtml($resultHtml)
		{
			$result = array();
			$html = str_get_html($resultHtml);

			$resultItems = $html->find("ul.serp-list > li");

			$resultItemsCounter = 0;

			foreach($resultItems as $singleItem)
			{
				if($singleItem->role == "complementary") 
				{
					$result[$resultItemsCounter] = array();
					
					$result[$resultItemsCounter]["position"] = $resultItemsCounter + 1;
					$result[$resultItemsCounter]["title"] = strip_tags($singleItem->find("div > h2 > a", 0)->innertext);

					$result[$resultItemsCounter]["sitelinks"] = array();

					$sitelinksCounter = 0;

					foreach($singleItem->find("div.sitelinks > div > div > a") as $singleSiteLink)
					{
						$result[$resultItemsCounter]["sitelinks"][$sitelinksCounter] = array();

						$result[$resultItemsCounter]["sitelinks"][$sitelinksCounter]["name"] = $singleSiteLink->innertext;
						$result[$resultItemsCounter]["sitelinks"][$sitelinksCounter]["url"] = $singleSiteLink->href;

						$sitelinksCounter++;
					}

					$result[$resultItemsCounter]["link"] = strip_tags($singleItem->find("div > div.typo_type_greenurl", 0)->find("div a", 0)->innertext);

					$result[$resultItemsCounter]["description"] = strip_tags($singleItem->find("div > div.clearfix", 0)->find("div", 0)->innertext);

					$result[$resultItemsCounter]["phone"] = $singleItem->find("div > div.clearfix", 0)->find("div", 1)->find("div", 2)->innertext;

					$result[$resultItemsCounter]["workTime"] = $singleItem->find("div > div.clearfix", 0)->find("div", 1)->find("div", 3)->innertext;

					$resultItemsCounter++;
				}
			}

			return $result;
		}

		private function makeSearchUrl($requestInfo)
		{
			$resultUrl = "";

			$resultUrl = $this->searchUrl . "?";

			$resultUrl .= "text=" . $requestInfo["key"] . "&";
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