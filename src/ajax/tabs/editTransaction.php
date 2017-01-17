<?php

    require('../../include/bootstrap.inc.php');

    session_start();

    if(empty($_GET['id']) || !is_numeric($_GET['id']))
        die('Invalid request!');

    if(empty($_GET['table']) || !is_numeric($_GET['table']))
        die('Invalid request!');

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READWRITE);

    //get transaction info
    $queryTransactions = $pdo->prepare('SELECT * FROM transactions WHERE id = :id LIMIT 1');
    $queryTransactions->execute(array('id' => $_GET['id']));
    $dataTransactions = $queryTransactions->fetchAll();

    if(count($dataTransactions) == 0)
        die('Transaction does not exist!');

?>
<script type="text/javascript">
    $('body').ready(function() {
        var idNumber = $('#id-number');
        var name = $('#name');

        $('#cancel-button').on('click', function(e) {
            e.preventDefault();
            changeTab('scroll-tab-1', '../ajax/tabs/table.php?id=<?= urlencode($_GET['table']) ?>');
        });

        $('#submit-button').on('click', function(e) {
            e.preventDefault();
            $.post('../ajax/functions/edit.php', {
                "name": $('#name').val(),
                "id-number": $('#id-number').val(),
                "transaction-id": <?= $_GET['id'] ?>,
            }, function() {
                changeTab('scroll-tab-1', '../ajax/tabs/table.php?id=<?= urlencode($_GET['table']) ?>');
            });
        });
        
        idNumber.on('keypress', function(e) {
            if(e.which == 13) {
                $('#name').focus();
                e.preventDefault();
            }
        });
                
        name.on('keypress', function(e) {
            if(e.which == 13) {
                $('#name').blur();
                $('#submit-button').click();
                e.preventDefault();
            }
        });
        
        name.on('focus', function() {
           $(this).select();
        });
        
        idNumber.on('focus', function() {
            $(this).select();
        });
        
        idNumber.focus();

        componentHandler.upgradeDom();
    });
</script>
<div class="gridless-content">
    <div class="gridless-container">
        <h4>Edit Transaction</h4>
        <h6 id="error" style="display:none;color:red"></h6>
        <form action="#">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input type="text" name="id-number" id="id-number" class="mdl-textfield__input" autocomplete="off" value="<?= htmlentities($dataTransactions[0]['idNumber']) ?>">
                <label for="id-number" class="mdl-textfield__label">ID Number</label>
            </div>
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input type="text" name="name" id="name" class="mdl-textfield__input" autocomplete="off" value="<?= htmlentities($dataTransactions[0]['guestName']) ?>">
                <label for="name" class="mdl-textfield__label">Guest Name</label>
            </div><br/>
            <button id="cancel-button" class="mdl-button mdl-js-button mdl-js-ripple-effect">Cancel</button>
            <button id="submit-button" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect">Submit</button>
        </form>
    </div>
</div>