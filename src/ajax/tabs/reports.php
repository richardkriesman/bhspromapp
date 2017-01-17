<?php

    require('../../include/bootstrap.inc.php');

    session_start();

    $sort = "all";
    if(!empty($_GET['sort'])) {
        if($_GET['sort'] == "present" || $_GET['sort'] == "absent") {
            $sort = $_GET['sort'];
        }
    }

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READ);


?>
<script type="text/javascript">
    $('body').ready(function() {
        $('#report-all-print-button').on('click', function() {
           window.open('../reports/print.php?sort=all');
        });

        $('#report-all-csv-button').on('click', function() {
            window.open('../reports/export.php?sort=all');
        });

        $('#report-present-print-button').on('click', function() {
            window.open('../reports/print.php?sort=present');
        });

        $('#report-present-csv-button').on('click', function() {
            window.open('../reports/export.php?sort=present');
        });

        $('#report-absent-print-button').on('click', function() {
            window.open('../reports/print.php?sort=absent');
        });

        $('#report-absent-csv-button').on('click', function() {
            window.open('../reports/export.php?sort=absent');
        });

        $('#report-earnings-button').on('click', function() {
            window.open('../reports/revenue.php');
        });

        componentHandler.upgradeDom();
    });
</script>
<div class="gridless-content">
    <div class="gridless-container">
        <h4>Reports</h4>
        <ul class="mdl-list">
            <li class="mdl-list__item">
                <span class="mdl-list__item-primary-content">Event Revenue</span>
                <span class="mdl-list__item-secondary-action report-list-actions">
                    <button id="report-earnings-button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised">Print</button>&nbsp;
                </span>
            </li>
            <li class="mdl-list__item">
                <span class="mdl-list__item-primary-content">Guest List: All</span>
                <span class="mdl-list__item-secondary-action report-list-actions">
                    <button id="report-all-print-button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised">Print</button>&nbsp;
                    <button id="report-all-csv-button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised">CSV</button>
                </span>
            </li>
            <li class="mdl-list__item">
                <span class="mdl-list__item-primary-content">Guest List: Present</span>
                <span class="mdl-list__item-secondary-action report-list-actions">
                    <button id="report-present-print-button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised">Print</button>&nbsp;
                    <button id="report-present-csv-button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised">CSV</button>
                </span>
            </li>
            <li class="mdl-list__item">
                <span class="mdl-list__item-primary-content">Guest List: Absent</span>
                <span class="mdl-list__item-secondary-action report-list-actions">
                    <button id="report-absent-print-button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised">Print</button>&nbsp;
                    <button id="report-absent-csv-button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised">CSV</button>
                </span>
            </li>
        </ul>
    </div>
</div>