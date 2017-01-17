<?php

require('../include/bootstrap.inc.php');
    
    session_start();
    
    //variables
    $validEntry = true;
    $validUsername = true;
    $validPassword = true;
    $usernameUnique = true;
    $passwordsMatch = true;
    $codeMatch = true;
    $eventID = 0;
    
    //validate form
    if($_POST) {
        if(empty($_POST['code']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['confirm'])) {
            $validEntry = false;
        }
        if(strlen($_POST['username']) < 4 || strlen($_POST['username']) >= 20 || !preg_match('/^[a-zA-Z0-9]*$/', $_POST['username'])) {
            $validUsername = false;
        }
        if(strlen($_POST['password']) < 8 || strlen($_POST['password']) >= 30) {
            $validPassword = false;
        }
        if($_POST['password'] != $_POST['confirm']) {
            $passwordsMatch = false;
        }
        
        //check that username doesn't exist
        if($validEntry && $validUsername && $validPassword && $passwordsMatch) {
            $queryUsername = $pdo->prepare("SELECT * FROM `users` WHERE `username` = :username");
            $resultUsername = $queryUsername->execute(array('username' => $_POST['username']));
            if(!$resultUsername || count($queryUsername->fetchAll()) > 0) {
                $usernameUnique = false;
            }
        }
        
        //validate code
        if($validEntry && $validUsername && $validPassword && $passwordsMatch && $usernameUnique) {
            $queryCode = $pdo->prepare("SELECT * FROM `events` WHERE `eventCode` = :code LIMIT 1");
            $resultCode = $queryCode->execute(array('code' => $_POST['code']));
            $dataCode = $queryCode->fetchAll();
            if(!$resultCode || count($dataCode) == 0) {
                $codeMatch = false;
            } else if($resultCode) {
                $eventID = $dataCode[0]['id'];
            }
        }
        
        //insert into database
        if($validEntry && $validUsername && $validPassword && $passwordsMatch && $usernameUnique && $codeMatch && $eventID != 0) {
			$queryInsert = $pdo->prepare("INSERT INTO `users` (`eventID`, `username`, `password`, `sessionID`, `permissionLevel`) VALUES (:eventID, :username, :password, :sessionID, :permissionLevel)");
			$resultInsert = $queryInsert->execute(array('eventID' => $eventID, 'username' => $_POST['username'], 'password' => sha1($_POST['password']), 'sessionID' => generateSessionID(), 'permissionLevel' => (!empty($_POST['kiosk']) ? '0' : '1')));
            if($resultInsert) {
                header('Location: ../login/');
            } else {
                header('Location: ../index.php?err=Database error creating account');
            }
        }
    }
    
?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once('../include/styles.inc.php'); ?>
    <script type="text/javascript">
        $('body').ready(function() {
           $('#confirm').on('change', function() {
               var password = $('#password');
               var confirm = $('#confirm');
               if(password.val() != confirm.val())
                   confirm.parent().addClass('is-invalid');
               else
                   confirm.parent().removeClass('is-invalid');
           });

            $('#submit-button').on('click', function(event) {
                if($('.is-invalid').length > 0)
                    event.preventDefault();
                if($('.is-dirty').length < 4)
                    event.preventDefault();
            });

            $('#cancel-button').on('click', function(event) {
                event.preventDefault();
                window.location.href = '../login';
            });
        });
    </script>
</head>
<body>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header">
            <div class="mdl-layout__header-row">
                <span class="mdl-layout-title">BHSPromApp</span>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="gridless-content page-content">
                <div class="gridless-container">
                    <h6>Welcome to BHSPromApp! To create an account, please complete the following fields. You will need the Event Passcode that was provided by your event organizer.</h6>
                    <form method="post">
                        <?php
                        if(!$usernameUnique) {
                            echo '<h6 style="color:red">That username has already been taken.</h6>';
                        } else if(!$codeMatch) {
                            echo '<h6 style="color:red">The Event Passcode is invalid.</h6>';
                        }
                        ?>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="text" id="code" name="code" maxlength="20" pattern="([A-Z]|[0-9])+" autocomplete="off"/>
                            <label class="mdl-textfield__label" for="code">Event Passcode</label>
                            <label class="mdl-textfield__error" for="code">Capital letters and numbers only</label>
                        </div>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="text" id="username" name="username" maxlength="20" pattern="([A-Z]|[a-z]|[0-9]){4,20}" autocomplete="off"/>
                            <label class="mdl-textfield__label" for="username">Username</label>
                            <label class="mdl-textfield__error">No special characters and at least 4 characters long</label>
                        </div>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="password" id="password" name="password" maxlength="30" pattern="\S{8,30}" autocomplete="off"/>
                            <label class="mdl-textfield__label" for="password">Password</label>
                            <label class="mdl-textfield__error">Must be at least 8 characters long</label>
                        </div>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="password" id="confirm" name="confirm" maxlength="30" autocomplete="off"/>
                            <label class="mdl-textfield__label" for="confirm">Confirm Password</label>
                            <label class="mdl-textfield__error">Must match the password field above</label>
                        </div>
                        <div class="form-button-div">
                            <button id="cancel-button" class="mdl-button mdl-js-button mdl-js-ripple-effect">Cancel</button>
                            <button id="submit-button" class="form-button--default mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>