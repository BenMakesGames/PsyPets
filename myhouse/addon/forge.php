<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Forge';
$THIS_ROOM = 'Forge';

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

if(!addon_exists($house, 'Forge'))
{
  header('Location: /myhouse.php');
  exit();
}

$alloys = array(
  'Argrum Steel' => array('Copper', 'Silver', 'Iron'),
  'Brass' => array('Copper', 'Zinc'),
  'Bronze' => array('Copper', 'Tin'),
  'Corinthian Bronze' => array('Copper', 'Gold', 'Silver'),
  'Electrum' => array('Gold', 'Silver'),
  'Orichalcum' => array('Copper', 'Gold'),
  'Panchaloha' => array('Gold', 'Silver', 'Copper', 'Zinc', 'Iron'),
);

$items = array(
  'Argrum Steel', 'Brass', 'Bronze', 'Corinthian Bronze', 'Electrum', 'Orichalcum', 'Panchaloha',
/*  'Copper', 'Tin', 'Iron', 'Gold', 'Silver', 'Zinc', 'Coal'*/
);

$inventory = $database->FetchMultipleBy('
	SELECT COUNT(idnum) AS count,itemname
	FROM monster_inventory
	WHERE
		user=' . $database->Quote($user['user']) . '
		AND location LIKE \'home%\'
		AND location NOT LIKE \'home/$%\'
		AND itemname ' . $database->In($items) . '
	GROUP BY itemname
', 'itemname');

if($_POST['submit'] == 'Melt')
{
  $amount = (int)$_POST['quantity'];
  $alloy = $_POST['alloy'];

  if(!array_key_exists($alloy, $alloys))
    $message = '<p class="failure">It doesn\'t look like you selected an alloy...</p>'; 
  else if($amount <= 0)
    $message = '<p class="failure">' . $amount . '?  That doesn\'t make much sense...</p>';
  else if($amount > $inventory[$alloy]['count'])
    $message = '<p class="failure">You do not have ' . $amount . ' ' . $alloy . ' available to melt...</p>';
  else
  {
    $used = delete_inventory_fromhome($user['user'], $alloy, $amount);

    if($used == 0)
      $message = '<p class="failure">Could not use up any of the ' . $alloy . '...</p>';
    else
    {
      if($used == $inventory[$alloy]['count'])
        unset($inventory[$alloy]);
      else
        $inventory[$alloy]['count'] -= $used;

      foreach($alloys[$alloy] as $metal)
      {
        for($i = 0; $i < $used; ++$i)
          add_inventory($user['user'], 'u:' . $user['idnum'], $metal, 'Melted at ' . $user['display'] . "'s Forge", 'home');
      }

      $message = '<p class="success">' . ($used * count($alloys[$alloy])) . ' metals were recovered from melting.</p>';
    }
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Forge</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Forge</h4>
<?php
echo $message;
room_display($house);
?>
     <ul class="tabbed">
      <li class="activetab"><a href="/myhouse/addon/forge.php">Melt</a></li>
      <li><a href="/myhouse/addon/forge2.php">Forge</a></li>
     </ul>
     <p>You can melt alloys down into their constituent metals.</p>
<?php
$count = 0;

foreach($inventory as $item)
{
  if(array_key_exists($item['itemname'], $alloys))
  {
    $details = get_item_byname($item['itemname']);
    
    if($count == 0)
    {
      $rowclass = begin_row_class();
      echo '<p><i>(The quantity listed is the quantity available in your house, and not the quantity required.  You will get one set of the Metals listed per one Alloy melted.)</i></p>' .
           '<form method="post"><table><tr class="titlerow"><th></th><th></th><th>Alloy</th><th>Qty</th><th></th><th>Metals</th></tr>';
    }

    echo '<tr class="' . $rowclass . '"><td><input type="radio" name="alloy" value="' . $item['itemname'] . '" /></td>' .
         '<td class="centered">' . item_display($details, '') . '</td><td>' . $item['itemname'] . '</td><td class="centered">' . $item['count'] . '</td>' .
         '<td><img src="/gfx/lookright.gif" alt="" /></td>' .
         '<td>' . implode('<br />', $alloys[$item['itemname']]) . '</td></tr>';

    $rowclass = alt_row_class($rowclass);
    $count++;
  }
}
  
if($count == 0)
  echo '<p>You do not have any alloys in non-protected rooms of your house.</p>';
else
{
  echo '</table><p>Quantity: <input name="quantity" maxlength="3" size="3" value="1" /> <input type="submit" name="submit" value="Melt" /></p></form>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
