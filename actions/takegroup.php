<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/grouplib.php';

$groupid = (int)$this_inventory['data'];

$group = get_group_byid($groupid);

if($group === false)
  echo '<p>Oh god!  This group no longer exists?  Group #' . $groupid . '?  This is probably a terrible error.  Group Ownership items should never refer to non-existant groups...</p>';
else if($_GET['action'] == 'go')
{
  $members = get_group_member_ids($group['members']);
  change_group_leader($group, $user['idnum']);
  psymail_group_byarray($members, 'psypets', $group['name'] . ' has a new Group Organizer!', '{r ' . $user['display'] . '} has taken over as Group Organizer!');

  update_group_members($invitation['groupid'], $members);

  $my_groups = take_apart(',', $user['groups']);

  if(!in_array($groupid, $my_groups))
  {
    $my_groups[] = $groupid;

    update_user_groups($user['idnum'], $my_groups);
    
    echo '<p class="success">You have joined ' . $group['name'] . '!</p>';
  }

  echo '
    <p class="success">You are now the Group Organizer for ' . $group['name'] . '!</p>
    <ul>
     <li><a href="grouppage.php?id=' . $groupid . '">Go to group page</a></li>
    </ul>
  ';
}
else
{
?>
<p>Using this item will give you control of the Group "<?= $group['name'] ?>".  You will become the Group Organizer, and the existing Group Organizer, if there is one, will become a regular member.</p>
<p>Will you do it?!  Will you accept this great responsibility!?</p>
<ul>
 <li><a href="itemaction.php?itemid=<?= $this_inventory['idnum'] ?>&amp;action=go">You can bet your last Sweetie Muffin I will!</a></li>
</ul>
<?php
}
?>
