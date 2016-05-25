<?php

    require('../include/config.inc.php');

    //check if a db already exists
    if(file_exists('../database/main.db')) {
        header('Location: ../login');
        die();
    }

    //create new db
    if(isset($_GET['start']) && $_GET['start'] == 1) {
        copy('../database/template.db', '../database/main.db');
        header('Location: ../login');
        die();
    }

?>
<!DOCTYPE html>
<html>
<head>
    <?php require('../include/styles.inc.php'); ?>
    <script type="text/javascript">

        $('body').ready(function() {
            $('#start-button').on('click', function() {
                window.location.href = '?start=1';
            })
        });

    </script>
</head>
<body>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header">
            <div class="mdl-layout__header-row">
                <span class="mdl-layout-title">Welcome to BHSPromApp</span>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="gridless-content page-content">
                <div class="gridless-container">
                    <h6>BHSPromApp is a live event management system that allows recording of event registrations, seat reservations, and financial transactions.</h6>
                    <h6>To get started, click the button below. A default database will be created for you, along with a default user account and event. The following credentials will be used for the default user account:
                            <span style="font-weight:bold">Username:</span> admin<br/>
                            <span style="font-weight:bold">Password:</span> password<br/>
                        <span style="font-weight:bold">Please</span> make sure you change the admin credentials after you log in so no one else can access the system.
                    </h6>
                    <button id="start-button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored">Get Started</button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>