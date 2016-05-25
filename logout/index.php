<?php
    
    require('../include/functions.inc.php');
    
    session_start();

    $_SESSION['username'] = '';
    $_SESSION['id'] = '';
    $_SESSION['event'] = '';
    $_SESSION['sessionID'] = '';
    
    header('Location: ../login/');
?>