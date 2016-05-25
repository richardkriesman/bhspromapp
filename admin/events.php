<?php
    require('../include/bootstrap.inc.php');
    
    session_start();
    
    requireLogin($pdo);
	requirePermissions(PERMISSIONS_ADMIN);

    $success = false;
    $error = "";

    if($_POST && !empty($_POST['formType'])) {
        if($_POST['formType'] == "edit") {
            if(!empty($_POST['events'])) {
                if($_POST['submit'] == "Delete Event" && (bool)getOption($pdo, 'permissions_eventsDelete')) {
                    //ensure there the user is not deleting their own event
                    $queryCheck = $pdo->prepare("SELECT * FROM `events` WHERE `id` = :id");
                    $queryCheck->execute(array('id' => $_POST['events']));
                    $dataCheck = $queryCheck->fetchAll();
                    if($dataCheck[0]['id'] == $_SESSION['event']) {
                        $error = "You cannot delete your own event. You must do it as the admin of another event.";
                    } else {
                        $queryUsers = $pdo->prepare("DELETE FROM `users` WHERE `eventID` = :id");
                        $queryUsers->execute(array('id' => $_POST['events']));

                        $queryTransactions = $pdo->prepare("SELECT * FROM `transactions` WHERE `event` = :id");
                        $queryTransactions->execute(array('id' => $_POST['events']));
                        $dataTransactions = $queryTransactions->fetchAll();
                        foreach($dataTransactions as $transaction) {
                            $queryTickets = $pdo->prepare("DELETE FROM `tickets` WHERE `transaction` = :tId");
                            $queryTickets->execute(array('tId' => $transaction['id']));
                        }

                        $queryGuests = $pdo->prepare("DELETE FROM `transactions` WHERE `event` = :id");
                        $queryGuests->execute(array('id' => $_POST['events']));

                        $queryEvent = $pdo->prepare("DELETE FROM `events` WHERE `id` = :id");
                        $queryEvent->execute(array('id' => $_POST['events']));

                        $queryTables = $pdo->prepare("DELETE FROM `tables` WHERE `event` = :id");
                        $queryTables->execute(array('id' => $_POST['events']));

                        $success = true;
                    }
                } else if($_POST['submit'] == "Purge Event Data" && (bool)getOption($pdo, 'permissions_eventsPurge')) {
                    $queryTransactions = $pdo->prepare("SELECT * FROM `transactions` WHERE `event` = :id");
                    $queryTransactions->execute(array('id' => $_POST['events']));
                    $dataTransactions = $queryTransactions->fetchAll();
                    foreach($dataTransactions as $transaction) {
                        $queryTickets = $pdo->prepare("DELETE FROM `tickets` WHERE `transaction` = :tId");
                        $queryTickets->execute(array('tId' => $transaction['id']));
                    }

                    $queryGuests = $pdo->prepare("DELETE FROM `transactions` WHERE `event` = :id");
                    $queryGuests->execute(array('id' => $_POST['events']));

                    $success = true;
                }
            }
        } else if($_POST['formType'] == "add" && (bool)getOption($pdo, 'permissions_eventsCreate')) {
            if(!empty($_POST['name']) && !empty($_POST['code']) && !empty($_POST['seats']) && is_numeric($_POST['seats'])) {
                $queryInsert = $pdo->prepare("INSERT INTO `events` (`name`, `eventCode`, `seatCount`, `ticketPrice`) VALUES (:name, :eventCode, :seatCount, :ticketPrice)");
                $queryInsert->execute(array('name' => $_POST['name'], 'eventCode' => $_POST['code'], 'seatCount' => $_POST['seats'], 'ticketPrice' => $_POST['price']));
                $newEventId = $pdo->lastInsertId();

                //change this from 68 when dynamic tables are added
                for($i = 1; $i <= 68; $i++) {
                    $tableCountQuery = $pdo->prepare('INSERT INTO tables (event, tableNumber, isEnabled, lockedTime) VALUES (:event, :tableNumber, 1, :newTime)');
                    $tableCountResult = $tableCountQuery->execute(array('event' => $newEventId, 'tableNumber' => $i, 'newTime' => date('Y-m-d H:i:s', time())));
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
<?php include_once('../include/styles.inc.php'); ?>
<title>BHSPromapp Administration Panel</title>
<style>
    body {
        text-align:center;
        font-family: 'Droid Sans', sans-serif;
		background-size:auto 100%;
        background-repeat:no-repeat;
        background-position:center;
        background-attachment:fixed;
        background-color:#000000;
    }
    
    #overlay {
        position:absolute;
        top:50%;
        left:50%;
        margin-top:-187px;
        margin-left:-150px;
    }
    
    #footer {
        position:absolute;
        bottom:0;
        margin-left:45px;
        font-size:12px;
    }
    
    .formElements {
        text-align:left;
        font-size:14px;
        margin-left:25px;
    }

    .dialog {
        position:absolute;
        top:50%;
        left:50%;
        margin-top:-187px;
        margin-left:-240px;
        width:500px;
        height:375px;
        -webkit-border-radius: 20px;
        -moz-border-radius: 20px;
        border-radius: 20px;
        border:2px solid #9C9C9C;
        background-color:#E8E8E8;
    }

    .hidden {
        display:none;
    }
</style>
<script type="text/javascript">
    function showNew() {
        document.getElementById("dimmedBackground").removeAttribute("class");
        document.getElementById("newEvent").setAttribute("class", "dialog");
        document.getElementById("newEventName").focus();
    }
    
    function hideNew() {
        document.getElementById("dimmedBackground").setAttribute("class", "hidden");
        document.getElementById("newEvent").setAttribute("class", "hidden");
        document.getElementById("newEventName").value = "";
        document.getElementById("newEventCode").value = "";
    }
</script>
</head>
<body>
<!-- Overlay -->
<div id="dimmedBackground" class="hidden" style="background-color:#606060;opacity:0.8;position:absolute;top:0;left:0;height:100%;width:100%;"></div>
<div id="overlay" style="width:300px;height:375px;-webkit-border-radius: 20px;-moz-border-radius: 20px;border-radius: 20px;border:2px solid #9C9C9C;background-color:#E8E8E8">
    <p style="font-size:24px;font-weight:bold">Edit Events</p>
    <div style="overflow:auto;height:285px;font-size:12px">
            <?php if($success) { echo '<p style="color:green;font-size:16px">The operation completed successfully.</p>'; } else if ($error != "") { echo '<p style="color:red;font-size:16px">' . $error . '</p>'; } ?>
            <form method="post" action="events.php">
                <input type="hidden" name="formType" value="edit" />
                <p><select name="events" style="width:65%">
                    <?php
                        //get events
                        $queryEvents = $pdo->query("SELECT * FROM `events`");
                        while($row = $queryEvents->fetch()) {
                            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                        }
                    ?>
                </select> <input type="button" style="width:25%" onclick="showNew()" value="Add Event" <?php if(!(bool)getOption($pdo, 'permissions_eventsCreate')) { echo 'disabled'; } ?>/></p>
                <p><input type="submit" name="submit" onclick="return confirm('Deleting this event will permanently delete all of its data and users . Are you sure you want to continue?')" value="Delete Event" title="Delete the event along with all of its data and users." <?php if(!(bool)getOption($pdo, 'permissions_eventsDelete')) { echo 'disabled'; } ?>/></p>
                <p><input type="submit" name="submit" onclick="return confirm('Purging this event will delete all of its guest reservations . Are you sure you want to continue?')" value="Purge Event Data" title="Delete all guest reservations from the event." <?php if(!(bool)getOption($pdo, 'permissions_eventsPurge')) { echo 'disabled'; } ?>/></p>
            </form>
    </div>
    <div style="position:relative;bottom:40px">
        <p><button onclick="window.location.replace('index.php'); return false">Back to Menu</button></p>
    </div>
</div>
<div id="newEvent" class="hidden">
    <form method="post" action="events.php">
        <p style="font-size:24px;font-weight:bold">Add Event</p>
        <input type="hidden" name="formType" value="add" />
        <p>Event Name<br/><input type="text" id="newEventName" name="name" maxlength="255" /></p>
        <p>Event Code (All caps and numbers only)<br/><input type="text" id="newEventCode" name="code" maxlength="20" pattern="([A-Z]|[0-9])+" /></p>
        <p>Seats Per Table <select name="seats">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
            <option value="16">16</option>
            <option value="17">17</option>
            <option value="18">18</option>
            <option value="19">19</option>
            <option value="20">20</option>
        </select></p>
        <p>Price<br/><input type="number" id="newEventPrice" name="price" /></p>
        <p><input type="submit" value="Submit" />&nbsp;<input type="button" value="Cancel" onclick="hideNew()" /></p>
    </form>
</div>
<!-- End Overlay -->
</body>
</html>