<?php

    function error($message) {
        header('Location: ' . BASE_URL . '/error?err=' . urlencode(htmlentities($message)));
        die();
    }

    function errorHandler($errno, $errstr, $errfile, $errline) {
        if(headers_sent()) {
            die(    'Error Number: ' . htmlentities($errno) . '<br/>' .
                    'Error File: ' . htmlentities($errfile) . '<br/>' .
                    'Error Line: ' . htmlentities($errline) . '<br/><br/>' .
                    'Error: ' . htmlentities($errstr)
                );
        } else {
            header('Location: ' . BASE_URL . '/error?err=' . urlencode(
                    'Error Number: ' . htmlentities($errno) . '<br/>' .
                    'Error File: ' . htmlentities($errfile) . '<br/>' .
                    'Error Line: ' . htmlentities($errline) . '<br/><br/>' .
                    'Error: ' . htmlentities($errstr)
                ));
            die();
        }
    }

    //get the current version of the software
    //edit the version here
    function getVersion() {
        return '3.3';
    }

    function getBuildDate() {
        return strtotime('2016-05-25');
    }

    //generate a session id for the user
    function generateSessionID($length = 16) {
        $result = "";

        $allowedChars = array('A', 'B', 'C', '0', 'D', 'E', 'F', '1', 'G', '2', 'H', 'I', '3', 'J', 'K', 'L', 'M', '4', 'N', 'O', 'P', 'Q', 'R', '5', '6', 'T', '7', 'U', 'V', '8', 'W', 'X', '9', 'Y', 'Z', '0');
        for($i = 0; $i < $length; $i++) {
            $result .= $allowedChars[rand(0, count($allowedChars) - 1)];
        }

        return $result;
    }

    //require a user to be signed in to access this page
    function requireLogin($pdo) {      
        if(!checkSession($pdo)) {
            header('Location: ' . BASE_URL . '/login');
            die();
        }
    }

    //check for a valid session
    function checkSession($pdo) {
        if(!isset($_SESSION['permissionLevel']))
            return false;
        if(empty($_SESSION['userID']) || empty($_SESSION['username'])) {
            if(isset($_SESSION['id'], $_SESSION['username'], $_SESSION['sessionID'])) {
                $queryCheck = $pdo->prepare("SELECT * FROM `users` WHERE `id` = :id AND `username` = :username AND `sessionID` = :sessionID LIMIT 1");
                $resultCheck = $queryCheck->execute(array('id' => $_SESSION['id'], 'username' => $_SESSION['username'], 'sessionID' => $_SESSION['sessionID']));
                if($resultCheck && count($queryCheck->fetchAll()) > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    //require a user to have a certain permission when accessing a page
    function requirePermissions($requiredLevel) {
        if($_SESSION['permissionLevel'] < $requiredLevel) {
            header('Location: ' . BASE_URL . '/login?denied=1');
            die();
        }
    }

    //echo html stuff
    function echohtml($text) {
        echo $text . "\n";
    }

    //update option
    function setOption($pdo, $name, $value) {
        //check if exists
        $queryOption = $pdo->prepare("SELECT * FROM `options` WHERE `name` = :name");
        $resultOption = $queryOption->execute(array('name' => $name));
        $dataOption = "";
        if($resultOption) {
            $dataOption = $queryOption->fetchAll();
        } else {
            return false;
        }

        if(count($dataOption) > 0) {
            //update entry
            $queryUpdate = $pdo->prepare("UPDATE `options` SET `value` = :value WHERE `name` = :name");
            $resultUpdate = $queryUpdate->execute(array('value' => $value, 'name' => $name));
            return $resultUpdate;
        } else {
            //create new entry
            $queryInsert = $pdo->prepare("INSERT INTO `options` (`name`, `value`) VALUES (:name, :value)");
            $resultInsert = $queryInsert->execute(array('name' => $name, 'value' => $value));
            return $resultInsert;
        }
    }

    //get option value
    function getOption($pdo, $name) {
        //check if exists
        $queryOption = $pdo->prepare("SELECT * FROM `options` WHERE `name` = :name");
        $resultOption = $queryOption->execute(array('name' => $name));
        if($resultOption) {
            $dataOption = $queryOption->fetchAll();

            if(count($dataOption) > 0) {
                return $dataOption[0]['value'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }