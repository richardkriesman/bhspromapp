<?php
    require('../include/bootstrap.inc.php');
    
    session_start();

	requireLogin($pdo);
	requirePermissions(PERMISSIONS_ADMIN);

    $success = false;
    $message = "";
    $error = "";

    if($_POST && !empty($_POST['formType'])) {
        if($_POST['formType'] == "edit") {
            if(!empty($_POST['events'])) {
                if($_POST['submit'] == "Delete User" && (bool)getOption($pdo, 'permissions_usersDelete')) {
                    //ensure the user isn't an admin
                    $queryUserData = $pdo->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
                    $queryUserData->execute(array('id' => $_POST['events']));
                    $dataUserData = $queryUserData->fetchAll();
                    if (count($dataUserData) > 0) {
                        if ($dataUserData[0]['permissionLevel'] > 1) {
                            $error = "You cannot delete an administrative account.";
                        } else if ($dataUserData[0]['username'] == $_SESSION['username']) {
                            $error = "You cannot delete your own account.";
                        } else {
                            //delete the user
                            $queryUsers = $pdo->prepare("DELETE FROM `users` WHERE `id` = :id");
                            $queryUsers->execute(array('id' => $_POST['events']));

                            $success = true;
                        }
                    }
                } else if($_POST['submit'] == 'Reset Password' && (bool)getOption($pdo, 'permissions_usersReset')) {
                    //ensure the user isn't themself
                    $queryUserData = $pdo->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
                    $queryUserData->execute(array('id' => $_POST['events']));
                    $dataUserData = $queryUserData->fetchAll();
                    if(count($dataUserData) > 0) {
                        if($dataUserData[0]['permissionLevel'] > $_SESSION['permissionLevel']) {
                            $error = "You don't have permission to reset this user's password.";
                        } else if($dataUserData[0]['username'] == $_SESSION['username']) {
                            $error = "You can change your own password<br/>from Table View.";
                        } else {
                            //zhu li, do the thing!
                            $password = generateSessionID(8);
                            $queryUsers = $pdo->prepare("UPDATE `users` SET `password` = :password, `forcePasswordChange` = 1 WHERE `id` = :id");
                            $queryUsers->execute(array('password' => sha1($password), 'id' => $_POST['events']));

                            $success = true;
                            $message = 'The user\'s password has been set to <span style="font-weight:bold">'.$password.'</span>.';
                        }
                    }
                } else if($_POST['submit'] == "Toggle Enabled" && (bool)getOption($pdo, 'permissions_usersToggleEnabled')) {
                    //ensure the user isn't an admin
                    $queryUserData = $pdo->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
                    $queryUserData->execute(array('id' => $_POST['events']));
                    $dataUserData = $queryUserData->fetchAll();
                    if(count($dataUserData) > 0) {
                        if ($dataUserData[0]['permissionLevel'] > 1) {
                            $error = "You cannot disable an administrative account.";
                        } else if($dataUserData[0]['username'] == $_SESSION['username']) {
                            $error = "You cannot disable your own account.";
                        } else {
                            //toggle the user
                            $queryUsers = $pdo->prepare("UPDATE `users` SET `isEnabled` = NOT isEnabled WHERE `id` = :id");
                            $queryUsers->execute(array('id' => $_POST['events']));

                            $success = true;
                        }
                    }
                } else if($_POST['submit'] == "Promote" && (bool)getOption($pdo, 'permissions_usersPromoteDemote')) {
                    //ensure the user isn't themself
                    $queryUserData = $pdo->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
                    $queryUserData->execute(array('id' => $_POST['events']));
                    $dataUserData = $queryUserData->fetchAll();
                    if(count($dataUserData) > 0) {
                        if($dataUserData[0]['username'] == $_SESSION['username']) {
                            $error = "You cannot change your own permission level.";
                        } else if($dataUserData[0]['permissionLevel'] == 3) {
                            $error = "This user is already at the highest permission level.";
                        } else if($dataUserData[0]['permissionLevel'] >= $_SESSION['permissionLevel']) {
                            $error = "You don't have permission to promote that high.";
                        } else {
                            //toggle permissions
                            $queryUsers = $pdo->prepare("UPDATE `users` SET `permissionLevel` = permissionLevel + 1 WHERE `id` = :id");
                            $queryUsers->execute(array('id' => $_POST['events']));

                            $success = true;
                        }
                    }
                } else if($_POST['submit'] == "Demote" && (bool)getOption($pdo, 'permissions_usersPromoteDemote')) {
                    //ensure the user isn't themself
                    $queryUserData = $pdo->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
                    $queryUserData->execute(array('id' => $_POST['events']));
                    $dataUserData = $queryUserData->fetchAll();
                    if(count($dataUserData) > 0) {
                        if($dataUserData[0]['username'] == $_SESSION['username']) {
                            $error = "You cannot change your own permission level.";
                        } else if($dataUserData[0]['permissionLevel'] == 0) {
                            $error = "This user is already at the lowest permission level.";
                        } else if($dataUserData[0]['permissionLevel'] > $_SESSION['permissionLevel']) {
                                $error = "You don't have permission to demote that user.";
                        } else {
                            //toggle permissions
                            $queryUsers = $pdo->prepare("UPDATE `users` SET `permissionLevel` = permissionLevel - 1 WHERE `id` = :id");
                            $queryUsers->execute(array('id' => $_POST['events']));

                            $success = true;
                        }
                    }
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
</style>
</head>
<body>
<!-- Overlay -->
<div id="overlay" style="width:300px;height:375px;-webkit-border-radius: 20px;-moz-border-radius: 20px;border-radius: 20px;border:2px solid #9C9C9C;background-color:#E8E8E8;">
    <p style="font-size:24px;font-weight:bold">Edit Users</p>
    <div style="overflow:auto;height:285px;font-size:12px">
        <?php
            if($message) {
                echo '<p style="color:green;font-size:16px;margin:0px 10px 0px 10px">'.$message.'</p><br/>';
            } else if($success) {
                echo '<p style="color:green;font-size:16px;margin:0px 10px 0px 10px">The operation completed successfully.</p><br/>';
            } else if($error != "") {
                echo '<p style="color:red;font-size:16px;margin:0px 10px 0px 10px">'.$error.'</p><br/>';
            }
        ?>
        <form method="post" action="users.php">
                <input type="hidden" name="formType" value="edit" />
                <select name="events" style="width:85%">
                    <?php
                        //get users
                        $queryEvents = $pdo->query("SELECT * FROM `users` ORDER BY `eventID`, `username` ASC");
                        while($row = $queryEvents->fetch()) {
                            echo '<option value="'.$row['id'].'">'.$row['username']. ' ' . ($row['permissionLevel'] == 0 ? '(Read Only)' : '') . ($row['permissionLevel'] == 2 ? '(Admin)' : '') . ($row['permissionLevel'] == 3 ? ' (Super Admin)' : '') . ($row['isEnabled'] == 0 ? '(Disabled)' : '') . '</option>';
                        }
                    ?>
                </select><br/>
                <p><input type="submit" name="submit" onclick="return confirm('Are you sure you want to delete this user?')"value="Delete User" title="Delete the user's account." <?php if(!(bool)getOption($pdo, 'permissions_usersDelete')) { echo 'disabled'; } ?>/></p>
                <p><input type="submit" name="submit" value="Reset Password" onclick="return confirm('Are you sure you want to reset this user\'s password?')" title="Reset the user's password to a random value. They will be required to change their password the next time they log in." <?php if(!(bool)getOption($pdo, 'permissions_usersReset')) { echo 'disabled'; } ?>/></p>
                <p><input type="submit" name="submit" value="Toggle Enabled" title="Enable or disable this account. Disabled accounts will be unable to log in." <?php if(!(bool)getOption($pdo, 'permissions_usersToggleEnabled')) { echo 'disabled'; } ?>/></p>
                <p><input type="submit" name="submit" value="Promote" title="Grant this user an additional permission level." <?php if(!(bool)getOption($pdo, 'permissions_usersPromoteDemote')) { echo 'disabled'; } ?>/>&nbsp;<input type="submit" name="submit" value="Demote" title="Revoke a permission level from this user." <?php if(!(bool)getOption($pdo, 'permissions_usersPromoteDemote')) { echo 'disabled'; } ?>/></p>
            </form>
    </div>
    <div style="position:relative;bottom:40px">
        <p><button onclick="window.location.replace('index.php'); return false">Back to Menu</button></p>
    </div>
</div>
<!-- End Overlay -->
</body>
</html>