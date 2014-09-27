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

// must be at least admin level 5 to view this page
if($admin["manageaccounts"] != "yes")
{
  Header('Location: /admin/tools.php');
  exit();
}

$command = "SELECT * FROM monster_items WHERE 1";
$result = mysql_query($command);

if(!$result)
{
  echo "Error in <i>$command</i><br />\n" .
       mysql_error() . "<br />\n";
  exit();
}

while($this_item = mysql_fetch_assoc($result))
{
  if($this_item["durability"] == 0)
    continue;

  echo "Refreshing " . $this_item["itemname"] . "... ";

  $command = "UPDATE monster_inventory SET health=" . $this_item["durability"] . " WHERE itemname=" . quote_smart($this_item["itemname"]);
  $this_result = mysql_query($command);
  if(!$this_result)
  {
    echo "Error in <i>$command</i><br />\n" .
         mysql_error() . "<br />\n";
    exit();
  }

  echo "done.<br />\n";
}

mysql_free_result($result);
?>