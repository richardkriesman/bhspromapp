<?php

    require('../../include/bootstrap.inc.php');

    session_start();

    $sort = "all";
    if(!empty($_GET['sort'])) {
        if($_GET['sort'] == "present" || $_GET['sort'] == "absent") {
            $sort = $_GET['sort'];
        }
    }

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READ);

    $querySearch = "";
    $resultSearch = true;
    if(empty($_POST['term'])) {
        if($sort == "all") {
            $querySearch = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event ORDER BY tickets."seatNumber", tickets."transaction"');
        } else if($sort == "present") {
            $querySearch = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event AND transactions.isPresent = 1 ORDER BY tickets."seatNumber", tickets."transaction"');
        } else if($sort == "absent") {
            $querySearch = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event AND transactions.isPresent = 0 ORDER BY tickets."seatNumber", tickets."transaction"');
        }
        $resultSearch = $querySearch->execute(array('event' => $_SESSION['event']));
    } else {
        if($sort == "all") {
            $querySearch = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event AND (transactions.guestName LIKE :term OR transactions.idNumber LIKE :term) ORDER BY tickets."seatNumber", tickets."transaction"');
        } else if($sort == "present") {
            $querySearch = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event AND (transactions.guestName LIKE :term OR transactions.idNumber LIKE :term) AND transactions.isPresent = 1 ORDER BY tickets."seatNumber", tickets."transaction"');
        } else if($sort == "absent") {
            $querySearch = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event AND (transactions.guestName LIKE :term OR transactions.idNumber LIKE :term) AND transactions.isPresent = 1 ORDER BY tickets."seatNumber", tickets."transaction"');
        }
        $resultSearch = $querySearch->execute(array('term' => '%'.$_POST['term'].'%', 'event' => $_SESSION['event']));
    }
    $dataSearch = $querySearch->fetchAll();

    //format guests into a pretty array
    $guests = array();
    foreach($dataSearch as $guest) {
        if(!array_key_exists($guest['transaction'], $guests)) {
            $guests[$guest['transaction']]['data'] = array();
            $guests[$guest['transaction']]['transaction'] = $guest['transaction'];
            $guests[$guest['transaction']]['idNumber'] = $guest['idNumber'];
            $guests[$guest['transaction']]['isPresent'] = $guest['isPresent'];
            $guests[$guest['transaction']]['name'] = $guest['guestName'];
            $guests[$guest['transaction']]['tableNumber'] = $guest['tableNumber'];
        }

        array_push($guests[$guest['transaction']]['data'], $guest['seatNumber']);
    }
    foreach($dataSearch as $guest) {
        $guests[$guest['transaction']]['startingSeat'] = min($guests[$guest['transaction']]['data']);
        $guests[$guest['transaction']]['endingSeat'] = max($guests[$guest['transaction']]['data']);
    }

    $output = array();
    foreach($guests as $data) {
        $seats = '';
        if($data['startingSeat'] != $data['endingSeat'])
            $seats = $data['tableNumber'] . $data['startingSeat'] . '-' . $data['tableNumber'] . $data['endingSeat'];
        else
            $seats = $data['tableNumber'] . $data['startingSeat'];

        array_push($output, array(
            'seats' => $seats,
            'guestName' => $data['name'],
            'idNumber' => $data['idNumber'],
            'isPresent' => $data['isPresent']
        ));
    }

    echo json_encode($output);