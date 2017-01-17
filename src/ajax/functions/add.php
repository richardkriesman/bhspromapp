<?php

    require('../../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READWRITE);

    $checkNumbers = array();

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
    if(empty($_POST['table-number'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_TABLE_NUMBER_POST'
        )));
    }
    if(!isset($_POST['cash-tickets'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_CASH_TICKETS_POST'
        )));
    }
    if(!isset($_POST['check-tickets'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_CHECK_TICKETS_POST'
        )));
    }
    if(!isset($_POST['free-tickets'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_FREE_TICKETS_POST'
        )));
    }
    if(!isset($_POST['check-number'])) {
        die(json_encode(array(
            'error' => 'ERR_EXPECTED_CHECK_NUMBER_POST'
        )));
    } else {
        if(!empty($_POST['check-number'])) {
            $checkNumbersRaw = base64_decode($_POST['check-number']);
            if ($checkNumbersRaw === false) {
                die(json_encode(array(
                    'error' => 'ERR_INVALID_CHECK_NUMBER:OCELOT'
                )));
            } else {
                $checkNumbers = explode('-', $checkNumbersRaw);
                foreach ($checkNumbers as $number) {
                    if (!is_numeric($number)) {
                        die(json_encode(array(
                            'error' => 'ERR_INVALID_CHECK_NUMBER:ZEBRA'
                        )));
                    }
                }
                if (count($checkNumbers) != $_POST['check-tickets']) {
                    die(json_encode(array(
                        'error' => 'ERR_MISMATCH_CHECK_NUMBER'
                    )));
                }
            }
        }
    }

    //get event info
    $queryEvent = $pdo->prepare("SELECT * FROM `events` WHERE `id` = :id LIMIT 1");
    $queryEvent->execute(array('id' => $_SESSION['event']));
    $dataEvent = $queryEvent->fetchAll();

    //get tickets for table
    $queryTickets = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event AND tickets.tableNumber = :tableNumber ORDER BY tickets."seatNumber", tickets."transaction"');
    $queryTickets->execute(array('event' => $_SESSION['event'], 'tableNumber' => $_POST['table-number']));
    $dataTickets = $queryTickets->fetchAll();

    //make sure there are enough spaces available
    $seatCount = count($dataTickets);
    if(($seatCount + intval($_POST['check-tickets']) + intval($_POST['cash-tickets']) + intval($_POST['free-tickets'])) > $dataEvent[0]['seatCount']) {
        die(json_encode(array(
            'error' => 'ERR_SEAT_LIMIT_EXCEEDED'
        )));
    }

    //create the transaction
    $queryTransaction = $pdo->prepare("INSERT INTO transactions (event, tableNumber, guestName, idNumber, isPresent, addedBy) VALUES (:event, :tableNumber, :guestName, :idNumber, 0, '')");
    $resultTransaction = $queryTransaction->execute(array('event' => $_SESSION['event'], 'tableNumber' => $_POST['table-number'], 'guestName' => $_POST['name'], 'idNumber' => $_POST['id-number']));
    $transactionId = $pdo->lastInsertId();

    //create tickets for the transaction
    for($i = 0; $i < intval($_POST['cash-tickets']); $i++) {
        $queryTicket = $pdo->prepare('INSERT INTO tickets (`transaction`, tableNumber, seatNumber, price, method, checkNumber) VALUES (:tId, :tableNumber, :seatNumber, :price, :method, :checkNumber)');
        $queryTicket->execute(array('tId' => $transactionId, 'tableNumber' => $_POST['table-number'], 'seatNumber' => $seatCount, 'price' => $dataEvent[0]['ticketPrice'], 'method' => 'TICKET_METHOD_CASH', 'checkNumber' => ''));
        $seatCount++;
    }
    for($i = 0; $i < intval($_POST['check-tickets']); $i++) {
        $queryTicket = $pdo->prepare('INSERT INTO tickets (`transaction`, tableNumber, seatNumber, price, method, checkNumber) VALUES (:tId, :tableNumber, :seatNumber, :price, :method, :checkNumber)');
        $queryTicket->execute(array('tId' => $transactionId, 'tableNumber' => $_POST['table-number'], 'seatNumber' => $seatCount, 'price' => $dataEvent[0]['ticketPrice'], 'method' => 'TICKET_METHOD_CHECK', 'checkNumber' => $checkNumbers[$i]));
        $seatCount++;
    }
    for($i = 0; $i < intval($_POST['free-tickets']); $i++) {
        $queryTicket = $pdo->prepare('INSERT INTO tickets (`transaction`, tableNumber, seatNumber, price, method, checkNumber) VALUES (:tId, :tableNumber, :seatNumber, :price, :method, :checkNumber)');
        $queryTicket->execute(array('tId' => $transactionId, 'tableNumber' => $_POST['table-number'], 'seatNumber' => $seatCount, 'price' => 0, 'method' => 'TICKET_METHOD_CASH', 'checkNumber' => ''));
        $seatCount++;
    }

    echo json_encode(array('success' => true));