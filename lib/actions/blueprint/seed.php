<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';
require_once 'commons/houseresources.php';

$consider_item = get_gardening_byid($action_info[2]);

if($consider_item === false)
{
  echo "Error in <i>" . $this_inventory['action'] . "</i><br />\n" .
       "There is no project #" . $action_info[2] . "<br />\n";
  exit();
}

$house_stats = get_housestats_byloc($user);

$unavailable = resources_unavailable($consider_item['ingredients']);

if(count($unavailable) == 0)
{
  if($_GET['step'] == 2)
  {
    if(expend_resources($consider_item['ingredients'], $user['user']) === false)
    {
      echo "Failed to use up the resources demanded by this project.<br />\n";
      exit();
    }

    $command = 'INSERT INTO monster_projects (`type`, `userid`, `creator`, `projectid`, `progress`, `notes`) ' .
               "VALUES ('gardening', " . $user['idnum'] . ', \'u:' . $user['idnum'] . '\', ' . $consider_item['idnum'] . ", '0', 'You started this gardening project.')";
    $database->FetchNone($command, 'adding project from seed');

    echo '<p>You plant the ' . $this_inventory['itemname'] . '...</p>';

    delete_inventory_byid($_GET['idnum']);

    $AGAIN_WITH_ANOTHER = true;

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Planted a Seed', 1);
  }

  $not_enough_materials = false;
}
else
  $not_enough_materials = true;

if($_GET['step'] != 2)
{
  echo '<p>With this ' . $this_inventory['itemname'] . ' as the seed, you could grow a <a href="encyclopedia2.php?item=' . link_safe($consider_item['makes']) . '">' . $consider_item['makes'] . '</a>!</p>';

  if($consider_item['ingredients'] != '')
  {
    echo '<p>You will also need:</p><ul>';

    $requirements = explode(',', $consider_item['ingredients']);
    foreach($requirements as $ingredient)
    {
      if($unavailable[$ingredient][1] > $unavailable[$ingredient][0])
      {
        echo '<li>' . item_text_link($ingredient, 'failure') . '</li>';
        $unavailable[$ingredient][1]--;
      }
      else
        echo '<li>' . item_text_link($ingredient) . '</li>';
    }

    echo '</ul><p>';

    if($not_enough_materials)
      echo 'Unfortunately, you don\'t have the materials necessary to plant this ' . $this_inventory['itemname'] . '.  (Materials must be in a non-protected room of your house.  Items listed in red are the items you\'re missing.)';
    else
      echo '<a href="/itemaction.php?idnum=' . $_GET['idnum'] . '&amp;step=2">Plant ' . $this_inventory['itemname'] . '</a>';

    echo '</p>';
  }
}
else
{
  if($not_enough_materials)
    echo '<p>You don\'t have the materials necessary to start this project.</p>';
}
?>
