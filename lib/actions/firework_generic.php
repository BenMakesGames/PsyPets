<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/fireworklib.php';
require_once 'commons/threadfunc.php';

$fireworkid = (int)$action_info[2];
$quantity = max(1, (int)$action_info[3]);

if($fireworkid > 0)
{
  if($_GET['action'] == 2)
  {
    delete_inventory_byid($this_inventory['idnum']);

    $supply = get_firework_supply($user);

    gain_firework($supply, $fireworkid, $quantity);

    $command = 'UPDATE monster_users SET fireworks=' . quote_smart(render_firework_data_string($supply)) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'removing firework from player');

    echo '
      <p>The ' . $this_inventory['itemname'] . ' is readied!</p>
      <ul>
       <li>To apply it to a Plaza post, find the post you want, and click the <img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /> icon.</li>
       <li>To apply it to a room in your house, visit that room, and click the <img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /> icon.</li>
       <li>To apply it to your profile, visit your profile, and click the <img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /> icon.</li>
      </ul>
    ';
    
    $AGAIN_WITH_ANOTHER = true;
  }
  else
  {
    echo '
      <p>Doing this will consume the ' . $this_inventory['itemname'] . ', but will give you a pretty graphic to apply to a Plaza post or room of your house, or to change your profile background!</p>
    ';
    
    if($quantity > 1)
      echo '<p>(Actually, it will give you the graphic for use ' . $quantity . ' times!)</p>';
    
    echo '
      <ul>
       <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=2">"' . $action_info[0] . '" for real this time!</a></li>
      </ul>
    ';

    if($this_inventory['itemname'] == 'Death\'s Head' && $this_inventory['idnum'] % 4 == 0)
      echo '<p>The following is written on the side of the firework: "If you look long enough into the void the void begins to look back through you.  Red, Chocolate, Chocolate, Red."</p>';

  }
}
else
  echo 'This is a badly-coded firework!  An administrator should probably be alerted.';
?>
