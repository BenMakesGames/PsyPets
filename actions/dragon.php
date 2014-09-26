<?php
if($okay_to_be_here !== true)
  exit();

$valid_items = array(
  'Red Dragon' => 'reddragon.png',
  'Green Dragon' => 'greendragon.png',
  'White Dragon' => 'whitedragon.png',
);

if(!array_key_exists($this_inventory['itemname'], $valid_items))
  die('Terrible item error!  Eepzors!');

if($user['cornergraphic'] == '')
{
  if($_GET['step'] == 2)
  {
    $command = 'UPDATE monster_users SET cornergraphic=\'' . $valid_items[$this_inventory['itemname']] . '\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'cobwebbing profile');

    echo '
      <p>A ' . $this_inventory['itemname'] . ' swoops in to settle down in the corner of your profile.</p>
      <p>Sweeeeet.</p>
    ';

    delete_inventory_byid($this_inventory['idnum']);
  }
  else
  {
    echo '
      <p>Would you like to call <em>A DRAGON?!</em></p>
      <ul>
       <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;step=2">Sure.  I don\'t have anything else in the corner of my profile, anyway.</a></li>
      </ul>
    ';
  }
}
else
{
  if($_GET['step'] == 2)
  {
    $command = 'UPDATE monster_users SET cornergraphic=\'\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'cobwebbing profile');

    echo '
      <p>A ' . $this_inventory['itemname'] . ' swoops in to settle down in the corner of your profile, but stumbles all over the stuff you\'ve got hanging up there already, destroying it.</p>
      <p>Annoyed, it flies off.</p>
    ';

    delete_inventory_byid($this_inventory['idnum']);
  }
  else
  {
    echo '
      <p>Would you like to call <em>A DRAGON?!</em></p>
      <p>You\'ve already got something hanging in the corner, not that that\'s a problem for a dragon...</p>
      <p>It\'ll probably be irritated about all the trouble, though, and refuse to stick around afterwards.</p>
      <ul>
       <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;step=2">The spectacle alone is sure to be worth it!</a></li>
      </ul>
    ';
  }
}
?>
