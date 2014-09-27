<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';
require_once 'commons/formatting.php';
require_once 'commons/questlib.php';

if(date('M d') != 'Mar 17' && $user['user'] != $SETTINGS['author_login_name'])
{
  header('Location: ./cityhall.php');
  exit();
}

$options = array();

$where = $_GET['where'];

if($where == 'totem')
{
  $other_tabs = '
    <li><a href="totemgarden.php">Information</a></li>
    <li><a href="totemgardenview.php">Browse Garden</a></li>
    <li><a href="mahjong.php">Mahjong Exchange</a></li>
  ';
  $npc_name = 'Matalie';
  $npc_graphic = '<a href="/npcprofile.php?npc=Matalie Mansur"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/totemgirl.jpg" align="right" width="350" height="501" alt="(Totem Pole aficionado Matalie)" /></a>';
  $title = 'Totem Pole Garden';
  $options[] = '<a href="/stpatricks.php?where=bank">Check on Lakisha</a>';
  $not_where = 'bank';
}
else if($where == 'bank')
{
  $other_tabs = '
    <li><a href="bank.php">The Bank</a></li>
    <li><a href="bank_groupcurrencies.php">Group Currencies</a></li>
    <li><a href="bank_exchange.php">Exchanges</a></li>
    <li><a href="ltc.php">License to Commerce</a></li>
    <li><a href="allowance.php">Allowance Preference</a></li>
    <li><a href="af_favortickets.php">Get Favor Tickets</a></li>
    <li><nobr><a href="af_favortransfer2.php">Transfer Favor</a></nobr></li>
  ';
  $npc_name = 'Lakisha';
  $npc_graphic = '<a href="/npcprofile.php?npc=Lakisha Pawlak"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/banker_lakisha.png" align="right" width="350" height="427" alt="(Lakisha the Banker)" /></a>';
  $title = 'Bank';
  $options[] = '<a href="/stpatricks.php?where=totem">Check on Matalie</a>';
  $not_where = 'totem';
}
else
{
  header('Location: /cityhall.php');
  exit();
}

$bank_quest_value_name = 'stpat bank ' . date('Y');

$quest_data['bank'] = get_quest_value($user['idnum'], $bank_quest_value_name);
if($quest_data['bank'] === false)
{
  add_quest_value($user['idnum'], $bank_quest_value_name, 0);
  $quest_data['bank'] = get_quest_value($user['idnum'], $bank_quest_value_name);

  $items_given['bank'] = 0;
  $_POST = array();
}
else
  $items_given['bank'] = $quest_data['bank']['value'];

$totem_quest_value_name = 'stpat totem ' . date('Y');

$quest_data['totem'] = get_quest_value($user['idnum'], $totem_quest_value_name);
if($quest_data['totem'] === false)
{
  add_quest_value($user['idnum'], $totem_quest_value_name, 0);
  $quest_data['totem'] = get_quest_value($user['idnum'], $totem_quest_value_name);

  $items_given['totem'] = 0;
  $_POST = array();
}
else
  $items_given['totem'] = $quest_data['totem']['value'];

if($_GET['dialog'] == 'rules')
{
  if($where == 'totem')
  {
    $dialog = '<p>Oh, of course!</p><p>Lakisha and I are each trying to collect as many items with the word "Green" in their name before St. Patrick\'s Day is over.  Anything from Green Cloth to a Wintergreen Diary is acceptable, so long as "green" appears in its name at all.</p>' .
              '<p>The loser has to clean the other\'s apartment, and Lakisha has a very big apartment - it\'d probably take me all day!</p>' .
              '<p>We decided that asking other people for items wouldn\'t be against the rules... so... I\'d really appreciate your help, if you have any extra items lying around!</p>';
  }
  else
  {
    $dialog = '<p>The rules are simple: at the end of St. Patrick\'s Day, whoever has the most items with the word "Green" in their name gets their apartment cleaned by the other.  Green Lolipops, Greenhouse Blueprints... you get the idea.</p>' .
              '<p>Matalie\'s apartment is a complete mess, and apparently it would be cheating for the loser to hire cleaners...</p>' .
              '<p>Well, fortunately the rules don\'t say anything about how we get the items, so if you could spare any - any at all - I\'d be very grateful!</p>';  
  }
}
else
{
  if($where == 'totem')
  {
    if($items_given['bank'] > 0)
      $dialog = '<p>So you\'ve decided to team up with Lakisha, hm?  You better not be here just to spy on me!</p>';
    else
      $dialog = '<p>You\'re here to help me beat Lakisha in our St. Patrick\'s Day competition, right?  I\'ll certainly need all the help I can.  She doesn\'t know the meaning of the word "restraint!"</p>';
  }
  else
  {
    if($items_given['totem'] > 0)
      $dialog = '<p>I hear you joined Matalie\'s side.  Hohohoho!  She\'ll need all the help she can get!</p>';
    else
      $dialog = '<p>Wonderful, wonderful:  help has arrived!  I won\'t hear the end of it from Matalie if she beats me at our little St. Patrick\'s Day competition.  That girl\'s ruthless!</p>';
  }

  $options[] = '<a href="stpatricks.php?where=' . $where . '&dialog=rules">Ask her to explain the rules of the competition again</a>';
}

