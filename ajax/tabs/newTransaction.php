<?php

    require('../../include/bootstrap.inc.php');

    session_start();

    if(empty($_GET['table']))
        die('Invalid request!');

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READWRITE);

    //get event info
    $queryEvent = $pdo->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
    $queryEvent->execute(array('id' => $_SESSION['event']));
    $dataEvent = $queryEvent->fetchAll();

?>
<script type="text/javascript">
    $('body').ready(function() {

        function claimTable() {
            $.post('../ajax/functions/claimTable.php', { "table": <?= urlencode($_GET['table']) ?> });
        }
        badgeUpdater = setInterval(claimTable, 1000);
        claimTable();

        function calculateTotal() {
            var cash = parseInt($('#cash-tickets').html()).toFixed(2) * <?= $dataEvent[0]['ticketPrice'] ?>;
            var check = parseInt($('#check-tickets').html()).toFixed(2) * <?= $dataEvent[0]['ticketPrice'] ?>;
            var total = parseInt(cash) + parseInt(check);

            $('#total-cost').html('$' + total);
        }

        $('#add-cash-ticket').on('click', function(e) {
            var cashTickets = $('#cash-tickets');
            if(parseInt(cashTickets.html()) + 1 >= 0)
                cashTickets.html(parseInt(cashTickets.html()) + 1);
            calculateTotal();
            e.preventDefault();
        });

        $('#add-check-ticket').on('click', function(e) {
            var checkTickets = $('#check-tickets');
            if(parseInt(checkTickets.html()) + 1 >= 0)
                checkTickets.html(parseInt(checkTickets.html()) + 1);

            var checkNumber = $($('.check-number-template')[0]);
            var newCheckNumber = $('.check-number-template').clone();

            if($('#right-col')[0].style.display == 'none') {
                $('#right-col')[0].style.display = 'block';
                $('#left-col').attr('style', 'float: left; width: 50%');
            }

            newCheckNumber.removeClass('check-number-template');
            $(newCheckNumber[0].children[0].children[0]).removeClass('is-upgraded');
            $(newCheckNumber[0].children[0].children[0]).removeAttr('data-upgraded');
            newCheckNumber.addClass('check-number');
            newCheckNumber[0].children[0].children[0].children[0].value = '';
            newCheckNumber.appendTo(checkNumber.parent());
            
            componentHandler.upgradeDom();

            calculateTotal();

            e.preventDefault();
        });

        $('#add-free-ticket').on('click', function(e) {
            var freeTickets = $('#free-tickets');
            if(parseInt(freeTickets.html()) + 1 >= 0)
                freeTickets.html(parseInt(freeTickets.html()) + 1);
            calculateTotal();
            e.preventDefault();
        });

        $('#remove-cash-ticket').on('click', function(e) {
            var cashTickets = $('#cash-tickets');
            if(parseInt(cashTickets.html()) - 1 >= 0)
                cashTickets.html(parseInt(cashTickets.html()) - 1);
            calculateTotal();
            e.preventDefault();
        });

        $('#remove-check-ticket').on('click', function(e) {
            var checkTickets = $('#check-tickets');
            if(parseInt(checkTickets.html()) - 1 >= 0) {
                checkTickets.html(parseInt(checkTickets.html()) - 1);
                
                var checkNumbers = $('.check-number');
                var checkNumber = $(checkNumbers[checkNumbers.length - 1]);
                
                checkNumber.remove();
                
                if(parseInt(checkTickets.html()) == 0) {
                    $('#right-col')[0].style.display = 'none';
                    $('#left-col').removeAttr('style');
                }
            }

            calculateTotal();

            e.preventDefault();
        });

        $('#remove-free-ticket').on('click', function(e) {
            var freeTickets = $('#free-tickets');
            if(parseInt(freeTickets.html()) - 1 >= 0)
                freeTickets.html(parseInt(freeTickets.html()) - 1);
            calculateTotal();
            e.preventDefault();
        });

        $('#cancel-button').on('click', function(e) {
            e.preventDefault();
            changeTab('scroll-tab-1', '../ajax/tabs/table.php?id=<?= urlencode($_GET['table']) ?>');
        });

        $('#submit-button').on('click', function(e) {
            e.preventDefault();
            var totalTickets = parseInt($('#cash-tickets').html()) + parseInt($('#check-tickets').html()) + parseInt($('#free-tickets').html());
            if($('.is-invalid').length == 0 && totalTickets > 0) {
                var checkNumbersArr = [];
                $('.check-number').each(function() {
                   checkNumbersArr.push($(this)[0].children[0].children[0].children[0].value);
                });
                var checkNumbers = checkNumbersArr.join('-');
                checkNumbers = window.btoa(checkNumbers);

                $.post('../ajax/functions/add.php', {
                    "name": $('#name').val(),
                    "id-number": $('#id-number').val(),
                    "table-number": <?= $_GET['table'] ?>,
                    "cash-tickets": $('#cash-tickets').html(),
                    "check-tickets": $('#check-tickets').html(),
                    "free-tickets": $('#free-tickets').html(),
                    "check-number": checkNumbers
                }, function(data) {
                    data = JSON.parse(data);
                    if(typeof data.error != 'undefined') {
                        if(data.error == 'ERR_SEAT_LIMIT_EXCEEDED') {
                            var error = $('#error');
                            error.html('There are not enough free seats at this table.');
                            error[0].style.display = 'inline';
                        }
                    } else {
                        changeTab('scroll-tab-1', '../ajax/tabs/table.php?id=<?= urlencode($_GET['table']) ?>');
                    }
                });
            }
        });
        
        $('#id-number').on('keypress', function(e) {
            if(e.which == 13) {
                $('#name').focus();
                e.preventDefault();
            }
        });
        
        $('#name').on('keypress', function(e) {
            if(e.which == 13) {
                $('#name').blur();
                e.preventDefault();
            }
        });
        
        <?php if(LDAP_ENABLED) { ?>
            $('#id-number').on('blur', function() {
                var value = $('#id-number').val();
                if(value != '') {
                    $.get('../ajax/functions/getUser.php?sessionID=<?= $_SESSION['sessionID'] ?>&idNumber=' + encodeURIComponent(value), function(data) {
                        var error = data.getElementsByTagName('error');
                        if(error.length == 0) {
                            var name = $('#name');
                            name.val($(data).find('cn').text());
                            name.parent().addClass('is-dirty');
                            name.blur();
                        } else {
                            $('#error').html(error[0].textContent);
                            $('#error')[0].style.display = 'inline';
                        }
                    });
                }
            });
        <?php } ?>
        
        $('#id-number').focus();

        componentHandler.upgradeDom();
    });
