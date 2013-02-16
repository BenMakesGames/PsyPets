<?php
$whereat = 'giftwrapping';
$wiki = 'Gift_Wrapping';
$url = 'giftwrapping.php';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/economylib.php';

$PRESENTS = array(
  20 => 'Tiny Blue Present',
  50 => 'Tiny White Present',
  80 => 'Small Red Present',
  110 => 'Small Blue Present',
  140 => 'Small Yellow Present',
  170 => 'Medium Orange Present',
  220 => 'Medium Red Star Present',
  260 => 'Sizable White Stripy Present',
  300 => 'Large Purple Present',
  350 => 'Large Galaxy Present',
//  400 => 'Unreasonably Large Present',
);

if($now_month == 11 || $now_month == 12 || $now_month == 1)
  $PRESENTS[220] = 'Medium Snowflake Present';

$command = 'SELECT a.itemname,a.idnum,b.bulk,b.weight,b.graphictype,b.graphic FROM monster_inventory AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname WHERE a.user=' . quote_smart($user['user']) . ' AND a.location=\'storage\' AND b.nomarket=\'no\' AND b.noexchange=\'no\' AND b.cursed=\'no\' AND a.health=b.durability ORDER BY itemname ASC';
$inventory = $database->FetchMultipleBy($command, 'idnum', 'fetching items from storage');

