<?php

    require('../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READ);

    $querySearch = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event ORDER BY tickets."seatNumber", tickets."transaction"');
    $querySearch->execute(array('event' => $_SESSION['event']));
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
            $guests[$guest['transaction']]['checkNumbers'] = array();
        }

        //create pretty formatted string from array of check numbers
        if(!is_array($guests[$guest['transaction']]['checkNumbers']))
            $guests[$guest['transaction']]['checkNumbers'] = explode(', ', $guests[$guest['transaction']]['checkNumbers']);
        if(is_numeric($guest['checkNumber']))
            array_push($guests[$guest['transaction']]['checkNumbers'], $guest['checkNumber']);
        $guests[$guest['transaction']]['checkNumbers'] = implode(', ', $guests[$guest['transaction']]['checkNumbers']);
        
        array_push($guests[$guest['transaction']]['data'], array('seat' => $guest['seatNumber'], 'price' => $guest['price'], 'method' => $guest['method']));
    }
    //magic hackey stuff that removes the ", " from the beginning of every check number series
    foreach($dataSearch as $guest) {
        if(strpos($guests[$guest['transaction']]['checkNumbers'], ', ') === 0) {
            $guests[$guest['transaction']]['checkNumbers'] = substr($guests[$guest['transaction']]['checkNumbers'], 2);
        }
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
    <p style="font-size:24px">Event Revenue</p>
    <br/>
    <table align="center" style="text-align:center;width:60%">
    <tr style="font-weight:bold">
        <td>Guest Name</td>
        <td>Tickets</td>
        <td>Cash Revenue</td>
        <td>Check Revenue</td>
        <td>Check Numbers</td>
        <td>Total Revenue</td>
    </tr>
    <?php
        $totalTickets = 0;
        $totalCash = 0;
        $totalCheck = 0;
        foreach($guests as $row) {
            echohtml('<tr>');
            echohtml('<td>'.htmlentities($row['name']).'</td>');

            $tickets = 0;
            $cash = 0;
            $check = 0;
            $checkNumbers = $row['checkNumbers'];
            foreach($row['data'] as $ticket) {
                $tickets++;
                $totalTickets++;
                if($ticket['method'] == 'TICKET_METHOD_CASH') {
                    $cash += $ticket['price'];
                    $totalCash += $ticket['price'];
                } else if($ticket['method'] == 'TICKET_METHOD_CHECK') {
                    $check += $ticket['price'];
                    $totalCheck += $ticket['price'];
                }
            }
            echohtml("<td>$tickets</td>");
            echohtml('<td>$'.number_format($cash, 2).'</td>');
            echohtml('<td>$'.number_format($check, 2).'</td>');
            echohtml('<td>'.htmlentities($checkNumbers).'</td>');
            echohtml('<td>$'.number_format($cash + $check, 2).'</td>');

            echohtml('</tr>');
        }
    ?>
    <tr style="font-weight:bold">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr style="font-weight:bold">
        <td>Total:</td>
        <td><?= $totalTickets ?></td>
        <td><?= '$'.number_format($totalCash, 2) ?></td>
        <td><?= '$'.number_format($totalCheck, 2) ?></td>
        <td>&nbsp;</td>
        <td style="border: 1px solid black"><?= '$'.number_format($totalCash + $totalCheck, 2) ?></td>
    </tr>
</table>
<script type="text/javascript">   
        $('#print-button').on('click', function () {
            var printToolbar = $('#print-toolbar');
            var printSpinner = $('#print-spinner');
            

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