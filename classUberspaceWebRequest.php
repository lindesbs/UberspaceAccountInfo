<?php

class classUberspaceWebRequest
	{
		function __construct($username, $password)
		{
			$strCookieFile = "cookie_".time().".txt";
		setlocale(LC_ALL, 'de_DE'); 
		
		
			$this -> arrValue = array();
			$this -> ch = curl_init();

			curl_setopt($this -> ch, CURLOPT_POST, 1);
			curl_setopt($this -> ch, CURLOPT_USERAGENT, "classUberspaceWebRequest");

			curl_setopt($this -> ch, CURLOPT_COOKIEJAR, $this->strCookieFile);
			curl_setopt($this -> ch, CURLOPT_COOKIEFILE, $this->strCookieFile);
			curl_setopt($this -> ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($this -> ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($this -> ch, CURLOPT_HEADER, 1);
			curl_setopt($this -> ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($this -> ch, CURLOPT_SSL_VERIFYPEER, 0);
		
			$strUrl = 'https://uberspace.de/login';
			$strData = sprintf("login=%s&password=%s&submit=anmelden", $username, $password);

			$this -> arrValue['username'] = $username;

			curl_setopt($this -> ch, CURLOPT_URL, $strUrl);
			curl_setopt($this -> ch, CURLOPT_POSTFIELDS, $strData);
		}

		function request()
		{
			$this -> store = curl_exec($this -> ch);

			$this -> getTransactions();
			$this -> getSettings();
			
			$this->close();
			
			
		}

		function execute($page)
		{
			curl_setopt($this -> ch, CURLOPT_URL, $page);
			$this -> content = curl_exec($this -> ch);
		}

		function close()
		{	
			curl_close($this -> ch);
			if (file_exists($this->strCookieFile))
				unlink($this->strCookieFile);
		}

		function getTransactions()
		{
			$this -> execute('https://uberspace.de/dashboard/accounting');
			
			$doc = new DomDocument();
			$doc->validateOnParse = true;
			$doc->loadHTML($this->content);

			$id = $doc->getElementById('accounting_price');

			if ($id!=null)
			{
				$this -> arrValue['wunschpreis'] = $this->floatval($id->getAttribute("value"));
				$idTotal = $doc->getElementById('total');
				$this -> arrValue['guthaben'] = $this->floatval($idTotal->nodeValue);
			}
		}

		function getSettings()
		{
			$this -> execute('https://uberspace.de/dashboard/datasheet');
			$strContent = nl2br($this -> content);

			preg_match('#<th>Hostname</th>(.*)uberspace.de<br />#s', $strContent, $matches);

			if (is_array($matches))
			{
				$this -> arrValue['hostname'] = trim(strip_tags($matches[1] . 'uberspace.de'));
			}

		}

		
		function floatval($strValue) 
		{ 
			$floatValue = ereg_replace("(^[0-9]*)(\\.|,)([0-9]*)(.*)", "\\1.\\3", $strValue); 
			if (!is_numeric($floatValue)) 
				$floatValue = ereg_replace("(^[0-9]*)(.*)", "\\1", $strValue); 
			if (!is_numeric($floatValue)) 
				$floatValue = 0; 
				
			return $floatValue; 
		} 
  
  
	}

?>
