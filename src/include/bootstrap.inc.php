<?php

    define('ROOT_PATH', dirname(dirname(__FILE__)));

    require(ROOT_PATH . '/include/constants.inc.php');
    require(dirname(ROOT_PATH) . '/config.inc.php');
    require(ROOT_PATH . '/include/functions.inc.php');

    //set the global error handler
    set_error_handler('errorHandler', E_ALL);

    //make sure the database exists to begin with
    if(!file_exists(dirname(ROOT_PATH) . '/data/main.db')) {
        header('Location: ' . BASE_URL . '/welcome');
        die();
    } else {
        if(!is_readable(dirname(ROOT_PATH) . '/data/main.db')) {
            error('Unable to read from main.db. Ensure the program has read access to the file.');
        } else if(!is_writeable(dirname(ROOT_PATH) . '/data/main.db')) {
            error('Unable to write to main.db. Ensure the program has write access to the file.');
        }
    }

    //connect to database
    $pdo = '';
    try {
        $pdo = new PDO('sqlite:' . dirname(ROOT_PATH) . '/data/main.db');
    } catch (PDOException $ex) {
        die('GLOBAL DATABASE ERROR: ' . $ex->getMessage());
    }

    //set default timezone
    date_default_timezone_set(TIME_ZONE);
