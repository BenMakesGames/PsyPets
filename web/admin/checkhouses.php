<?php
$IGNORE_MAINTENANCE = true;

require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once "commons/houselib.php";

if($admin["manageaccounts"] != "yes")
{
  Header("Location: /admin/tools.php");
  exit();
}

$command = "SELECT * FROM monster_users WHERE 1";
$result = mysql_query($command);

if(!$result)
{
  echo "Error in <i>$command</i><br />\n" .
       mysql_error() . "<br />\n";
  exit();
}

while($this_user = mysql_fetch_assoc($result))
{
  $command = 'SELECT * FROM monster_houses WHERE userid=' . (int)$this_user['idnum'] . ' LIMIT 1';
  $house_result = mysql_query($command);

  if(!$house_result)
  {
    echo "Error in <i>$command</i><br />\n" .
         mysql_error() . "<br />\n";
    exit();
  }

  if(mysql_num_rows($house_result) == 0)
  {
    add_house($this_user["idnum"], $this_user["homesize"]);
    echo "Created a house for ". $this_user["display"] . ".<br />\n";
  }

  mysql_free_result($house_result);
}

mysql_free_result($result);
?>