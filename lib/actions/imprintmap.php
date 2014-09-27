<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/utility.php';
require_once 'commons/grouplib.php';

$groups = take_apart(',', $user['groups']);

$townid = (int)$_GET['group'];
$okay = false;

if(count($groups) > 0)
{
  foreach($groups as $groupid)
  {
    $group = get_group_byid($groupid);
    if($group['leaderid'] == $user['idnum'])
    {
      $group_options[] = $group;
      if($groupid == $townid)
        $okay = true;
    }
  }
}

if($townid > 0 && $okay)
{
  $command = 'SELECT data FROM psypets_towns WHERE groupid=' . $townid . ' LIMIT 1';
  $town = $database->FetchSingle($command, 'fetching town data');

  $scroll_data = $townid . ';' . $now . ';' . $town['data'];
  
  delete_inventory_byid($this_inventory['idnum']);

  $id = add_inventory($user['user'], 'u:' . $user['idnum'], 'Town Map', '', $this_inventory['location']);
  
  $command = 'UPDATE monster_inventory SET data=' . quote_smart($scroll_data) . ' WHERE idnum=' . $id . ' LIMIT 1';
  $database->FetchNone($command, 'recording town data');

  $AGAIN_WITH_ANOTHER = true;
  
  echo 'You copy the map carefully...</p><p><i>(You received a Town Map of this group\'s town.)</i>';
}
else if(count($group_options) > 0)
{
?>
Which group's town map will you copy?</p>
<ul>
<?php
  foreach($group_options as $this_group)
  {
?>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&group=<?= $this_group['idnum'] ?>"><?= $this_group['name'] ?></a></li>
<?php
  }
?>
</ul>
<?php
}
else
  echo 'You must be a group organizer to use this item.';

?>
