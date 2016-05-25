<?php

    require_once('../include/bootstrap.inc.php');

    session_start();

    //upgrade if upgrade is required
    if(file_exists('../upgrade.php')) {
        header('Location: ../upgrade.php');
        die();
    }

    //variables
    $validLogin = true;
    $accountEnabled = true;
    $invalidSession = false;
    $maintenance = false;
    $accessDenied = false;

    //check if access was denied
    if(isset($_GET['denied']) && $_GET['denied'] == 1)
        $accessDenied = true;

    //check if session invalid
    if(!empty($_SESSION['sessionID'])) {
        if(!checkSession($pdo)) {
            $invalidSession = true;
        }
    }

    //validate form
    if($_POST) {
        if(!empty($_POST['username']) && !empty($_POST['password'])) {
            //login
            $queryCheck = $pdo->query("SELECT * FROM `users` WHERE `username` = :username AND `password` = :password LIMIT 1");
            $resultCheck = $queryCheck->execute(array('username' => $_POST['username'], 'password' => sha1($_POST['password'])));
            $dataCheck = $queryCheck->fetchAll();

            //user was found, log them in
            if($resultCheck && count($dataCheck) > 0) {
                //set session variables
                $_SESSION['id'] = $dataCheck[0]['id'];
                $_SESSION['username'] = $dataCheck[0]['username'];
                $_SESSION['event'] = $dataCheck[0]['eventID'];
                $_SESSION['permissionLevel'] = $dataCheck[0]['permissionLevel'];
                if($dataCheck[0]['isEnabled'] == 0) {
                    $accountEnabled = false;
                }
                if($dataCheck[0]['forcePasswordChange'] == 1) {
                    $_SESSION['forcePasswordChange'] = true;
                } else {
                    $_SESSION['forcePasswordChange'] = false;
                }

                //check if in maintenance
                $queryOptions = $pdo->query("SELECT * FROM `options`");
                while($option = $queryOptions->fetch()) {
                    if($option['name'] == 'maintenance' && $option['value'] == 1 && $dataCheck['permissionLevel'] >= 2) {
                        $maintenance = true;
                    }
                }

                if($accountEnabled && !$maintenance) {
                    //set session id
                    $_SESSION['sessionID'] = generateSessionID();
                    $queryLogin = $pdo->prepare("UPDATE `users` SET `sessionID` = :sessionID WHERE `id` = :uID");
                    $resultLogin = $queryLogin->execute(array('sessionID' => $_SESSION['sessionID'], 'uID' => $_SESSION['id']));
                    if($resultLogin) {
                        //login is complete, redirect user
                        header('Location: ../');
                        die();
                    } else {
                        die('nope');
                    }
                }
            } else {
                $validLogin = false;
            }
        } else {
            $validLogin = false;
        }
    }
    
    //if error, cleanup session info
    if(!$validLogin) {
        $_SESSION['id'] = 0;
        $_SESSION['username'] = '';
        $_SESSION['sessionID'] = '';
        $_SESSION['event'] = '';
        unset($_SESSION['permissionLevel']);
    }
    
?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once('../include/styles.inc.php'); ?>
    <script type="text/javascript">

        $('body').ready(function() {
                        
            $('#about-button').on('click', function() {
                var dialog = $('#about-dialog')[0];
                dialogPolyfill.registerDialog(dialog);
                dialog.showModal();
            });

            $('#close-about-button').on('click', function() {
                var dialog = $('#about-dialog')[0];
                dialog.close();
            });

            $('#register-button').on('click', function(event) {
                event.preventDefault();
                window.location.href = '../register';
            });
            
            setTimeout(function() {
                $('#username').focus();
            }, 300);
        });

    </script>
</head>
<body>
    <dialog class="mdl-dialog" id="about-dialog">
        <h4 class="mdl-dialog__title">About</h4>
        <div class="mdl-dialog__content">
            <p>BHSPromApp v.<?php echo getVersion(); ?><br/>
            Built on <?php echo date('Y-m-d', getBuildDate()); ?></p>

            <p>Created by Richard Kriesman</p>

            <p>Licensed under the <a href="../LICENSE.txt" target="_blank">GNU General<br/>Public License v.3.0</a><br/></p>

            <table border="0" style="position:relative;left:-2px;top:-12px">
                <tr>
                    <td><p>Created for L.D. Bell High School.<br/>
                    Assembled in Texas.</p></td>
                    <td><img src="../resources/images/texasIcon.png" /></td>
                </tr>
            </table>
        </div>
        <div class="mdl-dialog__actions">
            <button type="button" class="mdl-button mdl-js-button" id="close-about-button">OK</button>
        </div>
    </dialog>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header">
            <div class="mdl-layout__header-row">
                <span class="mdl-layout-title">BHSPromApp</span>
                <div class="mdl-layout-spacer"></div>
                <button class="mdl-button mdl-js-button mdl-button--icon" onclick="window.open('https://github.com/richardkriesman/bhspromapp/issues/new')">
                    <i class="material-icons" id="bug-report-icon">bug_report</i>
                </button>
                <div class="mdl-tooltip" for="bug-report-icon">Submit Bug Report</div>
                <button class="mdl-button mdl-js-button mdl-button--icon" id="about-button">
                    <i class="material-icons" id="about-icon">help</i>
                </button>
                <div class="mdl-tooltip" for="about-icon">About BHSPromApp</div>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="gridless-content page-content">
                <div class="gridless-container">
                    <h6>To sign in to BHSPromApp, enter your username and password below.</h6>
                    <?php
                        if(!$validLogin)
                            echo '<h6 class="error-text">The username or password you entered is incorrect.</h6>';
                        else if(!$accountEnabled)
                            echo '<h6 class="error-text">This account has been disabled.</h6>';
                        else if($invalidSession)
                            echo '<h6 class="error-text">This account has been signed in at another location.</h6>';
                        else if($maintenance)
                            echo '<h6 class="error-text">BHSPromApp is currently undergoing scheduled maintenance.</h6>';
                        else if($accessDenied)
                            echo '<h6 class="error-text">Access was denied to that resource. Please sign in using an account with access permissions.</h6>';
                    ?>
                    <form method="post">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="text" name="username" id="username" autocomplete="off">
                            <label class="mdl-textfield__label" for="username">Username</label>
                        </div>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="password" name="password" id="password" autocomplete="off">
                            <label class="mdl-textfield__label" for="password">Password</label>
                        </div>
                        <div class="form-button-div">
                            <button id="register-button" class="mdl-button mdl-js-button mdl-js-ripple-effect">Register</button>
                            <button class="form-button--default mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
    
