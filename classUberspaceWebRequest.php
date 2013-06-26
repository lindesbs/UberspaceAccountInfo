<?php

    class classUberspaceWebRequest
    {
        function __construct($username, $password)
        {
            $this->strCookieFile="cookie_".time().".txt";

            libxml_use_internal_errors(true);
            setlocale(LC_ALL, 'de_DE');

            $this->arrValue=array();
            $this->ch=curl_init();

            curl_setopt($this->ch, CURLOPT_POST, 1);
            curl_setopt($this->ch, CURLOPT_USERAGENT, "classUberspaceWebRequest");

            curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->strCookieFile);
            curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->strCookieFile);
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->ch, CURLOPT_HEADER, 0);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);

            $strUrl='https://uberspace.de/login';
            $strData=sprintf("login=%s&password=%s&submit=anmelden", $username, $password);

            $this->arrValue['username']=$username;

            curl_setopt($this->ch, CURLOPT_URL, $strUrl);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $strData);
        }

        function request()
        {
            $this->store=curl_exec($this->ch);

            $this->getData();
        }

        function execute($page)
        {
            curl_setopt($this->ch, CURLOPT_URL, $page);
            $this->content=curl_exec($this->ch);
        }

        function close()
        {
            curl_close($this->ch);
            if(file_exists($this->strCookieFile))
                unlink($this->strCookieFile);

            libxml_use_internal_errors(false);
        }

        function getData()
        {
            $this->execute('https://uberspace.de/dashboard/accountinfo?format=json');

            $arrData=json_decode(trim($this->content));
            
            print_r($arrData);
            echo "<hr>";

            $this->arrValue['guthaben']=$arrData->current_amount;
            $this->arrValue['wunschpreis']=$arrData->price;
            $this->arrValue['domains_webserver']=(array) $arrData->domains->web;
            $this->arrValue['domains_mailserver']=(array) $arrData->domains->mail;
            $this->arrValue['hostname']=$arrData->host->fqdn;
            $this->arrValue['ipv4']=$arrData->host->ipv4;
            $this->arrValue['username']=$arrData->login;

        }

        function floatval($strValue)
        {
            $floatValue=preg_replace("@(^[0-9]*)(\\.|,)([0-9]*)(.*)@", "\\1.\\3", $strValue);
            if(!is_numeric($floatValue))
                $floatValue=preg_replace("@(^[0-9]*)(.*)@", "\\1", $strValue);
            if(!is_numeric($floatValue))
                $floatValue=0;

            return $floatValue;
        }

    }
?>
