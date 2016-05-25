<?php

    require('../include/bootstrap.inc.php');

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

    ?>
<!DOCTYPE html>
<html>
<head>
    <title>BHSPromApp</title>
    <?php include('../include/styles.inc.php'); ?>
    <style>
        body {
            font-family: 'Droid Sans', sans-serif;
            text-align:center;
        }

        #print-toolbar {
            padding: 20px 20px 20px 20px;
        }
    </style>
</head>
<body>
    <div id="print-toolbar">
        <button id="cancel-button" class="mdl-button mdl-js-button mdl-js-ripple-effect">Cancel</button>
        <button id="print-button" class="mdl-button mdl-js-button mdl-button--colored mdl-button--raised mdl-js-ripple-effect">Print</button>
    </div>
    <br/>
    <?php
    if($sort == "all") {
        echohtml('<p style="font-size:24px">Guest List - All Guests</p>');
    } else if($sort == "present") {
        echohtml('<p style="font-size:24px">Guest List - Present Guests</p>');
    } else if($sort == "absent") {
        echohtml('<p style="font-size:24px">Guest List - Absent Guests</p>');
    }
    ?>

    <br/>

    <table align="center" style="text-align:center;width:60%">
        <tr style="font-weight:bold">
            <td>Seat</td>
            <td>Guest Name</td>
            <td>ID Number</td>
        </tr>
        <?php
            foreach($guests as $row) {
                echohtml('<tr>');
                if($row['startingSeat'] != $row['endingSeat'])
                    echohtml('<td>'.$row['tableNumber'] . $row['startingSeat'] . '-' . $row['tableNumber'] . $row['endingSeat'].'</td>');
                else
                    echohtml('<td>'.($row['tableNumber'] . $row['startingSeat']).'</td>');
                echohtml('<td>'.htmlentities($row['name']).'</td>');
                echohtml('<td>'.htmlentities($row['idNumber']).'</td>');
                echohtml('</tr>');
            }
        ?>
    </table>
    <script type="text/javascript">
        $('#print-button').on('click', function () {
            var printToolbar = $('#print-toolbar');

            printToolbar.hide();
            window.print();
            printToolbar.show();
        });

        $('#cancel-button').on('click', function() {
            window.close();
        });
    </script>
</body>
</html>