<title>BHSPromApp</title>

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="apple-touch-icon" href="<?php echo $_SERVER['HTTP_HOST']; ?>apple-touch-icon.png">
<link rel="stylesheet" href="https://code.getmdl.io/1.1.1/material.<?php echo PRIMARY_COLOR . '-' . ACCENT_COLOR; ?>.min.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto' type='text/css'>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/resources/stylesheets/stylesheet.css" type="text/css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/vendors/dialog-polyfill/dialog-polyfill.css" type="text/css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/vendors/gridster/jquery.gridster.min.css" type="text/css">
<link href="<?php echo BASE_URL; ?>/vendors/snackbar-light-js/dist/snackbarlight.min.css" rel="stylesheet">


<script defer src="https://code.getmdl.io/1.1.1/material.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>/vendors/dialog-polyfill/dialog-polyfill.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL; ?>/vendors/gridster/jquery.gridster.min.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL; ?>/vendors/snackbar-light-js/dist/snackbarlight.min.js"></script>

<!-- Prevent links from opening in MobileSafari -->
<script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")</script>

<script type="text/javascript">

    $('body').ready(function() {
        $('form input').on('keypress', function(event) {
            if((event.which && event.which == 13) || (event.keyCode && event.keyCode == 13)) {
                $('.form-button--default').click();
                event.preventDefault();
            }
        });
    });

</script>