if($_POST['submit'] == 'Wrap')
{
  $comment = trim($_POST['message']);
  $day = (int)$_POST['day'];
  $month = (int)$_POST['month'];
  $year = (int)$_POST['year'];
  
  $open_on = mktime(0, 0, 0, $month, $day, $year);
  
  list($today_day, $today_month, $today_year) = explode(' ', date('j n Y'));
  $today = mktime(0, 0, 0, $today_month, $today_day, $today_year);
  
  if($open_on === false)
  {
    $error_messages[] = '<span class="failure">The date you specified does not exist (such as September 33rd or 58th or 91st).</span>';
  }
  else if($open_on < $today)
  {
    $error_messages[] = '<span class="failure">You specified a date that is <em>before</em> today.</span>';
    list($day, $month, $year) = explode(' ', date('j n Y'));
  }

  $warned = false;
  
  foreach($_POST as $key=>$value)
  {
    if($value == 'on' && $key{0} == 'i')
    {
      $itemid = (int)substr($key, 1);
      
      if(!array_key_exists($itemid, $inventory) && !$warned)
      {
        $warned = true;
        $error_messages[] = '<span class="failure">One (or more) of the items you selected no longer exists, is no longer in your storage, or may no longer be gift-wrapped.</span>';
      }
      
      $itemids[] = $itemid;
      $itemnames[] = $inventory[$itemid]['itemname'];
      $total_bulk += $inventory[$itemid]['bulk'];
      $total_weight += $inventory[$itemid]['weight'];
    }
  }
  
  $total_size = max($total_bulk, $total_weight);
  $box = false;

  foreach($PRESENTS as $size=>$name)
  {
    if($total_size <= $size)
    {
      $box = $name;
      break;
    }
  }

  if($box === false)
    $error_messages[] = '<span class="failure">Unfortunately, we do not have a box large enough to hold all of the items you selected.</span>';

  if(count($itemids) == 0)
    $error_messages[] = '<span class="failure">You haven\'t picked any items for me to gift wrap...</span>';

  if(count($error_messages) == 0)
  {
    $itemdata = $user['idnum'] . ';' . $open_on . ';' . implode('|', $itemnames);

    $command = 'DELETE FROM monster_inventory WHERE idnum IN (' . implode(',', $itemids) . ') LIMIT ' . count($itemids);
    $database->FetchNone($command, 'deleting items for giftbox');

    $id = add_inventory($user['user'], '', $box, $comment, $user['incomingto']);
    $command = 'UPDATE monster_inventory SET data=' . quote_smart($itemdata) . ' WHERE idnum=' . $id . ' LIMIT 1';
    $database->FetchNone($command, 'adding items to giftbox');
    
    $message = 'Alright, all done!  You\'ll find the ' . $box . ' in your ' . $user['incomingto'] . '.';

    foreach($itemids as $itemid)
      unset($inventory[$itemid]);

    $_POST = array();

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Had Items Gift-wrapped', 1);
  }
}
else
{
  list($day, $month, $year) = explode(' ', date('j n Y'));
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Florist &gt; Gift-wrapping</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Florist &gt; Gift-wrapping</h4>
     <ul class="tabbed">
      <li><a href="/florist.php">Flower Shop</a></li>
      <li><a href="/florist_anonymous.php">Flower Delivery</a></li>
      <li><a href="/florist_exchange.php">Exchanges</a></li>
      <li class="activetab"><a href="/giftwrapping.php">Gift-wrapping</a></li>
     </ul>
<?php
// VANESSA ROSELLE
echo '<a href="/npcprofile.php?npc=Vanessa+Roselle"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/flowergirl.jpg" align="right" width="350" height="706" alt="(Vanessa the Florist)" /></a>';

if(strlen($_GET['msg']) > 0)
  $error_messages[] = form_message(explode(',', $_GET['msg']));

include 'commons/dialog_open.php';

if(count($error_messages) > 0)
  echo '<p>' . implode('</p><p>', $error_messages) . '</p>';
else if($message)
  echo $message;
else
{
  echo '<p>Check off all the items you\'d like wrapped, but keep in mind that my largest gift box is 35 size and 35 weight, so I won\'t be able to wrap up items that total larger than that into one gift.</p>';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>' , $options) . '</li></ul>';
?>
     <p><i>(Damaged items, cursed items, and items which are not allowed in the basement may not be gift-wrapped, and are not listed here.)</i></p>
     <form action="<?= $url ?>" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Size / Weight</th>
       <th>Item</th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($inventory as $item)
{
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="checkbox" name="i<?= $item['idnum'] ?>"<?= $_POST['i' . $item['idnum']] == 'on' ? ' checked' : '' ?> /></td>
       <td class="centered"><?= item_display_extra($item) ?></td>
       <td class="centered"><?= ($item['bulk'] / 10) . ' / ' . ($item['weight'] / 10) ?></td>
       <td><?= $item['itemname'] ?></td>
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
     <table>
      <tr><th>Message:</th><td><input name="message" maxlength="60" size="40" value="<?= $comment ?>" /></td></tr>
      <tr><th>Open Date:</th><td>
       <select name="month">
        <option value="1"<?= $month == 1 ? ' selected' : '' ?>>January</option>
        <option value="2"<?= $month == 2 ? ' selected' : '' ?>>February</option>
        <option value="3"<?= $month == 3 ? ' selected' : '' ?>>March</option>
        <option value="4"<?= $month == 4 ? ' selected' : '' ?>>April</option>
        <option value="5"<?= $month == 5 ? ' selected' : '' ?>>May</option>
        <option value="6"<?= $month == 6 ? ' selected' : '' ?>>June</option>
        <option value="7"<?= $month == 7 ? ' selected' : '' ?>>July</option>
        <option value="8"<?= $month == 8 ? ' selected' : '' ?>>August</option>
        <option value="9"<?= $month == 9 ? ' selected' : '' ?>>September</option>
        <option value="10"<?= $month == 10 ? ' selected' : '' ?>>October</option>
        <option value="11"<?= $month == 11 ? ' selected' : '' ?>>November</option>
        <option value="12"<?= $month == 12 ? ' selected' : '' ?>>December</option>
       </select>
       <input name="day" maxlength="2" size="2" value="<?= $day ?>" />, <input name="year" maxlength="4" size="4" value="<?= $year ?>" />
      </td></tr>
     </table>
     <p><input type="submit" name="submit" value="Wrap" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
