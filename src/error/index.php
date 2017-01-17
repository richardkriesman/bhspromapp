<!DOCTYPE html>
<html>
<head>
    <style>
        .error {
            font-size:36px;
            font-family: 'Arial', sans-serif;
            text-align:center;
        }
    </style>
</head>
<body>
<div class="error">
    <p>Whoops! It appears that something went wrong.</p>
    <br/>
    <p><?php
        if(isset($_GET['err'])) {
            echo str_replace('&lt;br/&gt;', '<br/>', htmlentities($_GET['err']));
        }
        ?></p>
    <p>Please try restarting the app.</p>
</div>
</body>
</html>