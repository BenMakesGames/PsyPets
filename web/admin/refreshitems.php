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

$items = fetch_multiple("SELECT * FROM monster_items WHERE durability>0");

foreach($items as $this_item)
{
  echo "Refreshing " . $this_item["itemname"] . "... ";

  fetch_none("UPDATE monster_inventory SET health=" . $this_item["durability"] . " WHERE itemname=" . quote_smart($this_item["itemname"]));

  echo "done.<br />\n";
}
