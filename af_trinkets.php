<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/questlib.php';
require_once 'commons/favorlib.php';
require_once 'commons/messages.php';

$favor_cost = 50;

// any-time items
$give_away = array(
  1 => array('Hourglass VIII', 'Immediately gives you 8 House Hours to run.'),
  2 => array('Potion Ticket', 'Trade for potions at the Potion Shop.'),
  3 => array('Loaf', 'Use on a spayed or neutered pet to un-fix it.'),
);

if($user['favor'] >= $favor_cost && $_POST['action'] == 'getitem')
{
  $index = $_POST['item'];
  
  $itemname = false;
  
  if(array_key_exists($index, $give_away))
    $itemname = $give_away[$index][0];

  if($itemname !== false)
  {
    $id = add_inventory($user['user'], '', $itemname, 'Purchased from the Rare Trinket Shop', 'storage/incoming');

    flag_new_incoming_items($user['user']);

    $record = 'bought item - ' . $itemname;

    spend_favor($user, $favor_cost, 'bought item - ' . $itemname, $id);

    require_once 'commons/dailyreportlib.php';
    record_daily_report_stat('Rare Trinkets: Someone Bought a ' . $itemname, 1);
    
    header('Location: ./af_trinkets.php?msg=144:' . $itemname);
    exit();
  }
}

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Alchemist's &gt; Rare Trinkets</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/alchemist.php">The Alchemist's</a> &gt; Rare Trinkets</h4>
     <ul class="tabbed">
      <li><a href="/alchemist.php">General Shop</a></li>
      <li><a href="/alchemist_potions.php">Potion Shop</a></li>
      <li class="activetab"><a href="/af_trinkets.php">Rare Trinkets</a></li>
      <li><a href="/alchemist_pool.php">Cursed Pool</a></li>
      <li><a href="/alchemist_transmute.php">Pet Transmutations</a></li>
     </ul>
<a href="npcprofile.php?npc=Thaddeus"><img src="//saffron.psypets.net/gfx/npcs/thaddeus.png" align="right" width="350" height="250" alt="(Thaddeus the Alchemist)" /></a>
<?php
include 'commons/dialog_open.php';

if($error_message)
  echo '<p>' . $error_message . '</p>';
else if($message)
  echo '<p>' . $message . '</p>';
else
{
  echo '
    <p>Hello, ', $user['display'], '.  I have a handful of unusual and rare trinkets here which you may be interested in - things I\'ve picked up during my travels.</p>
    <p>Ah, but that was long ago.  Now that I\'ve settled down, I don\'t really have much use for these things...</p>
    <p>Hm!  Well!  But you\'re not here to hear about all that, are you!</p>
    <p>My inventory is rather limited at the moment, I\'m afraid to say, but take a look around and let me know if anything strikes your fancy.</p>
    <p>The items here are <strong>', $favor_cost, ' Favor</strong> each.</p>
  ';

  echo $dialog_extra;
}

$options[] = '<a href="/buyfavors.php">Support PsyPets; get Favor</a>';

include 'commons/dialog_close.php';
?>
     <p>You currently have <?= $user['favor'] ?> Favor.  Each item here costs <?= $favor_cost ?> Favor.</p>
<?php
if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
     <form method="post">
     <table>
      <thead>
       <tr><th></th><th></th><th>Item</th><th>Description</th></tr>
      </thead>
      <tbody>
<?php
$rowclass = begin_row_class();

foreach($give_away as $index=>$item)
{
  $details = get_item_byname($item[0]);
?>
       <tr class="<?= $rowclass ?>">
        <td><input type="radio" name="item" value="<?= $index ?>" /></td>
        <td class="centered"><?= item_display($details) ?></td>
        <td><?= $item[0] ?></td>
        <td><?= $item[1] ?></td>
       </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
      </tbody>
     </table>
     <p><?php
if($user['favor'] >= $favor_cost)
  echo '<input type="hidden" name="action" value="getitem" /><input type="submit" value="This, Please (50 Favor)" style="width:150px;" />';
else
  echo '<input type="hidden" name="action" value="getitem" /><input type="submit" value="This, Please (50 Favor)" style="width:150px;" disabled />';
?></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
