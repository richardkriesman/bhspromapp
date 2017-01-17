<?php

    require('../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READ);

    if(empty($_GET['id'])) {
        header('Location: index.php');
    }

    $guest = null;
    $querySearch = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event AND transactions.id = :id ORDER BY tickets."seatNumber", tickets."transaction"');
    $result = $querySearch->execute(array('event' => $_SESSION['event'], 'id' => $_GET['id']));
    $dataSearch = $querySearch->fetchAll();
    if($result && count($dataSearch) > 0) {
        $guest = $dataSearch[0];
    } else {
        header('Location: ../index.php?err=The guest could not be found.');
    }

    //get event name and ticket pricing
    $eventName = "";
    $price = 0;
    $queryEvent = $pdo->prepare("SELECT * FROM `events` WHERE `id` = :id LIMIT 1");
    $queryEvent->execute(array('id' => $guest['event']));
    while($row = $queryEvent->fetch()) {
        $eventName = $row['name'];
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>BHSPromApp</title>
    <?php include('../include/styles.inc.php'); ?>
    <style>
        body {
            text-align:center;
            font-size:12px;
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
    <p style="font-size:24px"><?= $eventName ?>: Receipt</p>
    <br/>

    <div style="text-align:center">
        <p style="font-size:12px">
            <span style="font-weight:bold">Date of Purchase:</span> <?= date(DATE_FORMAT, strtotime($guest['addedDate'])) ?>
        </p>
        <p style="font-size:12px">
            <span style="font-weight:bold">Name:</span> <?= htmlentities($guest['guestName']); ?><br/>
            <span style="font-weight:bold">ID Number:</span> <?= $guest['idNumber'] == '' ? 'None' : htmlentities($guest['idNumber']) ?><br/>
        </p>
        <p style="font-size:12px">
            <span style="font-weight:bold">Event:</span> <?= htmlentities($eventName); ?><br/>
            <span style="font-weight:bold">Table Number:</span> <?= $guest['tableNumber']; ?><br/>
            <span style="font-weight:bold">Number of Seats:</span> <?= count($dataSearch); ?><br/>
        </p>
        <br/>
        <br/>
        <table style="border:none;margin-left:auto;margin-right:auto">
            <thead>
                <tr>
                    <th>Seat Number</th>
                    <th>Price</th>
                    <th>Check Number</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalPrice = 0;
                foreach($dataSearch as $guest) { ?>
                    <tr>
                        <td>Seat <?= $guest['tableNumber'] . $guest['seatNumber'] ?></td>
                        <td>$<?= number_format($guest['price'], 2) ?></td>
                        <td><?= htmlentities($guest['checkNumber']) ?></td>
                    </tr>
                <?php
                $totalPrice += $guest['price'];
                } ?>
                <tr>
                    <td style="font-weight:bold">Total:</td>
                    <td>$<?= number_format($totalPrice, 2) ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br/>
    <br/>
    <?php if($guest['idNumber'] != '') { ?>
        <div style="display:flex;justify-content:center;align-items:flex-end;">
            <div><img src="../ajax/functions/generateBarcode.php?data=<?= $guest['idNumber'] ?>" /></div>
        </div>
    <?php } ?>
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