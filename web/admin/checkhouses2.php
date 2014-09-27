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

// must be at least admin level 5 to view this page
if($admin["manageaccounts"] != "yes")
{
  Header("Location: /admin/tools.php");
  exit();
}

$command = "SELECT * FROM monster_houses WHERE 1";
$result = mysql_query($command);

if(!$result)
{
  echo "Error in <i>$command</i><br />\n" .
       mysql_error() . "<br />\n";
  exit();
}

while($this_house = mysql_fetch_assoc($result))
{
  $owner = get_user_byid($this_house["userid"]);
  if($owner === false)
    continue;

  $command = "UPDATE monster_houses SET maxbulk=" . $owner["homesize"] . " WHERE idnum=" . $this_house["idnum"] . " LIMIT 1";
  $house_result = mysql_query($command);

  if(!$house_result)
  {
    echo "admincheckhouses2.php<br />\n" .
         "Error in <i>$command</i><br />\n" .
         mysql_error() . "<br />\n";
    exit();
  }

  echo "Updated " . $owner["display"] . "'s house - maxbulk=" . $owner["homesize"] . "<br />\n";
}

mysql_free_result($result);
?>