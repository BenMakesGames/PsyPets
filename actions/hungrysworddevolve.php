<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  $command = 'UPDATE monster_inventory SET itemname=\'Hungry Cherub (level 0)\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating item');
  
  require_once 'commons/fireworklib.php';
  require_once 'commons/threadfunc.php';

  $supply = get_firework_supply($user);

  gain_firework($supply, 7, 3);

  $command = 'UPDATE monster_users SET fireworks=' . quote_smart(render_firework_data_string($supply)) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'removing firework from player');

  echo '
    <p>The pretty graphics are ready!</p>
    <ul>
     <li>To apply one to a Plaza post, find the post you want, and click the <img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /> icon.</li>
     <li>To apply one to a room in your house, visit that room, and click the <img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /> icon.</li>
     <li>To apply one to your profile, visit your profile, and click the <img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /> icon.</li>
    </ul>
  ';
}
else
{
?>
<p>Performing this action will reduce the Sated Cherub to a Hungry Cherub (level 0), and provide you with 3 readied "fireworks" for application to Plaza posts, your profile, or even a room of your house.</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Preeeeeety! (Go for it.)</a></li>
</ul>
<?php
}
?>
