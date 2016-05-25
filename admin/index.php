<?php
    require('../include/bootstrap.inc.php');

    session_start();

	requireLogin($pdo);
	requirePermissions(PERMISSIONS_ADMIN);

	
?>
<!DOCTYPE html>
<html>
<head>
<?php include_once('../include/styles.inc.php'); ?>
<title>BHSPromapp Administration Panel</title>
<style>
    body {
        text-align:center;
        font-family: 'Droid Sans', sans-serif;
		background-size:auto 100%;
        background-repeat:no-repeat;
        background-position:center;
        background-attachment:fixed;
        background-color:#000000;
    }
    
    #overlay {
        position:absolute;
        top:50%;
        left:50%;
        margin-top:-187px;
        margin-left:-150px;
    }
</style>
</head>
<body>
<!-- Overlay -->
<div id="overlay" style="width:300px;height:375px;-webkit-border-radius: 20px;-moz-border-radius: 20px;border-radius: 20px;border:2px solid #9C9C9C;background-color:#E8E8E8;">
    <p style="font-size:24px;font-weight:bold">Administration Panel</p>
    <div style="overflow:auto;height:285px;font-size:12px">
        <p><button onclick="window.location.replace('events.php'); return false">Edit Events</button></p>
        <p><button onclick="window.location.replace('users.php'); return false">Edit Users</button></p>
        <?php
        if($_SESSION['permissionLevel'] > 2) {
            echo '<p><button onclick="window.location.replace(\'permissions.php\'); return false">Edit Admin Permissions</button></p>';
        }
        ?>
    </div>
    <div style="position:relative;bottom:40px">
        <p><button onclick="window.close(); return false">Return to Table View</button></p>
    </div>
</div>
<!-- End Overlay -->
</body>
</html>