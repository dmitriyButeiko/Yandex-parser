<?php 

	require_once "Logger.php";

	class YandexHelper
	{
		private $cookiesFile = "cookies.txt";
		private $logger;
		private $proxyServer = "139.59.118.0:3128";

		public static function getInstance()
		{
			$instance = null;
			if($instance == null)
			{
				$instance = new YandexHelper();
			}  

			return $instance;
		}
		
		public function getSearchMainPage($url)
		{

		}

		private function getCookiesFromResponse($response)
		{
			preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
            $cookies = array();
            foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
             }
            var_dump($cookies);
		}

		private function getCookiesString()
		{
			$cookiesStringFromFile = file_get_contents($this->cookiesFile);
			return $cookiesStringFromFile;
		}

		public function searchRequest($url)
		{
			//$this->logger->log("Getting html code of page: " . $url);

    		$ch = curl_init();
			
			
			//$cookie_file = "cookiesTest.txt";
			
			
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
            //curl_setopt($ch, CURLOPT_HEADER, 1);
			//curl_setopt($ch, CURLINFO_HEADER_OUT, true);

            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
			curl_setopt($ch, CURLOPT_PROXY, $this->proxyServer);

            if($this->proxyServer)
            {
            	curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
				curl_setopt($ch, CURLOPT_PROXY, $this->proxyServer);
            }

            $cookiesString = $this->getCookiesString();

            //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            	'Host: yandex.ua',
            	'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'DNT: 1',
                'Accept-Language: en-US,en;q=0.5',
                //'Accept-Encoding: gzip, deflate, br',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'Cookie: ' . $cookiesString
            ));    


            //$html = htmlentities(curl_exec($ch));

            //$html = gzdecode(curl_exec($ch));

            $html = curl_exec($ch);

			//var_dump($html);
			
            /*$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($response)*/


            return $html;
		}

		private function __construct()
		{
			$this->logger = Logger::getInstance();
		}		
	}

?>