<?php
require_once 'commons/init.php';

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
  'Argrum Steel' => array('Copper', 'Silver', 'Iron', 'Coal'),
  'Brass' => array('Copper', 'Zinc'),
  'Bronze' => array('Copper', 'Tin'),
  'Corinthian Bronze' => array('Copper', 'Gold', 'Silver'),
  'Electrum' => array('Gold', 'Silver'),
  'Orichalcum' => array('Copper', 'Gold'),
  'Panchaloha' => array('Gold', 'Silver', 'Copper', 'Zinc', 'Iron'),
);

$items = array(
/*  'Argrum Steel', 'Brass', 'Bronze', 'Corinthian Bronze', 'Electrum', 'Orichalcum', 'Panchaloha',*/
  'Copper', 'Tin', 'Iron', 'Gold', 'Silver', 'Zinc', 'Coal',
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

if($_POST['submit'] == 'Forge')
{
  $amount = (int)$_POST['quantity'];
  $alloy = $_POST['alloy'];

  if(!array_key_exists($alloy, $alloys))
    $message = '<p class="failure">It doesn\'t look like you selected an alloy...</p>'; 
  else if($amount <= 0)
    $message = '<p class="failure">' . $amount . '?  That doesn\'t make much sense...</p>';
  else
  {
    $needed = $alloys[$alloy];

    $has_items = true;

    foreach($needed as $itemname)
    {
      if($inventory[$itemname]['count'] < $amount)
        $has_items = false;
    }

    if(!$has_items)
      $message = '<p class="failure">You do not have the metals necessary to forge ' . $amount . ' ' . $alloy . 's.</p>';
    else
    {
      foreach($needed as $itemname)
      {
        delete_inventory_fromhome($user['user'], $itemname, $amount);

        if($amount == $inventory[$itemname]['count'])
          unset($inventory[$itemname]);
        else
          $inventory[$itemname]['count'] -= $amount;
      }


      for($i = 0; $i < $amount; ++$i)
        add_inventory($user['user'], 'u:' . $user['idnum'], $alloy, 'Forged at ' . $user['display'] . "'s Forge", 'home');

      $message = '<p class="success">' . $amount . ' metal' . ($amount == 1 ? ' was' : 's were') . ' forged!</p>';
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
      <li><a href="/myhouse/addon/forge.php">Melt</a></li>
      <li class="activetab"><a href="/myhouse/addon/forge2.php">Forge</a></li>
     </ul>
     <p>You can forge alloys from base metals.</p>
<?php
$rowclass = begin_row_class();

echo '<p><i>(The quantity listed is the quantity available in your house, and not the quantity required.  You will get one set of the Metals listed per one Alloy melted.)</i></p>' .
     '<form method="post"><table><tr class="titlerow"><th></th><th></th><th>Alloy to Forge</th><th>Metals Needed</th></tr>';

foreach($alloys as $alloy=>$metals)
{
  $details = get_item_byname($alloy);

  $disabled = false;

  foreach($metals as $metal)
  {
    if((int)$inventory[$metal]['count'] < 1)
    {
      $disabled = true;
      break;
    }
  }

  echo '<tr class="' . $rowclass . '"><td><input type="radio" name="alloy" value="' . $alloy . '"' . ($disabled ? ' disabled' : '') . ' /></td>' .
       '<td class="centered">' . item_display($details, '') . '</td><td>' . $alloy . '</td>' .
       '<td' . ($disabled ? ' class="failure"' : '') . '>';

  foreach($metals as $metal)
    echo $metal . ' (' . (int)$inventory[$metal]['count'] . ' / 1)<br />';

  echo '</td></tr>';

  $rowclass = alt_row_class($rowclass);
}
  
echo '</table><p>Quantity: <input name="quantity" maxlength="3" size="3" value="1" /> <input type="submit" name="submit" value="Forge" /></p></form>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
