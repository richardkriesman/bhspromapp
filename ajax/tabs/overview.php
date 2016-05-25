<?php
    require('../../include/bootstrap.inc.php');

    session_start();

    requireLogin($pdo);
    requirePermissions(PERMISSIONS_READ);
?>
<script type="text/javascript">
    $('body').ready(function() {

        $('.event-table').on('click', function() {
            if($(this).html() != 'FAC TABLE') {
                var tableNumber = parseInt($(this).html().substring(9));
                $.post('../ajax/functions/getLockedState.php', { "table": tableNumber }, function(data) {
                    data = JSON.parse(data);
                    if(data.locked == false)
                        changeTab('scroll-tab-1', '../ajax/tabs/table.php?id=' + tableNumber);
                    else
                        new Snackbar('This table is currently in use. Try again in a few moments.');
                });
            }
        });

        //update badges every second
        badgeUpdater = setInterval(function() {
            $.get('../ajax/functions/updateBadges.php?sessionID=<?= $_SESSION['sessionID'] ?>', function(data) {
                for(var i = 0; i < data.length; i++) {
                    var table = $('#table-' + (i + 1));
                    table.attr('data-badge', data[i]);
                    table.removeClass('badge-green');
                    table.removeClass('badge-yellow');
                    table.removeClass('badge-red');
                    if(data[i] >= 7) {
                        if (table.hasClass('badge-yellow'))
                            table.removeClass('badge-yellow');
                        if (table.hasClass('badge-red'))
                            table.removeClass('badge-red');
                        if(!table.hasClass('badge-green'))
                            table.addClass('badge-green');
                    } else if(data[i] >= 3) {
                        if (table.hasClass('badge-green'))
                            table.removeClass('badge-green');
                        if (table.hasClass('badge-red'))
                            table.removeClass('badge-red');
                        if (!table.hasClass('badge-yellow'))
                            table.addClass('badge-yellow');
                    } else {
                        if (table.hasClass('badge-green'))
                            table.removeClass('badge-green');
                        if (table.hasClass('badge-yellow'))
                            table.removeClass('badge-yellow');
                        if (!table.hasClass('badge-red'))
                            table.addClass('badge-red');
                    }
                }
            });
        }, 1000);

    });
