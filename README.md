PHP Request Class fuer Uberspace Accounts

Beispiel

	$uberspace = new classUberspaceWebRequest(USERNAME,PASSWORD);
	$uberspace->request();
	$uberspace->close();

	print_r($uberspace->arrValue);

	
Generiert ein Array, mit dem Buntzernamen, der zugeorndete Host, angebundene Web- und MailDomains, dem eingestellten Wunschpreis und dem Guthaben auf dem Account

	Array
	(
        [username]   =>   USERNAME
        [wunschpreis]   =>   3.00
        [guthaben]   =>   2.00
        [hostname]   =>   HOST.uberspace.de
        [domains_webserver]   =>   Array
                (
                        [0]   =>   *.DOMAIN.TLD
                        [1]   =>   DOMAIN.TLD
                        [2]   =>   *.USERNAME.HOST.uberspace.de
                )

        [domains_mailserver]   =>   Array
                (
                        [0]   =>   DOMAIN.TLD
                )

	)