<?php 


	class Logger
	{
		private $logsEnabled = true;

		public static function getInstance()
		{
			$instance = null;
			if($instance == null)
			{
				$instance = new Logger();
			}  

			return $instance;
		}

		public function enableLogs($state)
		{
			$this->logsEnabled = $state;
		}
		
		public function log($text)
		{
			if($this->logsEnabled)
			{
				echo $text . PHP_EOL;
			}
		}

		private function __construct()
		{

		}
	}

?>