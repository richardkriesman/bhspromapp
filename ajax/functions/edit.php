<?php

    require('../../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READWRITE);

    //validate inputs
    if(empty($_POST['name'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_NAME_POST'
        )));
    }
    if(empty($_POST['id-number'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_ID_NUMBER_POST'
        )));
    }
    if(empty($_POST['transaction-id'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_TRANSACTION_ID_POST'
        )));
    }

    //get event info
    $queryEvent = $pdo->prepare("SELECT * FROM `events` WHERE `id` = :id LIMIT 1");
    $queryEvent->execute(array('id' => $_SESSION['event']));
    $dataEvent = $queryEvent->fetchAll();

    //update the transaction
    $queryTransaction = $pdo->prepare("UPDATE transactions SET guestName = :guestName, idNumber = :idNumber WHERE event = :event AND id = :tId");
    $resultTransaction = $queryTransaction->execute(array('guestName' => $_POST['name'], 'idNumber' => $_POST['id-number'], 'event' => $_SESSION['event'], 'tId' => $_POST['transaction-id']));

    if($resultTransaction)
        echo json_encode(array('success' => true));
    else
        echo json_encode(array('success' => false));