<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

require_once 'commons/admincheck.php';
require_once "commons/dbconnect.php";

$command = "UPDATE monster_users SET sessionid=0 WHERE 1";
$result = mysql_query($command);

if(!$result)
  echo mysql_error() . "<br />\n";
else
  echo "success!<br />\n";
?>