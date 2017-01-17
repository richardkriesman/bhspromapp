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

    $queryAttendance = $pdo->prepare("UPDATE `transactions` SET `isPresent` = NOT isPresent WHERE `event` = :event AND `id` = :id");
    $queryAttendance->execute(array('event' => $_SESSION['event'], 'id' => $_POST['id']));

    $queryNewAttendance = $pdo->prepare("SELECT `isPresent` FROM `transactions` WHERE `event` = :event AND `id` = :id LIMIT 1");
    $queryNewAttendance->execute(array('event' => $_SESSION['event'], 'id' => $_POST['id']));
    $dataNewAttendance = $queryNewAttendance->fetchAll();

    if(!$queryAttendance) {
        echo json_encode(array(
            'success' => false,
            'present' => ($dataNewAttendance[0]['isPresent'] == 1 ? true : false)
        ));
    } else {
        echo json_encode(array(
            'success' => true,
            'present' => ($dataNewAttendance[0]['isPresent'] == 1 ? true : false)
        ));
    }