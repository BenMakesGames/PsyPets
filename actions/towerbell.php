<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/towerlib.php';

$tower = get_tower_byuser($user['idnum']);

if($tower === false)
  echo '<p>You don\'t have a Tower to put the Tower Bell in...</p>';
else if($tower['bell'] == 'yes')
  echo '<p>Your Tower already has a Tower Bell.</p>';
else if($_GET['action'] == 'attach')
{
  delete_inventory_byid($this_inventory['idnum']);
  attach_bell($user['idnum']);
  
  echo '
    <p>It\'s done!</p>
    <p>Congratulations!  You win the game!</p>
    <p>Nah, I was just messin\' with you.  You don\'t win the game for doing that.</p>
  ';
}
else
{
  echo '
    <p>Put this Tower Bell in your Tower?</p>
    <ul>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=attach">-k-!</a></li>
    </ul>
  ';
}
?>
