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
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/blimplib.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if(!addon_exists($house, 'Airship Mooring'))
{
  header('Location: /myhouse.php');
  exit();
}

if($_POST['submit'] == 'Build')
{
  $craftname = trim($_POST['name']);
  
  if(strlen($craftname) < 2 || strlen($craftname) > 32)
    $errors[] = '<span class="failure">The craft\'s name has to be between 2 and 32 characters long.</span>';
  else
  {
    $command = 'SELECT * FROM psypets_airships WHERE ownerid=' . $user['idnum'] . ' AND name=' . quote_smart($craftname) . ' LIMIT 1';
    $existing_craft = fetch_single($command, 'fetching existing craft with this name');
    
    if($existing_craft !== false)
      $errors[] = '<span class="failure">You already have an airship with that name!  How confusing!</span>';
    else
    {
      $itemid = (int)$_POST['item'];
      $item = get_inventory_byid($itemid);
      if($item === false || $item['user'] != $user['user'])
        $errors[] = '<span class="failure">That item does not exist!  Did you even select an item?  Maybe you forgot to select an item.</span>';
      else if(new_airship($user['idnum'], $craftname, $item['itemname']))
      {
        delete_inventory_byid($itemid);

        header('Location: /myhouse/addon/airship_mooring.php');
        exit();
      }
      else
        $errors[] = '<span class="failure">The ' . $item['itemname'] . ' wouldn\'t make a very good chassis...</span>';
    }
  }
}

foreach($chassis as $item=>$bonuses)
  $allowed_items[] = quote_smart($item);

$command = 'SELECT idnum,COUNT(idnum) AS c,itemname FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' AND itemname IN (' . implode(', ', $allowed_items) . ') GROUP BY itemname';
$items = fetch_multiple($command, 'fetching allowed chassis');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Airship Mooring &gt; New Airship</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/airship_mooring.php">Airship Mooring</a> &gt; New Airship</h4>
<?php
room_display($house);

if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';

if(count($items) > 0)
{
  echo '<p>Choose an item to serve as the chassis for your new airship.  The quantity shown is the total quantity in non-protected rooms of your house, not the quantity you will use.  You will only use one of the selected item.</p>' .
       '<form method="post">' .
       '<table><tr class="titlerow"><th></th><th></th><th>Item</th><th>Qty</th><th>Weight</th><th>Space</th><th>Bonuses</th></tr>';

  $row_class = begin_row_class();

  foreach($items as $item)
  {
    $details = get_item_byname($item['itemname']);

    echo '<tr class="' . $row_class . '"><td><input type="radio" name="item" value="' . $item['idnum'] . '" /></td>' .
         '<td class="centered">' . item_display($details, '') . '</td><td>' . $item['itemname'] . '</td><td class="centered">' . $item['c'] . '</td>' .
         '<td class="centered">' . ($details['weight'] / 10) . '</td><td class="centered">' . blimp_size($details['bulk']) . '</td><td>';
    
    $bonuses = array();
    foreach($chassis[$item['itemname']] as $stat=>$bonus)
    {
      if($bonus > 0)
        $bonuses[] = $stat . ' +' . $bonus;
      else if($bonus < 0)
        $bonuses[] = $stat . ' ' . $bonus;
    }
    echo implode(', ', $bonuses) . '</td></tr>';
    
    $row_class = alt_row_class($row_class);
  }
  
  echo '</table>' .
       '<p>Airship name: <input name="name" maxlength="32" /></p>' .
       '<p><input type="submit" name="submit" value="Build" /></p></form>';
}
else
  echo '<p>None of the items in your house would make a good airship chassis (only items in non-protected rooms were considered).</p>' .
       '<p></i>(Dugout Canoes, Swan Boats, and even Couches and Sleds can be used as a chassis.  There are several other options as well, ranging from the reasonable to the ridiculous, but as a rule of thumb, almost anything that can seat a pet can probably be used as an airship chassis.)</i></p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
