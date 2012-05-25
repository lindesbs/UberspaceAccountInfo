PHP Request Class fuer Uberspace Accounts

Beispiel

	$uberspace = new classUberspaceWebRequest(USERNAME,PASSWORD);
	$uberspace->request();
	$uberspace->close();

	print_r($uberspace->arrValue);

	
Generiert ein Array, mit dem Buntzernamen, der zugeorndete Host, dem eingestellten Wunschpreis und dem Guthaben auf dem Account