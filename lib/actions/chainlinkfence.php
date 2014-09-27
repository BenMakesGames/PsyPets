<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  $user['profile_wall'] = 'fence_back.png';
  $command = 'UPDATE monster_users SET profile_wall=' . quote_smart($user['profile_wall']) . ",profile_wall_repeat='yes' WHERE idnum=" . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'itemaction.php?idnum=' . $this_inventory['idnum']);

  delete_inventory_byid($this_inventory['idnum']);

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Installed a Chain Link Fence', 1);

  echo '<p>The fence is erected in no time.</p>';

  $AGAIN_WITH_ANOTHER = true;
}
else if($_GET['step'] == 3)
{
  $size = 10;

  delete_inventory_byid($this_inventory['idnum']);

  $command = 'UPDATE monster_houses SET maxbulk=maxbulk+' . $size . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'expanding your house (deliciously)');

  echo '<p>Your house has been expanded by ' . ($size / 10) . ' units.</p>';

  $AGAIN_WITH_ANOTHER = true;
}
else
{
  echo '<h5>Profile Background</h5>';

  if($user['profile_wall'] == 'fence_back.png')
    echo '<p>You already have Chain Link Fence installed.</p>';
  else
  {
?>
      <p>Put the Chain Link Fence up?</p>
      <ul><li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&amp;step=2">Yes.</a></li></ul>
<?php
  }
?>
<h5>House Expansion</h5>
<p>Expand your house by 1 space?</p>
<ul><li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&amp;step=3">Totally.</a></li></ul>
<?php
}
?>
