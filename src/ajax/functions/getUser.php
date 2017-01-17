<?php

    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    require('../../include/bootstrap.inc.php');

    if(empty($_GET['idNumber']) || empty($_GET['sessionID'])) {
        die('!: Invalid request!');
    }

    set_error_handler(function() {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><User></User>');
        $xml->addChild('error', 'The domain controller could not be reached. Check your configuration settings.');
        header('Content-type: text/xml');
        echo $xml->asXML();
        die();
    });

    $event = 0;

    //validate session ID
    $querySessionID = $pdo->prepare("SELECT * FROM `users` WHERE `sessionID` = :sID");
    $querySessionID->execute(array('sID' => $_GET['sessionID']));
    $dataSessionID = $querySessionID->fetchAll();
    if(count($dataSessionID) <= 0) {
        die('!: Access denied!');
    } else {
        $event = $dataSessionID[0]['eventID'];
    }

    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><User></User>');

    //make sure ldap integration is enabled
    if(!function_exists('ldap_connect')) {
        $xml->addChild('error', 'The PHP LDAP extension is disabled. Enable it in PHP\'s configuration.');
        header('Content-type: text/xml');
        echo $xml->asXML();
        die();
    }

    //query the domain controller
    $ds = ldap_connect(LDAP_HOST);
    $r = ldap_bind($ds, LDAP_USERNAME, LDAP_PASSWORD);
    $sr = ldap_search($ds, LDAP_DN, str_replace('##ID_NUMBER##', $_GET['idNumber'], LDAP_SEARCH));
    $data = ldap_get_entries($ds, $sr);

    //user doesn't exist
    if($data['count'] == 0) {
        $xml->addChild('error', 'The guest could not be found on the network.');
        header('Content-type: text/xml');
        echo $xml->asXML();
        die();
    }

    //user exists, echo as xml file
    $xml->addChild('idNumber', $_GET['idNumber']);
    $xml->addChild('samaccountname', $data[0]['samaccountname'][0]);
    $xml->addChild('cn', $data[0]['displayname'][0]);

    header('Content-type: text/xml');
    echo $xml->asXML();

?>