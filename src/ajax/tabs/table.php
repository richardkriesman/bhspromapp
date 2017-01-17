<?php

    require('../../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READ);

    if(empty($_GET['id']) || !is_numeric($_GET['id'])) {
        header('Location: index.php');
    }

    //variables
    $seatCount = 0;
    $enabled = true;

    //check if table is disabled
    $queryPrefs = $pdo->prepare('SELECT * FROM `tables` WHERE `event` = :event AND `tableNumber` = :tableNumber AND `isEnabled` = 0');
    $resultPrefs = $queryPrefs->execute(array('event' => $_SESSION['event'], 'tableNumber' => $_GET['id']));
    if($resultPrefs && count($queryPrefs->fetchAll()) > 0) {
        $enabled = false;
    }

    //fetch data
    $queryEvent = $pdo->prepare('SELECT * FROM `events` WHERE `id` = :id LIMIT 1');
    $queryGuests = $pdo->prepare('SELECT * FROM tickets JOIN transactions ON tickets."transaction" = transactions."id" WHERE transactions.event = :event AND tickets.tableNumber = :tableNumber ORDER BY tickets."seatNumber", tickets."transaction"');
    //$queryGuests = $pdo->prepare('SELECT * FROM `transactions` WHERE `event` = :event AND `tableNumber` = :tableNumber');
    $queryEvent->execute(array('id' => $_SESSION['event']));
    $dataEvent = $queryEvent->fetchAll();
    if(count($dataEvent) > 0) {
        if($dataEvent[0]['tableCount'] >= $_GET['id']) {
            $queryGuests->execute(array('event' => $dataEvent[0]['id'], 'tableNumber' => $_GET['id']));
            $seatCount = $dataEvent[0]['seatCount'];
        } else {
            header('Location: index.php');
        }
    }
    $dataGuests = $queryGuests->fetchAll();

    //format guests into a pretty array
    $guests = array();
    foreach($dataGuests as $guest) {
        if(!array_key_exists($guest['transaction'], $guests)) {
            $guests[$guest['transaction']]['data'] = array();
            $guests[$guest['transaction']]['transaction'] = $guest['transaction'];
            $guests[$guest['transaction']]['idNumber'] = $guest['idNumber'];
            $guests[$guest['transaction']]['isPresent'] = $guest['isPresent'];
            $guests[$guest['transaction']]['name'] = $guest['guestName'];
        }

        array_push($guests[$guest['transaction']]['data'], $guest['seatNumber']);
    }
    foreach($dataGuests as $guest) {
        $guests[$guest['transaction']]['startingSeat'] = min($guests[$guest['transaction']]['data']);
        $guests[$guest['transaction']]['endingSeat'] = max($guests[$guest['transaction']]['data']);
    }
?>
<script type="text/javascript">
    $('body').ready(function() {

        function claimTable() {
            $.post('../ajax/functions/claimTable.php', { "table": <?= urlencode($_GET['id']) ?> });
        }
        badgeUpdater = setInterval(claimTable, 1000);
        claimTable();

        $('#table-toggle-switch').on('click', function() {
            var toggle = $('#table-toggle-switch');
            if(!toggle[0].checked) {
                $.post('../ajax/functions/disableTable.php', { "id": "<?= urlencode($_GET['id']) ?>" }).done(function(data) {
                    if(data.success == true)
                        changeTab('scroll-tab-1', '../ajax/tabs/table.php?id=<?= urlencode($_GET['id']) ?>');
                });
            } else {
                $.post('../ajax/functions/enableTable.php', { "id": "<?= urlencode($_GET['id']) ?>" }, function(data) {
                    if(data.success == true)
                        changeTab('scroll-tab-1', '../ajax/tabs/table.php?id=<?= urlencode($_GET['id']) ?>');
                });
            }
        });

        $('.attendance-button').on('click', function() {
            var elem = $(this);
            var id = elem.attr('data-transaction');
            $.post('../ajax/functions/toggleAttendance.php', { "id": id }, function(data) {
                if(data.success == true) {
                    if (data.present == true) {
                        elem.children()[0].innerHTML = 'person';
                    } else {
                        elem.children()[0].innerHTML = 'person_outline';
                    }
                }
            });
        });

        $('.delete-button').on('click', function() {
            if(confirm('Are you sure you want to delete this transaction?')) {
                var elem = $(this);
                var id = elem.attr('data-transaction');
                $.post('../ajax/functions/delete.php', {"id": id}, function (data) {
                    changeTab('scroll-tab-1', '../ajax/tabs/table.php?id=<?= urlencode($_GET['id']) ?>');
                });
            }
        });

        $('.receipt-button').on('click', function() {
            var elem = $(this);
            var id = elem.attr('data-transaction');
            window.open('../reports/receipt.php?id=' + id);
        });

        $('#add-transaction-button').on('click', function() {
            changeTab('scroll-tab-1', '../ajax/tabs/newTransaction.php?table=<?= urlencode($_GET['id']) ?>');
        });
        
        $('.edit-button').on('click', function() {
            var elem = $(this);
            var id = elem.attr('data-transaction');
            changeTab('scroll-tab-1', '../ajax/tabs/editTransaction.php?table=<?= urlencode($_GET['id']) ?>&id=' + id);
        });

        componentHandler.upgradeDom();
    });
</script>
<div class="gridless-content">
    <div class="gridless-container">
        <h4>
            Table <?php echo htmlentities($_GET['id']); ?>&nbsp;&nbsp;<label class="table-toggle mdl-switch mdl-js-switch mdl-js-ripple-effect" for="table-toggle-switch">
                <input type="checkbox" id="table-toggle-switch" class="mdl-switch__input" <?= $enabled ? 'checked' : '' ?> <?= $_SESSION['permissionLevel'] == PERMISSIONS_READ ? 'disabled' : '' ?>>
                <span class="mdl-switch__label"></span>
            </label>
        </h4>
        <?php if($enabled) { ?>
            <table id="table-data-table" class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
                <thead>
                    <tr>
                        <th>Seats</th>
                        <th class="mdl-data-table__cell--non-numeric">Guest Name</th>
                        <th>ID Number</th>
                        <th class="mdl-data-table__cell--non-numeric">Attendance</th>
                        <th class="mdl-data-table__cell--non-numeric">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($guests as $guest) { ?>
                        <tr>
                            <td><?php
                                    if($guest['startingSeat'] != $guest['endingSeat']) {
                                        echo htmlentities($_GET['id']) . $guest['startingSeat'] . '-' . htmlentities($_GET['id'] . $guest['endingSeat']);
                                    } else {
                                        echo htmlentities($_GET['id']) . $guest['startingSeat'];
                                }
                                ?></td>
                            <td class="mdl-data-table__cell--non-numeric"><?= htmlentities($guest['name']) ?></td>
                            <td><?= htmlentities($guest['idNumber']) ?></td>
                            <td class="mdl-data-table__cell--non-numeric" style="text-align: center">
                                <?php if($_SESSION['permissionLevel'] >= PERMISSIONS_READWRITE) { ?>
                                    <?php if($guest['isPresent'] == 1) { ?>
                                        <button id="attendance-icon-<?= $guest['transaction'] ?>" data-transaction="<?= $guest['transaction'] ?>" class="attendance-button mdl-button mdl-js-button mdl-button--icon">
                                            <i class="material-icons">person</i>
                                        </button>
                                    <?php } else { ?>
                                        <button id="attendance-icon-<?= $guest['transaction'] ?>" data-transaction="<?= $guest['transaction'] ?>" class="attendance-button mdl-button mdl-js-button mdl-button--icon">
                                            <i class="material-icons">person_outline</i>
                                        </button>
                                    <?php } ?>
                                <?php } else { ?>
                                    <?php if($guest['isPresent'] == 1) { ?>
                                        <i class="material-icons">person</i>
                                    <?php } else { ?>
                                        <i class="material-icons">person_outline</i>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                            <td class="mdl-data-table__cell--non-numeric">
                                <?php if($_SESSION['permissionLevel'] >= PERMISSIONS_READWRITE) { ?>
                                    <button id="edit-icon-<?= $guest['transaction'] ?>" data-transaction="<?= $guest['transaction'] ?>" class="edit-button mdl-button mdl-js-button mdl-button--icon">
                                        <i class="material-icons">edit</i>
                                    </button>
                                    <div class="mdl-tooltip" for="edit-icon-<?= $guest['transaction'] ?>">Edit</div>
                                    <button id="delete-icon-<?= $guest['transaction'] ?>" data-transaction="<?= $guest['transaction'] ?>" class="delete-button mdl-button mdl-js-button mdl-button--icon">
                                        <i class="material-icons">delete</i>
                                    </button>
                                    <div class="mdl-tooltip" for="delete-icon-<?= $guest['transaction'] ?>">Delete</div>
                                <?php } ?>
                                <button id="receipt-icon-<?= $guest['transaction'] ?>" data-transaction="<?= $guest['transaction'] ?>" class="receipt-button mdl-button mdl-js-button mdl-button--icon">
                                    <i class="material-icons">receipt</i>
                                </button>
                                <div class="mdl-tooltip" for="receipt-icon-<?= $guest['transaction'] ?>">Receipt</div>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td style="font-weight: bold"><?= count($dataGuests) >= $seatCount ? 'This table is currently full.' : '' ?></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button id="add-transaction-button" class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored mdl-js-ripple-effect" <?= ($_SESSION['permissionLevel'] == PERMISSIONS_READ) || (count($dataGuests) >= $seatCount) ? 'disabled' : '' ?>>
                                <i class="material-icons">add</i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php } else { ?>
            <table id="table-data-table" class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
                <thead>
                    <tr>
                        <th class="mdl-data-table__cell--non-numeric">This table has been disabled. Guests cannot be added or modified.</th>
                    </tr>
                </thead>
            </table>
        <?php } ?>
    </div>
</div>