<?php

    require('../include/bootstrap.inc.php');
    
    session_start();
    
    $sort = "all";
    if(!empty($_GET['sort'])) {
        if($_GET['sort'] == "present" || $_GET['sort'] == "absent") {
            $sort = $_GET['sort'];
        }
    }
    
    if(empty($_GET['type'])) {
        $_GET['type'] = 'csv';
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
    
    //create csv file
    if($_GET['type'] == "csv") {
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=guests.csv');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo "Seats,Guest Name,ID Number\n";
        
        foreach($guests as $row) {
            $seats = '';
            if($row['startingSeat'] != $row['endingSeat'])
                $seats = $row['tableNumber'] . $row['startingSeat'] . '-' . $row['tableNumber'] . $row['endingSeat'];
            else
                $seats = $row['tableNumber'] . $row['startingSeat'];

            if($row['idNumber'] == '') {
                echohtml($seats.',"'.$row['name'].'"');
            } else {
                echohtml($seats.',"'.$row['name'].'","'.$row['idNumber'].'"');
            }
        }
    } else if($_GET['type'] == "csv") {
        header('Location: index.php');
    }
    
?>