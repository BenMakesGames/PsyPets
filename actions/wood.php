<?php
if($okay_to_be_here !== true)
  exit();

if($user['cornergraphic'] == '')
{
  if($_GET['step'] == 2)
  {
    $command = 'UPDATE monster_users SET cornergraphic=\'support.png\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'supporting profile? :P');

    echo 'You nail the wood up in a corner of your profile.  I mean, to support your house.  Because this text is in-character &gt;_&gt;';

    delete_inventory_byid($this_inventory['idnum']);
  }
  else
  {
    echo 'Want to nail the wood up in the corner of your profile?  I mean, your house?  You know, to add support?  It\'d be awful if it collapsed in on itself.</p>';
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2">Um... sure &gt;_&gt;</a></li></ul>';
  }
}
else
  echo 'You\'ve already got something in the corner.';
?>
