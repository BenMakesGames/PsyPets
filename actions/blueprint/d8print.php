<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';
require_once 'commons/houseresources.php';

$consider_item = get_item_byname('8-Sided Die Blueprint');

if($consider_item === false)
{
  echo "There is no item called '" . $action_info[2] . "'<br />\n";
  exit();
}

$house_stats = get_housestats_byloc($user);

if(resources_available('Paper,Blue Dye') === true)
{
  if($_GET['step'] == 2)
  {
    if(expend_resources('Paper,Blue Dye', $user['user']) === false)
    {
      echo "Failed to use up the resources demanded by this project.<br />\n";
      exit();
    }

    add_inventory($user['user'], 'u:' . $user['idnum'], "8-Sided Die Blueprint", "Printed with a " . $this_inventory['itemname'], $this_inventory['location']);

    echo "*vzzzt! vzzzt! vzzzt!*</p>\n" .
         "<p><i>After a 2-minute wait you are finally rewarded with an 8-Sided Die Blueprint.</i></p>\n";

    $AGAIN_WITH_SAME = true;
  }

  $not_enough_materials = false;
}
else
  $not_enough_materials = true;

if($_GET['step'] != 2)
{
?>
The Model 700+ 8-Sided Die Blueprint Printer has a tiny LCD which is currently showing "RDY".</p>
<p>According to the manual, you will need to hand-feed the printer a single sheet of paper and a bottle of blue ink.</p>
<p>
<?php
  if($not_enough_materials)
    echo 'Unfortunately, you don\'t have the materials necessary to do any printing.  (Materials must be in a non-protected room of your house.)';
  else
    echo '<a href="/itemaction.php?idnum=' . $_GET['idnum'] . '&amp;step=2">Press the "GO" button</a>';
}
else
{
  if($not_enough_materials)
    echo "You don't have the materials necessary to any printing.";
}
?>
