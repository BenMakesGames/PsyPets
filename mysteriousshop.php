<?php
$wiki = 'Mysterious Shop';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';
require_once 'commons/formatting.php';
require_once 'commons/questlib.php';
require_once 'commons/timelib.php';

$cave_quest = get_quest_value($user['idnum'], 'hidden cave quest');

if($cave_quest['value'] < 2)
{
  header('Location: ./myhouse.php');
  exit();
}

$badges = get_badges_byuserid($user['idnum']);

$options = array();

if($now_month == 5 && $now_day == 4)
  $message = 'May the 4th be with you!';
if($now_month == 11 && $now_day == 5)
  $message = 'Remember, remember...';
else if($badges['demolition'] == 'yes')
  $message = 'Buy somethin\', will ya!';
else
  $message = 'You\'re going to take responsibility for breaking the wall of my shop, right?';

$items_offered = array(
  array('Blank Map', 10),
  array('Lychee', 15),
  array('Hamster Plushy', 50),
  array('Map Room Blueprint', 80),
  array('Hungry Sword (level 0)', 120),
);

if($now_month == 4 && $now_day == 23)       // April 23rd
  $items_offered[] = array('Ascalon', 100);

if($now_month == 11 && $now_day == 5)
  $items_offered[] = array('Guy Fawkes Mask', 200);
  
if($now_month == 5 && $now_day == 4)
  $items_offered[] = array('Model Moon', 200);

else if($now_month == 6 && $now_day == 21)  // June 21st
  $items_offered[] = array('June\'s Scepter', 100);

else if($now_month == 8)  // August
{
  if($now_day == 16)      //   16th
    $items_offered[] = array('Xicolatada', 60);
  else if($now_day == 20) //   20th
    $items_offered[] = array('Virtual Sword', 100);
}

else if($now_month == 10) // October
{
  if($now_day == calculate_holiday(10, 1, 2)) // second Monday
    $items_offered[] = array('"India"', 80);
}

else if($now_month == 11 && $now_day == 4)  // November 4th
  $items_offered[] = array('Waiting For The Barbarians', 90);

else if($now_month == 12 && $now_day == 5)  // December 5th
  $items_offered[] = array('Super-Secret Ninja Technique Scroll', 80);

if(date('D j') == 'Fri 13')
  $items_offered[] = array('Hockey Mask', 20);

if($user['show_mysteriousshop'] == 'no')
{
  $command = 'UPDATE monster_users SET show_mysteriousshop=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'revealing mysterious shop');
  
  $extra_message = '<p><i>(The Mysterious Shop has been revealed to you!  You can find it in the Services menu.)</i></p>';
}

if($_POST['submit'] == 'Buy')
{
  if($_POST['item'] == 'repair' && $badges['demolition'] == 'no' && $user['rupees'] >= 30)
  {
    $command = 'UPDATE monster_users SET rupees=rupees-30 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating rupee count');
    
    $user['rupees'] -= 30;

    $badges['demolition'] = 'yes';
    set_badge($user['idnum'], 'demolition');
    $message = 'Thanks!</p><p><i>(You received the Demolitions Expert badge!)</i>';
  }
  else if(array_key_exists($_POST['item'], $items_offered))
  {
    $purchase = $items_offered[$_POST['item']];
    if($user['rupees'] >= $purchase[1])
    {
      $command = 'UPDATE monster_users SET rupees=rupees-' . $purchase[1] . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'updating rupee count');

      $user['rupees'] -= $purchase[1];

      add_inventory($user['user'], '', $purchase[0], 'Purchased from the Mysterious Shop', $user['incomingto']);

      $message = 'Thanks!  You\'ll find the item in ' . link_to_room($user['incomingto']) . '.';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Bought Something at the Mysterious Shop', 1);
    }
    else
      $message = 'Thief!';
  }
}

include 'commons/html.php';
?>
 <head data-mysterious-number="+0<?= amelia_earhart_number($user) ?>">
  <title><?= $SETTINGS['site_name'] ?> &gt; Mysterious Shop</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>Mysterious Shop</h4>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/mysteriousshop.png" align="right" width="350" height="120" alt="(Mysterious Shop owner)" />';

include 'commons/dialog_open.php';
echo '<p>' . $message . '</p>';
include 'commons/dialog_close.php';
echo $extra_message;

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

// Three Wise Men Day
if($now_month == 1 && $now_day == 6)
  echo '
    <ul class="tabbed">
     <li class="activetab"><a href="mysteriousshop.php">Shop</a></li>
     <li><a href="mysteriousshop_special.php">Special Offers <i style="color:red;">Hm!</i></a></li>
    </ul>
  ';
?>
<p><i>(The Mysterious Shop only accepts Rupees; not Moneys.)</i></p>
<form action="mysteriousshop.php" method="post">
<table>
 <tr class="titlerow">
  <th></th><th></th><th>Item</th><th>Cost</th>
 </tr>
<?php
$row_class = begin_row_class();

if($badges['demolition'] == 'no')
{
?>
 <tr class="<?= $row_class ?>">
  <td><input type="radio" name="item" value="repair" /></td>
  <td class="centered"> </td>
  <td>Pay for damages</td>
  <td class="centered">30</td>
 </tr>
<?php
  $row_class = alt_row_class($row_class);
}

foreach($items_offered as $id=>$item)
{
  $details = get_item_byname($item[0]);

  if($user['rupees'] >= $item[1])
  {
?>
 <tr class="<?= $row_class ?>">
  <td><input type="radio" name="item" value="<?= $id ?>" /></td>
  <td class="centered"><?= item_display($details, '') ?></td>
  <td><?= $item[0] ?></td>
  <td class="centered"><?= $item[1] ?></td>
 </tr>
<?php
  }
  else
  {
?>
 <tr class="<?= $row_class ?>">
  <td><input type="radio" name="item" value="0" disabled /></td>
  <td class="centered"><?= item_display($details, '') ?></td>
  <td><?= $item[0] ?></td>
  <td class="failure centered"><?= $item[1] ?></td>
 </tr>
<?php
  }

  $row_class = alt_row_class($row_class);
}
?>
</table>
<p><input type="submit" name="submit" value="Buy" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
