<?php

class classUberspaceWebRequest
	{
		function __construct($username, $password)
		{
			$this -> arrValue = array();
			$this -> ch = curl_init();

			curl_setopt($this -> ch, CURLOPT_POST, 1);
			curl_setopt($this -> ch, CURLOPT_USERAGENT, "classUberspaceWebRequest");

			curl_setopt($this -> ch, CURLOPT_COOKIEJAR, 'cookie.txt');
			curl_setopt($this -> ch, CURLOPT_COOKIEFILE, 'cookie.txt');
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
		}

		function execute($page)
		{
			curl_setopt($this -> ch, CURLOPT_URL, $page);
			$this -> content = curl_exec($this -> ch);
		}

		function close()
		{
			curl_close($this -> ch);
		}

		function getTransactions()
		{
			$this -> execute('https://uberspace.de/dashboard/accounting');

			preg_match('#input type="text" name="price" value="(.*?)" size="3" style="text-align: center;" id="accounting_price" />#s', $this -> content, $matches);
			if (is_array($matches))
			{
				$this -> arrValue['wunschpreis'] = intval(strip_tags($matches[1]));
				preg_match('#<td id="total" class="last">(.*?)</table>#s', $this -> content, $matches);

				$this -> arrValue['guthaben'] = intval(strip_tags($matches[1]));
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

	}

?>