</script>
<div class="gridless-content">
    <div class="gridless-container">
        <div id="left-col">
            <h4>Add New Transaction</h4>
            <h6 id="error" style="display:none;color:red"></h6>
            <form action="#">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input type="text" name="id-number" id="id-number" class="mdl-textfield__input" autocomplete="off">
                    <label for="id-number" class="mdl-textfield__label">ID Number</label>
                </div>
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input type="text" name="name" id="name" class="mdl-textfield__input" autocomplete="off">
                    <label for="name" class="mdl-textfield__label">Guest Name</label>
                </div>
                <ul class="mdl-list" style="margin-left:auto;margin-right:auto;width:260px">
                    <li class="mdl-list__item">
                        <span class="mdl-list__item-primary-content">
                            <i class="material-icons">attach_money</i>
                            Cash Tickets:&nbsp;<span id="cash-tickets">0</span>&nbsp;
                            <button id="add-cash-ticket" class="mdl-button mdl-js-button mdl-button--icon">
                                <i class="material-icons">add</i>
                            </button>
                            <button id="remove-cash-ticket" class="mdl-button mdl-js-button mdl-button--icon">
                                <i class="material-icons">remove</i>
                            </button>
                        </span>
                    </li>
                    <li class="mdl-list__item">
                        <span class="mdl-list__item-primary-content">
                            <i class="material-icons">payment</i>
                            Check Tickets:&nbsp;<span id="check-tickets">0</span>&nbsp;
                            <button id="add-check-ticket" class="mdl-button mdl-js-button mdl-button--icon">
                                <i class="material-icons">add</i>
                            </button>
                            <button id="remove-check-ticket" class="mdl-button mdl-js-button mdl-button--icon">
                                <i class="material-icons">remove</i>
                            </button>
                        </span>
                    </li>
                    <li class="mdl-list__item">
                        <span class="mdl-list__item-primary-content">
                            <i class="material-icons">money_off</i>
                            Free Tickets:&nbsp;<span id="free-tickets">0</span>&nbsp;
                            <button id="add-free-ticket" class="mdl-button mdl-js-button mdl-button--icon">
                                <i class="material-icons">add</i>
                            </button>
                            <button id="remove-free-ticket" class="mdl-button mdl-js-button mdl-button--icon">
                                <i class="material-icons">remove</i>
                            </button>
                        </span>
                    </li>
                    <li class="mdl-list__item">
                        <span class="mdl-list__item-primary-content">
                            <i class="material-icons">shopping_basket</i>&nbsp;
                            <span style="font-weight:bold">Total Cost:</span>&nbsp;<span id="total-cost">$0</span>
                        </span>
                    </li>
                </ul>
                <button id="cancel-button" class="mdl-button mdl-js-button mdl-js-ripple-effect">Cancel</button>
                <button id="submit-button" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect">Submit</button>
            </form>
        </div>
        <div id="right-col" style="float: left; width: 50%; display: none">
            <div id="check-numbers-container" class="gridless-container">
                <h5>Check Numbers</h5>
                <ul id="check-numbers-list" class="mdl-list">
                    <li class="check-number-template mdl-list__item">
                        <span class="mdl-list__item-primary-content">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input type="text" name="check-number" class="mdl-textfield__input" pattern="-?[0-9]*(\.[0-9]+)?" autocomplete="off">
                                <label for="check-number" class="mdl-textfield__label">Check Number</label>
                                <span class="mdl-textfield__error">Input is not a number!</span>
                            </div>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>