<?php
$whereat = 'home';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/love.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

if($now_month < 5 || $now_month > 8 || $now < $user['tot_time'])
{
  header('Location: /');
  exit();
}

$house = get_house_byuser($user['idnum']);

if($house === false)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$message = '<p>Howdy, ' . $user['display'] . '!  What can I get for ya\'?</p>';

$items_for_sale = array(
  1 => 'Artificial Grape "Will-o\'-the-Wisp" Popsicle',
  2 => 'Blueberry "Sakaki" Popsicle',
  3 => 'Rainbow "Splat" Popsicle',
  4 => 'Redsberry "Walking Fish" Popsicle',
  5 => 'Sour Lime "Onion" Popsicle',
  6 => 'Tropical "Fish" Popsicle',
);

if($_GET['action'] == 'goaway')
{
  $user['tot_time'] = $now + mt_rand(4 * 24 * 60 * 60, 8 * 24 * 60 * 60);

  $command = 'UPDATE monster_users SET tot_time=' . $user['tot_time'] . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'ice cream truck!');

  $message = '<p>Nothing this time, eh?</p><p>Oh well.  See you next time!</p>';

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Told the Ice Cream Truck to Go Away', 1);
}

if($_POST['action'] == 'buy')
{
  $option_id = (int)$_POST['popsicle'];

  if(array_key_exists($option_id, $items_for_sale))
  {
    $itemname = $items_for_sale[(int)$_POST['popsicle']];

    $details = get_item_byname($itemname);

    $price = floor($details['value'] * 2.5);

    if($user['money'] >= $price)
    {
      $user['tot_time'] = $now + mt_rand(4 * 24 * 60 * 60, 8 * 24 * 60 * 60);

      $command = 'UPDATE monster_users SET tot_time=' . $user['tot_time'] . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'ice cream truck!');

      take_money($user, $price, 'Icecream Truck purchase', $itemname);
      $user['money'] -= $price;

      add_inventory($user['user'], '', $itemname, 'Bought from The Icecream Truck', 'storage/incoming');
      flag_new_incoming_items($user['user']);

      $message = '
        <p>One ' . $itemname . ', coming right up!</p>
        <p><i>(Find it in <a href="incoming.php">Incoming</a>.)</i></p>
        <p>Enjoy!</p>
        <p>I\'ll see you around.</p>
      ';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Purchases From the Ice Cream Truck', 1);
    }
    else
      $message = '<p>It looks like you\'re a little short.  The ' . $itemname . ' costs ' . $price . $MONEY . '.</p>';
  }
  else
    $message = '<p>Hm... I don\'t sell anything like that.  Maybe you\'re thinking of a more-different icecream truck?</p>';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Icecream Truck</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
<h4>The Icecream Truck</h4>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/icecreamtruck.png" align="right" width="300" height="200" alt="(The Ice Cream Truck)" />';
include 'commons/dialog_open.php';
echo $message;
include 'commons/dialog_close.php';

if($now_month >= 5 && $now_month <= 8 && $now >= $user['tot_time'])
{
  echo '<ul><li><a href="icecreamtruck.php?action=goaway">Explain that you do not want anything.</a></li></ul>';

  echo '<table>';
  
  $rowclass = begin_row_class();
  
  foreach($items_for_sale as $id=>$itemname)
  {
    $details = get_item_byname($itemname);
    $price = floor($details['value'] * 2.5);

    $disabled = ($user['money'] < $price ? ' disabled="disabled"' : '');

    echo '
      <tr class="' . $rowclass . '">
       <td class="centered">' . item_display_extra($details) . '</td>
       <td>' . $itemname . '</td>
       <td><form action="icecreamtruck.php" method="post"><input type="hidden" name="action" value="buy" /><input type="hidden" name="popsicle" value="' . $id . '" /><input type="submit" value="Buy (' . $price . 'm)"' . $disabled . ' /></form></td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
  }
  
  echo '</table>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
