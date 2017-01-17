<?php

    require('../../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READWRITE);

    header('Content-type: text/json');

    if(empty($_POST['id'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_POST_ID'
        )));
    }

    $queryTransactions = $pdo->prepare("SELECT 1 FROM transactions WHERE event = :event AND id = :id");
    $queryTransactions->execute(array('event' => $_SESSION['event'], 'id' => $_POST['id']));
    $dataTransactions = $queryTransactions->fetchAll();

    if(count($dataTransactions) == 0) {
        die(json_encode(array(
            'error' => 'ERR_INVALID_TRANSACTION'
        )));
    }

    $queryDelete = $pdo->prepare("DELETE FROM `transactions` WHERE `event` = :event AND `id` = :id");
    $resultDelete = $queryDelete->execute(array('event' => $_SESSION['event'], 'id' => $_POST['id']));

    $queryDeleteTickets = $pdo->prepare("DELETE FROM tickets WHERE `transaction` = :id");
    $resultDeleteTickets = $queryDeleteTickets->execute(array('id' => $_POST['id']));

    if(!$resultDelete && !$resultDeleteTickets) {
        echo json_encode(array(
            'success' => false
        ));
    } else {
        echo json_encode(array(
            'success' => true
        ));
    }