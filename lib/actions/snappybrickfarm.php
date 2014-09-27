<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';
require_once 'commons/houseresources.php';

$bricks = 7;

$improvement = get_home_improvement_byid(26);

$not_enough_materials = false;

$house_stats = get_housestats_byloc($user);
$unavailable = resources_unavailable($improvement['craft_reqs']);

if(count($unavailable) > 0)
  $not_enough_materials = true;

if($_GET['action'] == 'takeapart')
{
  delete_inventory_byid($this_inventory['idnum']);

  for($x = 0; $x < $bricks; ++$x)
    add_inventory($user['user'], '', 'Snappy Bricks', '', $this_inventory['location']);

  $AGAIN_WITH_ANOTHER = true;

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Took Apart a Snappy Brick Construction', 1);
  
  echo '<p>You take the ' . $this_inventory['itemname'] . ' apart into its constituent Snappy Bricks.</p>';
}
else if($_GET['action'] == 'study' && !$not_enough_materials)
{
  if(expend_resources($improvement['craft_reqs'], $user['user']) === false)
  {
    echo "Failed to use up the resources demanded by this project.<br />\n";
    exit();
  }

  $command = 'INSERT INTO monster_projects (`type`, `userid`, `itemid`, `progress`, `notes`) ' .
             "VALUES ('construct', " . $user['idnum'] . ', ' . $improvement['idnum'] . ", '0', 'You started this construction.')";
  $database->FetchNone($command, 'starting project for house add-on');

  echo '<p>You set up the foundations for the ' . $improvement['name'] . ' project.</p>';

  delete_inventory_byid($this_inventory['idnum']);

  $AGAIN_WITH_ANOTHER = true;

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Used a Blueprint', 1);
}
else
{
  echo '
    <p>What a detailed construction!  There\'s fences, a little chicken, a silo... everything you\'d expect on a farm!</p>
    <p>What will you do with it?</p>
    <h5>Take Apart</h5>
    <p>This will disassemble the construction, yielding 7 Snappy Bricks.</p>
    <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=takeapart">Yeah!  Smash it to pieces!  Hahahaha!</a></li></ul>
    <h5>Study</h5>
    <p>Using this as a model, you could probably construct an <em>actual</em> farm!</p>
    <p>I know it sounds hard to believe, but it\'s true!  All you\'d need is...</p>
    <table>
  ';

  $requireditems = take_apart(',', $improvement['craft_reqs']);

  $counts = array();
  foreach($requireditems as $item)
    $counts[$item]++;

  $row_class = begin_row_class();

  foreach($counts as $itemname=>$count)
  {
    echo '<tr class="' . $row_class . '">';

    if($unavailable[$itemname][1] > $unavailable[$itemname][0])
      echo '<td><span class="failure">' . $itemname . '</span></td><td><span class="failure">(' . (int)$unavailable[$itemname][0] . '/' . $unavailable[$itemname][1] . ')</span></td>';
    else
      echo '<td>' . $itemname . '</td><td>(' . $count . '/' . $count . ')</td>';

    echo '</tr>';

    $row_class = alt_row_class($row_class);
  }

  echo '</table>';
  
  if(!$not_enough_materials)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=study">Neat!  I\'ve always wanted a Farm Add-on for my house!</a></li></ul>';
}
?>