</script>
<div class="grid gridster">
    <ul>
        <li data-row="5" data-col="1" data-sizex="1" data-sizey="1"></li>
        <li id="table-63" class="event-object event-table mdl-badge mdl-badge--overlap" style="[data-badge]::after { background: green }" data-row="5" data-col="2" data-sizex="1" data-sizey="1">TABLE<br/>63</li>
        <li id="table-57" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="3" data-sizex="1" data-sizey="1">TABLE<br/>57</li>
        <li id="table-51" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="4" data-sizex="1" data-sizey="1">TABLE<br/>51</li>
        <li id="table-45" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="5" data-sizex="1" data-sizey="1">TABLE<br/>45</li>
        <li id="table-39" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="6" data-sizex="1" data-sizey="1">TABLE<br/>39</li>
        <li id="table-33" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="7" data-sizex="1" data-sizey="1">TABLE<br/>33</li>
        <li id="table-27" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="8" data-sizex="1" data-sizey="1">TABLE<br/>27</li>
        <li id="table-21" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="9" data-sizex="1" data-sizey="1">TABLE<br/>21</li>
        <li id="table-15" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="10" data-sizex="1" data-sizey="1">TABLE<br/>15</li>
        <li id="table-9" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="11" data-sizex="1" data-sizey="1">TABLE<br/>9</li>
        <li id="table-4" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="12" data-sizex="1" data-sizey="1">TABLE<br/>4</li>
        <li id="table-3" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="13" data-sizex="1" data-sizey="1">TABLE<br/>3</li>
        <li id="table-2" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="14" data-sizex="1" data-sizey="1">TABLE<br/>2</li>
        <li id="table-1" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="5" data-col="15" data-sizex="1" data-sizey="1">TABLE<br/>1</li>

        <li class="event-object" data-row="6" data-col="1" data-sizex="1" data-sizey="1">FAC<br/>TABLE</li>
        <li id="table-64" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="6" data-col="2" data-sizex="1" data-sizey="1">TABLE<br/>64</li>
        <li id="table-58" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="6" data-col="3" data-sizex="1" data-sizey="1">TABLE<br/>58</li>
        <li id="table-52" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="6" data-col="4" data-sizex="1" data-sizey="1">TABLE<br/>52</li>
        <li id="table-46" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="6" data-col="5" data-sizex="1" data-sizey="1">TABLE<br/>46</li>
        <li id="table-40" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="6" data-col="6" data-sizex="1" data-sizey="1">TABLE<br/>40</li>
        <li id="table-34" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="6" data-col="7" data-sizex="1" data-sizey="1">TABLE<br/>34</li>
        <li id="table-28" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="6" data-col="8" data-sizex="1" data-sizey="1">TABLE<br/>28</li>
        <li id="table-22" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="6" data-col="9" data-sizex="1" data-sizey="1">TABLE<br/>22</li>
        <li id="table-16" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="6" data-col="10" data-sizex="1" data-sizey="1">TABLE<br/>16</li>
        <li id="table-10" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="6" data-col="11" data-sizex="1" data-sizey="1">TABLE<br/>10</li>

        <li class="event-object" data-row="6" data-col="12" data-sizex="4" data-sizey="4">STAGE</li>

        <li class="event-object" data-row="7" data-col="1" data-sizex="1" data-sizey="1">FAC<br/>TABLE</li>
        <li id="table-65" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="7" data-col="2" data-sizex="1" data-sizey="1">TABLE<br/>65</li>
        <li id="table-59" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="7" data-col="3" data-sizex="1" data-sizey="1">TABLE<br/>59</li>
        <li id="table-53" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="7" data-col="4" data-sizex="1" data-sizey="1">TABLE<br/>53</li>
        <li id="table-47" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="7" data-col="5" data-sizex="1" data-sizey="1">TABLE<br/>47</li>
        <li id="table-41" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="7" data-col="6" data-sizex="1" data-sizey="1">TABLE<br/>41</li>
        <li id="table-35" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="7" data-col="7" data-sizex="1" data-sizey="1">TABLE<br/>35</li>
        <li id="table-29" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="7" data-col="8" data-sizex="1" data-sizey="1">TABLE<br/>29</li>
        <li id="table-23" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="7" data-col="9" data-sizex="1" data-sizey="1">TABLE<br/>23</li>
        <li id="table-17" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="7" data-col="10" data-sizex="1" data-sizey="1">TABLE<br/>17</li>
        <li id="table-11" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="7" data-col="11" data-sizex="1" data-sizey="1">TABLE<br/>11</li>

        <li class="event-object" data-row="8" data-col="1" data-sizex="1" data-sizey="1">FAC<br/>TABLE</li>
        <li id="table-66" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="8" data-col="2" data-sizex="1" data-sizey="1">TABLE<br/>66</li>
        <li id="table-60" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="8" data-col="3" data-sizex="1" data-sizey="1">TABLE<br/>60</li>
        <li id="table-54" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="8" data-col="4" data-sizex="1" data-sizey="1">TABLE<br/>54</li>
        <li id="table-48" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="8" data-col="5" data-sizex="1" data-sizey="1">TABLE<br/>48</li>
        <li id="table-42" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="8" data-col="6" data-sizex="1" data-sizey="1">TABLE<br/>42</li>
        <li id="table-36" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="8" data-col="7" data-sizex="1" data-sizey="1">TABLE<br/>36</li>
        <li id="table-30" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="8" data-col="8" data-sizex="1" data-sizey="1">TABLE<br/>30</li>
        <li id="table-24" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="8" data-col="9" data-sizex="1" data-sizey="1">TABLE<br/>24</li>
        <li id="table-18" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="8" data-col="10" data-sizex="1" data-sizey="1">TABLE<br/>18</li>
        <li id="table-12" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="8" data-col="11" data-sizex="1" data-sizey="1">TABLE<br/>12</li>

        <li class="event-object" data-row="9" data-col="1" data-sizex="1" data-sizey="1">FAC<br/>TABLE</li>
        <li id="table-67" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="9" data-col="2" data-sizex="1" data-sizey="1">TABLE<br/>67</li>
        <li id="table-61" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="9" data-col="3" data-sizex="1" data-sizey="1">TABLE<br/>61</li>
        <li id="table-55" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="9" data-col="4" data-sizex="1" data-sizey="1">TABLE<br/>55</li>
        <li id="table-49" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="9" data-col="5" data-sizex="1" data-sizey="1">TABLE<br/>49</li>
        <li id="table-43" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="9" data-col="6" data-sizex="1" data-sizey="1">TABLE<br/>43</li>
        <li id="table-37" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="9" data-col="7" data-sizex="1" data-sizey="1">TABLE<br/>37</li>
        <li id="table-31" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="9" data-col="8" data-sizex="1" data-sizey="1">TABLE<br/>31</li>
        <li id="table-25" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="9" data-col="9" data-sizex="1" data-sizey="1">TABLE<br/>25</li>
        <li id="table-19" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="9" data-col="10" data-sizex="1" data-sizey="1">TABLE<br/>19</li>
        <li id="table-13" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="9" data-col="11" data-sizex="1" data-sizey="1">TABLE<br/>13</li>

        <li data-row="10" data-col="1" data-sizex="1" data-sizey="1"></li>
        <li id="table-68" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="2" data-sizex="1" data-sizey="1">TABLE<br/>68</li>
        <li id="table-62" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="3" data-sizex="1" data-sizey="1">TABLE<br/>62</li>
        <li id="table-56" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="4" data-sizex="1" data-sizey="1">TABLE<br/>56</li>
        <li id="table-50" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="5" data-sizex="1" data-sizey="1">TABLE<br/>50</li>
        <li id="table-44" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="6" data-sizex="1" data-sizey="1">TABLE<br/>44</li>
        <li id="table-38" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="7" data-sizex="1" data-sizey="1">TABLE<br/>38</li>
        <li id="table-32" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="8" data-sizex="1" data-sizey="1">TABLE<br/>32</li>
        <li id="table-26" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="9" data-sizex="1" data-sizey="1">TABLE<br/>26</li>
        <li id="table-20" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="10" data-sizex="1" data-sizey="1">TABLE<br/>20</li>
        <li id="table-14" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="11" data-sizex="1" data-sizey="1">TABLE<br/>14</li>
        <li id="table-8" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="12" data-sizex="1" data-sizey="1">TABLE<br/>8</li>
        <li id="table-7" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="13" data-sizex="1" data-sizey="1">TABLE<br/>7</li>
        <li id="table-6" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="14" data-sizex="1" data-sizey="1">TABLE<br/>6</li>
        <li id="table-5" class="event-object event-table mdl-badge mdl-badge--overlap" data-row="10" data-col="15" data-sizex="1" data-sizey="1">TABLE<br/>5</li>
    </ul>
</div>
<script type="text/javascript">
    $(function() {

        var grid = $(".gridster ul").gridster({
            widget_margins: [10, 10],
            widget_base_dimensions: [60, 60]
        }).data('gridster');

        grid.disable();

    });
</script>