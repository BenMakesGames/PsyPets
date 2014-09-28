<?php
$IGNORE_MAINTENANCE = true;

require_once 'commons/init.php';

require_once 'commons/admincheck.php';
require_once "commons/dbconnect.php";

if($admin['manageaccounts'] != 'yes')
{
    header('Location: /admin/tools.php');
    exit();
}

fetch_none("UPDATE monster_users SET sessionid=0");
?>