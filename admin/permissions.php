<?php
    require('../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_SUPERADMIN);

    $success = false;
    $error = "";

    if($_POST) {

        $success = true;

        //process events
        if(!setOption($pdo, 'permissions_eventsCreate', (int)isset($_POST['eventsCreate']))) {
            $success = false;
            $error = "Your changes could not be saved.";
        }
        if(!setOption($pdo, 'permissions_eventsDelete', (int)isset($_POST['eventsDelete']))) {
            $success = false;
            $error = "Your changes could not be saved.";
        }
        if(!setOption($pdo, 'permissions_eventsPurge', (int)isset($_POST['eventsPurge']))) {
            $success = false;
            $error = "Your changes could not be saved.";
        }

        //process users
        if(!setOption($pdo, 'permissions_usersDelete', (int)isset($_POST['usersDelete']))) {
            $success = false;
            $error = "Your changes could not be saved.";
        }
        if(!setOption($pdo, 'permissions_usersReset', (int)isset($_POST['usersReset']))) {
            $success = false;
            $error = "Your changes could not be saved.";
        }
        if(!setOption($pdo, 'permissions_usersToggleEnabled', (int)isset($_POST['usersToggleEnabled']))) {
            $success = false;
            $error = "Your changes could not be saved.";
        }
        if(!setOption($pdo, 'permissions_usersPromoteDemote', (int)isset($_POST['usersPromoteDemote']))) {
            $success = false;
            $error = "Your changes could not be saved.";
        }

        //process customization
        if(!setOption($pdo, 'permissions_customizeImage', (int)isset($_POST['customizeImage']))) {
            $success = false;
            $error = "Your changes could not be saved.";
        }
        if(!setOption($pdo, 'permissions_customizeColour', (int)isset($_POST['customizeColour']))) {
            $success = false;
            $error = "Your changes could not be saved.";
        }

    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once('../include/styles.inc.php'); ?>
    <title>BHSPromapp Administration Panel</title>
    <script type="text/javascript" src="../lib/jscolor/jscolor.js"></script>
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

        .twoColumns {
            -webkit-column-count:2;
            -moz-column-count:2;
            column-count:2;
        }

        #overlay {
            position:absolute;
            top:50%;
            left:50%;
            margin-top:-225px;
            margin-left:-150px;
        }
    </style>
</head>
<body>
<!-- Overlay -->
<div id="overlay" style="width:300px;height:460px;-webkit-border-radius: 20px;-moz-border-radius: 20px;border-radius: 20px;border:2px solid #9C9C9C;background-color:#E8E8E8;">
    <p style="font-size:24px;font-weight:bold">Edit Admin Permissions</p>
    <div style="overflow:auto;height:370px;font-size:12px">
        <?php
        if($success & $error == "") {
            echo '<p style="color:green;font-size:16px">Settings successfully saved.</p>';
        } else if($error != "") {
            echo '<p style="color:red;font-size:16px">'.$error.'</p>';
        }
        ?>
        <form method="post" action="permissions.php" enctype="multipart/form-data">
            <p>
                <span style="font-size:18px;font-weight:bold">Events</span><br/>
                <input type="checkbox" name="eventsCreate" value="yes" <?php if((bool)getOption($pdo, 'permissions_eventsCreate')) { echo 'checked="checked" '; } ?>>Allow New Events</input><br/>
                <input type="checkbox" name="eventsDelete" value="yes" <?php if((bool)getOption($pdo, 'permissions_eventsDelete')) { echo 'checked="checked" '; } ?>>Allow Deleting Events</input><br/>
                <input type="checkbox" name="eventsPurge" value="yes" <?php if((bool)getOption($pdo, 'permissions_eventsPurge')) { echo 'checked="checked" '; } ?>>Allow Purging Events</input><br/>
            </p>
            <p>
                <span style="font-size:18px;font-weight:bold">Users</span><br/>
                <input type="checkbox" name="usersDelete" value="yes" <?php if((bool)getOption($pdo, 'permissions_usersDelete')) { echo 'checked="checked" '; } ?>>Allow Deleting Users</input><br/>
                <input type="checkbox" name="usersReset" value="yes" <?php if((bool)getOption($pdo, 'permissions_usersReset')) { echo 'checked="checked" '; } ?>>Allow Resetting Passwords</input><br/>
                <input type="checkbox" name="usersToggleEnabled" value="yes" <?php if((bool)getOption($pdo, 'permissions_usersToggleEnabled')) { echo 'checked="checked" '; } ?>>Allow Enabling/Disabling</input><br/>
                <input type="checkbox" name="usersPromoteDemote" value="yes" <?php if((bool)getOption($pdo, 'permissions_usersPromoteDemote')) { echo 'checked="checked" '; } ?>>Allow Promotion/Demotion</input><br/>
            </p>
            <p>
                <span style="font-size:18px;font-weight:bold">Customization</span><br/>
                <input type="checkbox" name="customizeImage" value="yes" <?php if((bool)getOption($pdo, 'permissions_customizeImage')) { echo 'checked="checked" '; } ?>>Allow Setting Background Image</input><br/>
                <input type="checkbox" name="customizeColour" value="yes" <?php if((bool)getOption($pdo, 'permissions_customizeColour')) { echo 'checked="checked" '; } ?>>Allow Setting Background Colour</input><br/>
            </p>
            <p></p>
            <p><input type="submit" value="Save" /></p>
        </form>
    </div>
    <div style="position:relative;bottom:0">
        <p><button onclick="window.location.replace('index.php'); return false">Back to Menu</button></p>
    </div>
</div>
<!-- End Overlay -->
</body>
</html>