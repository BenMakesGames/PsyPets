<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';
require_once 'commons/houseresources.php';

$consider_item = get_item_byname($action_info[2]);

if($consider_item === false)
{
  echo "There is no item called '" . $action_info[2] . "'<br />\n";
  exit();
}

$house_stats = get_housestats_byloc($user);

if(resources_available($consider_item['recycle_for']) === true)
{
  if($_GET['step'] == 2)
  {
    if(expend_resources($consider_item['recycle_for'], $user['user']) === false)
    {
      echo "Failed to use up the resources demanded by this project.<br />\n";
      exit();
    }

    add_inventory($user['user'], 'u:' . $user['idnum'], $consider_item['itemname'], "Printed with a " . $this_inventory['itemname'], $this_inventory['location']);

    echo "<p><i>The printer makes a bunch of noise before finally producing a bound copy of " . $consider_item['itemname'] . ".</i></p>\n";
    
    $AGAIN_WITH_SAME = true;
  }

  $not_enough_materials = false;
}
else
  $not_enough_materials = true;

if($_GET['step'] != 2)
{
?>
To print a copy of the book, you will need:</p>
<ul>
<?php
$requirements = explode(',', $consider_item['recycle_for']);
foreach($requirements as $ingredient)
  echo "<li>$ingredient</li>\n";
?>
</ul>
<p>
<?php
  if($not_enough_materials)
    echo "Unfortunately, you don't have the materials necessary to do any printing (resources useable from house only).";
  else
    echo "<a href=\"itemaction.php?idnum=" . $_GET['idnum'] . "&step=2\">Start printing</a>";
}
else
{
  if($not_enough_materials)
    echo "You don't have the materials necessary to print.";
}
?>
