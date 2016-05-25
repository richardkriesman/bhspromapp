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

    $queryToggle = $pdo->prepare("UPDATE `tables` SET `isEnabled` = 1 WHERE `event` = :event AND `tableNumber` = :tableNumber");
    $queryToggle->execute(array('event' => $_SESSION['event'], 'tableNumber' => $_POST['id']));

    if(!$queryToggle) {
        echo json_encode(array(
            'success' => false
        ));
    } else {
        echo json_encode(array(
            'success' => true
        ));
    }