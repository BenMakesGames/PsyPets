<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';
require_once 'commons/houseresources.php';

$improvement = get_home_improvement_byid($action_info[2]);

if($improvement === false)
{
  echo "Error in <i>" . $this_inventory['action'] . "</i><br />\n" .
       "There is no home improvement '" . $action_info[2] . "'<br />\n";
  exit();
}

$not_enough_materials = false;

$house_stats = get_housestats_byloc($user);

$unavailable = resources_unavailable($improvement['craft_reqs']);

if(count($unavailable) == 0)
{
  if($_GET['step'] == 2)
  {
    if(expend_resources($improvement['craft_reqs'], $user['user']) === false)
    {
      echo "Failed to use up the resources demanded by this project.<br />\n";
      exit();
    }

    $command = 'INSERT INTO monster_projects (`type`, `userid`, `itemid`, `progress`, `notes`) ' .
               "VALUES ('construct', " . $user['idnum'] . ', ' . $improvement['idnum'] . ", '0', 'You started this construction.')";
    $database->FetchNone($command, 'starting project for house add-on');

    echo 'You set up the foundations for the ' . $improvement['name'] . ' project.';
    
    delete_inventory_byid($this_inventory['idnum']);

    $AGAIN_WITH_ANOTHER = true;

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Used a Blueprint', 1);
  }
}
else
  $not_enough_materials = true;

if($_GET['step'] != 2)
{
?>
This blueprint details the construction of the <?= $improvement['name'] ?>.</p>
<?php
$requireditems = take_apart(',', $improvement['craft_reqs']);

  if(count($requireditems) > 0)
  {
?>
<h5>Materials Needed</h5>
<table>
<?php
    $counts = array();
    foreach($requireditems as $item)
      $counts[$item]++;

    $row_class = begin_row_class();

    foreach($counts as $itemname=>$count)
    {
      echo '<tr class="' . $row_class . '">';

      if($unavailable[$itemname][1] > $unavailable[$itemname][0])
        echo '<td>' . item_text_link($itemname, 'failure') . '</td><td class="failure">(' . (int)$unavailable[$itemname][0] . '/' . $unavailable[$itemname][1] . ')</td>';
      else
        echo '<td>' . item_text_link($itemname) . '</td><td>(' . $count . '/' . $count . ')</td>';

      echo '</tr>';
      
      $row_class = alt_row_class($row_class);
    }
?>
</table>
<?php
  }
?>
<p>
<?php
  if($not_enough_materials)
    echo 'Unfortunately, you don\'t have the materials necessary to start this project. <i>(Materials must be in a non-protected room of your house.)</i>';
  else
    echo '<a href="/itemaction.php?idnum=' . $_GET['idnum'] . '&amp;step=2">Start project</a>';
}
?>
