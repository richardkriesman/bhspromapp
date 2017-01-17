<?php

    require('../../include/bootstrap.inc.php');

    if(empty($_GET['data'])) {
        die('!: Invalid request!');   
    }
    
    require('../../vendors/barcode/class/BCGFont.php');
    require('../../vendors/barcode/class/BCGColor.php');
    require('../../vendors/barcode/class/BCGDrawing.php');
    require('../../vendors/barcode/class/BCGcode128.barcode.php');
    
    //generate the barcode
    $colourFront = new BCGColor(0, 0, 0);
    $colourBack = new BCGColor(255, 255, 255);
                
    $font = new BCGFont('../../vendors/barcode/class/font/Arial.ttf', 18);
                
    $code = new BCGCode128();
    $code->setScale(2);
    $code->setThickness(30);
    $code->setForegroundColor($colourFront);
    $code->setBackgroundColor($colourBack);
    $code->setFont($font);
    $code->parse($_GET['data']);
    
    $drawing = new BCGDrawing('', $colourBack);
    $drawing->setBarcode($code);
    $drawing->draw();
    
    header('Content-Type: image/png');
    
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
    
?>