<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Airship Mooring';
$THIS_ROOM = 'Airship Mooring';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/blimplib.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if($user['pvp_message'] == 'yes')
{
  $user['pvp_message'] = 'no';
  
  $command = 'UPDATE monster_users SET pvp_message=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'clearing PvP notification icon');
}

if(!addon_exists($house, 'Airship Mooring'))
{
  header('Location: /myhouse.php');
  exit();
}

if($user['show_aerosoc'] == 'no')
{
  $user['show_aerosoc'] = 'yes';
  $command = 'UPDATE monster_users SET show_aerosoc=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'reveaing the aeronautical society');

  $message = '<p class="success">The Aeronautical Society has been revealed to you!  Find it on the menu under "Services."</p>';
}

$command = 'SELECT * FROM psypets_airships WHERE ownerid=' . $user['idnum'] . ' ORDER BY name ASC';
$airships = fetch_multiple($command, 'fetching this resident\'s airships');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Airship Mooring</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Airship Mooring</h4>
<?php
echo $message;

room_display($house);
?>
     <ul>
      <li><a href="/myhouse/addon/airship_mooring_new.php">Make new Airship</a></li>
     </ul>
<?php
if(count($airships) > 0)
{
  $rowclass = begin_row_class();
?>
     <table>
      <thead>
       <tr><th></th><th></th><th>Chassis</th><th>Name</th><th>Details</th><th>Status</th></tr>
      </thead>
      <tbody>
<?php
  foreach($airships as $airship)
  {
    apply_airship_crew_bonus($airship, $user['user']);
  
    $part_chassis = get_item_byname($airship['chassis']);
    $part_list = explode(',', $airship['parts']);
    
    $details = render_airship_bonuses_as_list_xhtml($chassis[$airship['chassis']]);
    
    $effects = array();
    foreach($part_list as $item)
    {
      $details .= render_airship_bonuses_as_list_xhtml($parts[$item]);
    }
?>
      <tr class="<?= $rowclass ?>">
       <td>
        <a href="/myhouse/addon/airship_mooring_edit.php?idnum=<?= $airship['idnum'] ?>"><img src="/gfx/wrench.png" width="16" height="16" alt="modify parts" title="(modify parts)" /></a>
        <a href="/myhouse/addon/airship_mooring_crew.php?idnum=<?= $airship['idnum'] ?>"><img src="/gfx/pilot.png" width="16" height="16" alt="change crew" title="(change crew)" /></a>
       </td>
       <td class="centered"><?= item_display($part_chassis, '') ?></td>
       <td><?= $part_chassis['chassis'] ?></td>
       <td><?= airship_link($airship) ?></td>
       <td><ul class="plainlist"><?= $details ?></ul></td>
       <td><?php
    if($airship['disabled'] == 'yes')
      echo '<span class="failure">May not be used; please disassemble</span>';
    else if(!airship_can_attack($airship))
      echo '<span class="obstacle">Incomplete</span>';
    else
      echo 'Complete';
?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
