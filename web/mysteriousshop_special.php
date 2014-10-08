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
  header('Location: /myhouse.php');
  exit();
}

$offers = array();

if($now_month == 1 && $now_day == 6)
{
  $offers = array(
    1 => 'Balthasar',
    2 => 'Melchior',
    3 => 'Gasper',
  );
}
else
{
  header('Location: /mysteriousshop.php');
  exit();
}

$message = 'Here today, gone tomorrow!';

if($_POST['action'] == 'Buy (200 Favor)' && $user['favor'] >= 200)
{
  $offerid = $_POST['item'];
  
  if(array_key_exists($offerid, $offers))
  {
    require_once 'commons/favorlib.php';
  
    $itemname = $offers[$offerid];
    
    $itemid = add_inventory($user['user'], '', $itemname, 'Purchased from the Mysterious Shop', 'storage/incoming');
    spend_favor($user, 200, 'Mysterious Shop - ' . $itemname, $itemid);
    
    $message .= '</p><p><i>(You\'ve received ' . $itemname . '!  You\'ll find it in <a href="incoming.php">Incoming</a>.)</i>';
  }
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Mysterious Shop &gt; Special Offers</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>Mysterious Shop &gt; Special Offers</h4>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/mysteriousshop.png" align="right" width="350" height="120" alt="(Mysterious Shop owner)" />';

include 'commons/dialog_open.php';
echo '<p>' . $message . '</p>';
include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

echo '
  <ul class="tabbed">
   <li><a href="mysteriousshop.php">Shop</a></li>
   <li class="activetab"><a href="mysteriousshop_special.php">Special Offers <i style="color:red;">Hm!</i></a></li>
  </ul>
';
?>
<p><i>(<strong>These strange offers cost 200 Favor each!</strong>  You currently have <?= $user['favor'] ?> Favor.)</i></p>
<ul><li><a href="/buyfavors.php">Get more Favor</a></li></ul>
<form action="mysteriousshop_special.php" method="post">
<table>
 <thead>
  <tr class="titlerow">
   <th></th><th></th><th>Item</th>
  </tr>
 </thead>
 <tbody>
<?php
$rowclass = begin_row_class();

foreach($offers as $id=>$itemname)
{
  $details = get_item_byname($itemname);

  echo '
    <tr class="' . $rowclass . '">
     <td><input type="radio" name="item" value="' . $id . '" /></td>
     <td>' . item_display($details) . '</td>
     <td>' . $itemname . '</td>
    </tr>
  ';
  
  $rowclass = alt_row_class($rowclass);
}
?>
 </tbody>
</table>
<?php
if($user['favor'] >= 200)
  echo '<p><input type="submit" name="action" value="Buy (200 Favor)" class="bigbutton" /></p>';
else
  echo '<p><input type="submit" value="Buy (200 Favor)" disabled="disabled" class="bigbutton" /></p>';
?>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
