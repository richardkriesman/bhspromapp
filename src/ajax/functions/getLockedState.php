<?php

    require('../../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READ);

    //validate inputs
    if(empty($_POST['table'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_TABLE_POST'
        )));
    }

    //get table info
    $lockedQuery = $pdo->prepare('SELECT lockedTime FROM tables WHERE event = :event AND tableNumber = :tableNumber LIMIT 1');
    $lockedResult = $lockedQuery->execute(array('event' => $_SESSION['event'], 'tableNumber' => $_POST['table']));
    if(!$lockedResult) {
        die(json_encode(array(
            'error' => 'ERR_QUERY_FAILED:PONY'
        )));
    }
    $lockedData = $lockedQuery->fetchAll();

    //output result
    if(strtotime($lockedData[0]['lockedTime']) >= time()) {
        echo json_encode(array(
            'success' => true,
            'locked' => true
        ));
    } else {
        echo json_encode(array(
            'success' => true,
            'locked' => false
        ));
    }