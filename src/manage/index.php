<?php

    require('../include/bootstrap.inc.php');

    session_start();
    
    requireLogin($pdo);
	requirePermissions(PERMISSIONS_READ);

    if($_SESSION['forcePasswordChange']) {
        header('Location: ../password');
        die();
    }

	//get event info
	$queryEvents = $pdo->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
	$queryEvents->execute(array('id' => $_SESSION['event']));
	$dataEvents = $queryEvents->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <?php require('../include/styles.inc.php'); ?>
    <script type="text/javascript">
        var badgeUpdater = null;

        function changeTab(tab, url) {
            if(badgeUpdater != null) {
                clearInterval(badgeUpdater);
            }
            setTimeout(function () {
                $.get(url, function (data) {
                    $('#' + tab).html(data);
                });
            }, 500);
        }

        function addDialog(dialog) {
            var oldDialog = $('.' + dialog);
            var newDialog = oldDialog.clone();
            oldDialog.remove();
            newDialog.appendTo($('body'));
        }

        $('body').ready(function() {
            //sign out handler
            $('#sign-out-button').on('click', function() {
               window.location.href = '../logout';
            });

            //change password handler
            $('#change-password-button').on('click', function() {
                window.location.href = '../password';
            });

            //table view handler
            $('#table-view-button').on('click', function() {
                changeTab('scroll-tab-1', '../ajax/tabs/overview.php');
            });
            changeTab('scroll-tab-1', '../ajax/tabs/overview.php');

            //list view handler
            $('#list-view-button').on('click', function() {
                changeTab('scroll-tab-2', '../ajax/tabs/search.php');
            });

            $('#report-view-button').on('click', function() {
                changeTab('scroll-tab-3', '../ajax/tabs/reports.php');
            });

            $('#admin-panel-button').on('click', function() {
                window.open('../admin');
                window.location.reload();
            });
        });

    </script>
</head>
<body>
    <div class="layout mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header">
            <div class="mdl-layout__header-row">
                <span class="mdl-layout-title">BHSPromApp</span>
                <div class="mdl-layout-spacer"></div>
				<span class="mdl-layout-title"><?= htmlentities($dataEvents[0]['name']) ?>&nbsp;</span>
                <button id="account-button" class="mdl-button mdl-js-button mdl-button--icon">
                    <i class="material-icons">account_circle</i>
                </button>
                <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="account-button">
                    <li class="mdl-menu__item" disabled>Hello, <?php echo htmlentities($_SESSION['username']); ?></li>
                    <!--<li class="mdl-menu__item" disabled>Session <?= $_SESSION['sessionID']; ?></li>-->
                    <li class="mdl-menu__item mdl-menu__item--full-bleed-divider" id="change-password-button">Change Password</li>
                    <li class="mdl-menu__item" id="sign-out-button">Sign Out</li>
                </ul>
            </div>
            <div class="mdl-layout__tab-bar mdl-js-ripple-effect">
                <a href="#scroll-tab-1" id="table-view-button" class="mdl-layout__tab is-active">Overview</a>
                <a href="#scroll-tab-2" id="list-view-button" class="mdl-layout__tab">Search</a>
                <a href="#scroll-tab-3" id="report-view-button" class="mdl-layout__tab">Reports</a>
                <?php if($_SESSION['permissionLevel'] >= 2) { ?>
                    <a href="#scroll-tab-4" id="admin-panel-button" class="mdl-layout__tab">Administration Panel</a>
                <?php } ?>
            </div>
        </header>
        <main class="mdl-layout__content">
            <section class="mdl-layout__tab-panel is-active" id="scroll-tab-1"></section>
            <section class="mdl-layout__tab-panel" id="scroll-tab-2"></section>
            <section class="mdl-layout__tab-panel" id="scroll-tab-3"></section>
            <section class="mdl-layout__tab-panel" id="scroll-tab-4"></section>

            <div id="admiral-snackbar" class="mdl-js-snackbar mdl-snackbar">
                <div class="mdl-snackbar__text"></div>
                <button class="mdl-snackbar__action" type="button"></button>
            </div>
        </main>
    </div>
</body>
</html>
