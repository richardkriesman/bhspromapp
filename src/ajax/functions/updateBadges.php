<?php

    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    require('../../include/bootstrap.inc.php');
    
    if(empty($_GET['sessionID'])) {
        die('!: Invalid request!');   
    }
    
    $event = 0;
    
    //validate session ID
    $querySessionID = $pdo->prepare("SELECT * FROM `users` WHERE `sessionID` = :sID");
    $querySessionID->execute(array('sID' => $_GET['sessionID']));
    $dataSessionID = $querySessionID->fetchAll();
    if(count($dataSessionID) <= 0) {
        die('!: Access denied!');
    } else {
        foreach($dataSessionID as $row) {
            $event = $row['eventID'];
        }
    }

    $output = array();

    //get seats per table
    $totalSeats = 0;
    $totalTables = 0;
    $querySeats = $pdo->prepare("SELECT * FROM `events` WHERE `id` = :id LIMIT 1");
    $querySeats->execute(array('id' => $event));
    while($row = $querySeats->fetch()) {
        $totalSeats = $row['seatCount'];
        $totalTables = $row['tableCount'];
    }

    //initialize seats available array
    $seatCount = array();
    for($i = 0; $i < $totalTables; $i++) {
        $seatCount[$i] = 0;
    }

    $queryFetch = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event ORDER BY tickets."seatNumber", tickets."transaction"');
    $queryFetch->execute(array('event' => $event));
    while($row = $queryFetch->fetch()) {
        $seatCount[$row['tableNumber'] - 1]++;
    }
    
    for($i = 0; $i < count($seatCount); $i++) {
        $seatCount[$i] = $totalSeats - $seatCount[$i];
    }
    
    //get disabled tables
    $queryDisabled = $pdo->prepare('SELECT * FROM `tables` WHERE `event` = :event AND `isEnabled` = 0');
    $queryDisabled->execute(array('event' => $event));
    while($row = $queryDisabled->fetch()) {
        $seatCount[$row['tableNumber'] - 1] = "-"; //set disabled table seats to 0
    }
    
    for($i = 0; $i < count($seatCount); $i++) {
        $output[$i] = $seatCount[$i];
    }
    
    header('Content-type: text/json');
    echo json_encode($output);
?>