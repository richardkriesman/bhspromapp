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
        var searchButton = $('#search-button');

        searchButton.on('click', function (event) {
            $.post('../ajax/functions/search.php?sort=<?php echo urlencode(htmlentities($sort)); ?>', $('#search-form').serialize(), function (data) {
                var results = JSON.parse(data);
                var resultBody = $('#result-body');

                resultBody.html('');
                if (results.length > 0) {
                    for (var i = 0; i < results.length; i++) {
                        resultBody.html(resultBody.html() + '<tr>' +
                            '<td>' + results[i].seats + '</td>' +
                            '<td>' + results[i].guestName + '</td>' +
                            '<td>' + results[i].idNumber + '</td>' +
                            '<td>' + (results[i].isPresent == 1 ? 'Present' : 'Absent') + '</td>' +
                            '</tr>');
                        componentHandler.upgradeDom();
                    }
                } else {
                    resultBody.html('<tr>' +
                        '<td></td>' +
                        '<td>No Results</td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '</tr>');
                    componentHandler.upgradeDom();
                }
            });
            event.preventDefault();
        });

        componentHandler.upgradeDom();

        searchButton.trigger('click');
    });
</script>
<div class="gridless-content">
    <div class="gridless-container">
        <h4>Search</h4><form id="search-form">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input class="mdl-textfield__input" type="text" id="term" name="term" autocomplete="off">
                <label class="mdl-textfield__label" for="term">Query</label>
            </div>
            <button id="search-button" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect">Search</button>
        </form>
        <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
            <thead>
                <tr>
                    <th>Seat</th>
                    <th class="mdl-data-table__cell--nonnumeric">Guest Name</th>
                    <th>ID Number</th>
                    <th class="mdl-data-table__cell--nonnumeric">Attendance</th>
                </tr>
            </thead>
            <tbody id="result-body"></tbody>
        </table>
    </div>
</div>