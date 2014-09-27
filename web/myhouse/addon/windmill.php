<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Windmill';
$THIS_ROOM = 'Windmill';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

if(!addon_exists($house, 'Windmill'))
{
  header('Location: /myhouse.php');
  exit();
}

$inventory = get_houseinventory_byuser_forpets($user['user']);

$wheat = 0;
$logs = 0;

foreach($inventory as $i)
{
  if($i['itemname'] == 'Wheat')
    $wheat++;
  else if($i['itemname'] == 'Log')
    $logs++;
}

if($_POST['mill'] == 'wood')
{
  $amount = (int)$_POST['amount'];

  if($amount <= 0)
    $message = '<p class="failure">You can\'t saw fewer than one Log...</p>';
  else if($amount > $logs)
    $message = '<p class="failure">You do not have ' . $amount . ' Wheat.</p>';
  else
  {
    $used = delete_inventory_fromhome($user['user'], 'Log', $amount);

    if($used == 0)
      $message = '<p class="failure">Could not use up any Wood...</p>';
    else
    {
      $logs -= $used;

      $yield = $used * 4;

      add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Wood', 'Sawed at ' . $user['display'] . "'s windmill", 'home', $yield);

      $message = '<p class="success">Sawing yielded ' . $yield . ' Wood.</p>';
    }
  }
}
else if($_POST['mill'] == 'wheat')
{
  $amount = (int)$_POST['amount'];

  if($amount <= 0)
    $message = '<p class="failure">You can\'t grind fewer than one Wheat...</p>';
  else if($amount > $wheat)
    $message = '<p class="failure">You do not have ' . $amount . ' Wheat.</p>';
  else
  {
    $used = delete_inventory_fromhome($user['user'], 'Wheat', $amount);
    
    if($used == 0)
      $message = '<p class="failure">Could not use up any Wheat...</p>';
    else
    {
      $wheat -= $used;

      $yield = floor($used * 3 / 2);

      add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Flour', 'Ground at ' . $user['display'] . "'s flourmill", 'home', $yield);

      $message = '<p class="success">Grinding yielded ' . $yield . ' Flour.</p>';
    }
  }
}
else if($_POST['action'] == 'addprofile')
{
  $user['profile_wall'] = 'windmill_back.png';
  $command = 'UPDATE monster_users SET profile_wall=' . quote_smart($user['profile_wall']) . ',profile_wall_repeat=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'addon_windmill.php');
}
else if($_POST['action'] == 'removeprofile')
{
  $user['profile_wall'] = '';
  $command = 'UPDATE monster_users SET profile_wall=' . quote_smart($user['profile_wall']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'addon_windmill.php');
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Windmill</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   function check_uncheck(min, max, offset)
   {
     for(i = min; i <= max; ++i)
     {
       document.homeaction.elements[i + 5 + offset].checked = document.homeaction.elements[min + 4 + offset].checked;
     }
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Windmill</h4>
<?= $message ?>
<?php
room_display($house);
?>
     <form method="post">
<?php
if($user['profile_wall'] == 'windmill_back.png')
{
?>
     <p><input type="hidden" name="action" value="removeprofile" /><input type="submit" value="Remove from Profile" class="bigbutton" /></p>
<?php
}
else
{
?>
     <p><input type="hidden" name="action" value="addprofile" /><input type="submit" value="Add to Profile" class="bigbutton" /></p>
<?php
}
?>
     </form>
     <h5>Woodmill</h5>
     <p>You can turn Logs into Wood efficiently.</p>
<?php
if($logs > 0)
{
?>
     <p>You have <?= $logs ?> Log<?= ($logs != 1 ? 's' : '') ?>.  How many will you mill?  Each Log will yield four Wood.</p>
     <form method="post">
     <p><input name="amount" maxlength="3" size="3" /> <input type="hidden" name="mill" value="wood" /><input type="submit" value="Mill" /></p>
     </form>
<?php
}
else
{
?>
     <p>You do not have any Logs.  <i>(Logs in Storage are not accessible from the Windmill.)</i></p>
<?php
}
?>
     <h5>Flourmill</h5>
     <p>You can turn Wheat into Flour efficiently.</p>
<?php
if($wheat > 0)
{
?>
     <p>You have <?= $wheat ?> Wheat.  How many will you mill?  Every two Wheat yields three Flour (if you mill an odd number, the last Wheat will only yield one Flour).</p>
     <form method="post">
     <p><input name="amount" maxlength="3" size="3" /> <input type="hidden" name="mill" value="wheat" /><input type="submit" value="Mill" /></p>
     </form>
<?php
}
else
{
?>
     <p>You do not have any Wheat.  <i>(Wheat in Storage is not accessible from the Windmill.)</i></p>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
