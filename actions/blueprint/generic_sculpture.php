<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';
require_once 'commons/houseresources.php';

$consider_item = get_sculpture_byid($action_info[2]);

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
               "VALUES ('sculpture', " . $user['idnum'] . ', \'u:' . $user['idnum'] . '\', ' . $consider_item['idnum'] . ", '0', 'You started this sculpture project.')";
    $database->FetchNone($command, 'adding project from blueprint');

    echo 'You piece together the basis for a ' . $consider_item['makes'] . ' project.';

    delete_inventory_byid($_GET['idnum']);

    $AGAIN_WITH_ANOTHER = true;

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Used a Blueprint', 1);
  }

  $not_enough_materials = false;
}
else
  $not_enough_materials = true;

if($_GET['step'] != 2)
{
?>
This blueprint contains instructions for making <?= $consider_item['makes'] ?>.</p>
<p>You will need:</p>
<ul>
<?php
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
?>
</ul>
<p>
<?php
  if($not_enough_materials)
    echo 'Unfortunately, you don\'t have the materials necessary to start this project.  (Materials must be in a non-protected room of your house.  Items listed in red are the items you\'re missing.)';
  else
    echo '<a href="/itemaction.php?idnum=' . $_GET['idnum'] . '&step=2">Start project</a>';
}
else
{
  if($not_enough_materials)
    echo 'You don\'t have the materials necessary to start this project.';
}
?>
