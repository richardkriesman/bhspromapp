<?php

    require('../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READ);

    $error = "";

    //validate form
    if($_POST) {
        if($_POST['newPassword'] != $_POST['confirmNewPassword']) {
            $error = "The passwords you entered do not match.";
        } else if(empty($_POST['newPassword']) || empty($_POST['confirmNewPassword'])) {
            $error = "You must choose a new password.";
        } else if(strlen($_POST['newPassword']) < 8) {
            $error = "Your password must be no less than 8 characters.";
        } else if(strlen($_POST['newPassword']) >= 30) {
            $error = "Your password must be no greater than 30 characters.";
        }
        if($error == "") {
            $queryChange = $pdo->prepare("UPDATE `users` SET `password` = :password, `forcePasswordChange` = 0 WHERE `id` = :id");
            $queryChange->execute(array('password' => sha1($_POST['newPassword']), 'id' => $_SESSION['id']));

            $_SESSION['forcePasswordChange'] = false;

            header('Location: ../manage');
            die();
        }
    }

?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once('../include/styles.inc.php'); ?>
    <script type="text/javascript">
        $('body').ready(function() {
            $('#confirmNewPassword').on('change', function() {
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
                if($('.is-dirty').length < 2)
                    event.preventDefault();
            });

            $('#cancel-button').on('click', function(event) {
                event.preventDefault();
                window.location.href = '../manage';
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
                <?php
                    if($_SESSION['forcePasswordChange']) {
                        echo '<h6>Because your password was reset, you\'ll need to choose a new password.</h6>';
                    } else {
                        echo '<h6>To change your password, type a new one below.</h6>';
                    }
                ?>
                <form method="post">
                    <?php
                        if($error != "") {
                            echo '<h6 class="error-text">'.$error.'</h6>';
                        }
                    ?>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <input class="mdl-textfield__input" type="password" id="newPassword" name="newPassword" maxlength="30" pattern="\S{8,30}" autocomplete="off"/>
                        <label class="mdl-textfield__label" for="newPassword">New Password</label>
                        <label class="mdl-textfield__error">Must be at least 8 characters long</label>
                    </div>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <input class="mdl-textfield__input" type="password" id="confirmNewPassword" name="confirmNewPassword" maxlength="30" autocomplete="off"/>
                        <label class="mdl-textfield__label" for="confirmNewPassword">Confirm New Password</label>
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