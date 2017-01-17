<?php

    require('../../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READWRITE);

    header('Content-type: text/json');

    if(empty($_POST['table'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_POST_ID'
        )));
    }

    $newTime = date('Y-m-d H:i:s', time() + 3);

    $lockedQuery = $pdo->prepare("UPDATE `tables` SET `lockedTime` = :newTime WHERE `event` = :event AND `tableNumber` = :tableNumber");
    $lockedResult = $lockedQuery->execute(array('newTime' => $newTime, 'event' => $_SESSION['event'], 'tableNumber' => $_POST['table']));

    if(!$lockedResult) {
        echo json_encode(array(
            'success' => false
        ));
    } else {
        echo json_encode(array(
            'success' => true
        ));
    }