if($_POST['submit'] == 'Give' && $items_given[$not_where] == 0)
{
  $itemids = array();

  foreach($_POST as $key=>$value)
  {
    if($key{0} == 'i' && ($value == 'on' || $value == 'yes'))
    {
      $itemid = (int)substr($key, 1);
      $inventory = get_inventory_byid($itemid);

      if($inventory['user'] == $user['user'] && $inventory['location'] == 'storage' && stripos($inventory['itemname'], 'green') !== false)
        $itemids[] = $itemid;
    }
  }
  
  if(count($itemids) > 0)
  {
    if($where == 'bank')
      $dialog = '<p>Thank you!  I won\'t be beaten by Matalie!</p>';
    else
      $dialog = '<p>Awesome, thanks!  Lakisha doesn\'t stand a chance!</p>';

    $items_given[$where] += count($itemids);
    $command = 'UPDATE psypets_stpatricks SET items=items+' . count($itemids) . ' WHERE npc=' . quote_smart($where) . ' AND year=' . date('Y') . ' LIMIT 1';
    $database->FetchNone($command, 'updating NPC St. Patrick\'s Day item count');

    update_quest_value($quest_data[$where]['idnum'], $items_given[$where]);

    $command = 'DELETE FROM monster_inventory WHERE idnum IN (' . implode(',', $itemids) . ') LIMIT ' . count($itemids);
    $database->FetchNone($command, 'deleting donated green items');

    $badges = get_badges_byuserid($user['idnum']);
    if($badges['stpatricks'] == 'no' && $items_given[$where] >= 1)
    {
      set_badge($user['idnum'], 'stpatricks');
      $dialog .= '<p><i>(You received the 3-Leaf Clover Badge!)</i></p>';
    }
    if($badges['stpatricks2'] == 'no' && $items_given[$where] >= 20)
    {
      set_badge($user['idnum'], 'stpatricks2');
      $dialog .= '<p><i>(You received the 4-Leaf Clover Badge!)</i></p>';
    }
    if($badges['stpatricks3'] == 'no' && $items_given[$where] >= 50)
    {
      set_badge($user['idnum'], 'stpatricks3');
      $dialog .= '<p><i>(You received the 5-Leaf Clover Badge!)</i></p>';
    }
  }
  else
    $dialog = '<p>You didn\'t select any items...</p>';
}

$command = 'SELECT items FROM psypets_stpatricks WHERE npc=' . quote_smart($where) . ' AND year=' . date('Y') . ' LIMIT 1';
$data = $database->FetchSingle($command, 'fetching item count for NPC');

if($data === false)
{
  $command = 'INSERT INTO psypets_stpatricks (npc, year) VALUES (' . quote_smart($where) . ', ' . date('Y') . ')';
  $database->FetchNone($command, 'initializing NPC St. Patrick\'s Day item count');
  
  $item_count = 0;
}
else
  $item_count = $data['items'];

if(!array_key_exists('dialog', $_GET))
{
  $dialog .= '<p>So far I\'ve collected ' . $item_count . ' item' . ($item_count != 1 ? 's' : '');

  if($items_given[$where] > 0)
    $dialog .= ', ' . $items_given[$where] . ' of which ' . ($items_given[$where] == 1 ? 'was' : 'were') . ' from you';

  $dialog .= '.</p>';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $title ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The <?= $title ?></h4>
     <ul class="tabbed">
<?= $other_tabs ?>
      <li class="activetab stpatrick"><nobr><a href="stpatricks.php?where=<?= $where ?>">St. Patrick's Day Competition</a></nobr></li>
     </ul>
<?php
echo $npc_graphic;

include 'commons/dialog_open.php';

echo $dialog;

include 'commons/dialog_close.php';

echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($items_given[$not_where] == 0)
{
?>
<h4>Donate</h4>
<p>You can give <?= $npc_name ?> any items from your Storage with "green" in the name.  (Standard-availability items only: not custom items, cross-game items, etc.)</p>
<p><i>(Remember: to select a range of items, select the first, then hold SHIFT while selecting the last.)</i></p>
<?php
  $command = 'SELECT a.idnum,a.itemname,b.graphictype,b.graphic FROM monster_inventory AS a LEFT JOIN monster_items AS b on a.itemname=b.itemname WHERE a.user=' . quote_smart($user['user']) . ' AND a.location=\'storage\' AND a.itemname LIKE \'%green%\' ORDER BY a.itemname ASC';
  $inventory = $database->FetchMultiple($command, 'fetching "green" items from storage');

  if(count($inventory) == 0)
    echo '<p>You do not have any such items in Storage at this time.</p>';
  else
  {
?>
<form action="stpatricks.php?where=<?= $where ?>" method="post">
<table>
 <tr class="titlerow"><th></th><th></th><th>Item</th></tr>
<?php
    $rowclass = begin_row_class();

    foreach($inventory as $item)
    {
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="checkbox" name="i<?= $item['idnum'] ?>" /></td>
  <td class="centered"><?= item_display($item, '') ?></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
</table>
<p><input type="submit" name="submit" value="Give" onclick="return confirm('Really give her the selected items?  Just to be clear: you won\'t ever get them back!');" /></p>
</form>
<?php
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
