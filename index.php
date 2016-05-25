<?php
    require('include/bootstrap.inc.php');

    //start session
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <?php require('include/styles.inc.php'); ?>
    <style>
        body {
            font-family: 'Droid Sans', sans-serif;
        }
    </style>
</head>
<body>
<script type="text/javascript">
    var redirect = true;
</script>
<div style="text-align:center;font-family:Droid Sans">
    <noscript>
            <p style="font-size:32px">Woah there, partner!</p>
            <p>Like most websites today, we use Javascript to deliver the best experience possible.</p>
            <p>If you'd like to have a nice, functional site, please enable Javascript.</p>
            <p>If you'd like to ignore this warning and continue anyways, click <a href="manage">here</a>. However, many features of this site may not work.</p>
            <p>&nbsp;</p>
    </noscript>
</div>
<script type="text/javascript">
    if(redirect) {
        <?php

            //if logged in
            if(checkSession($pdo)) {
                echo 'window.location.replace("manage/");';
            } else {
                //header('Location: login/');
                echo 'window.location.replace("login/");';
            }
        ?>
    }
</script>
</body>
</html>