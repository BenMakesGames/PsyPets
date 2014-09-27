<?php
$wiki = 'My_Favor_Store';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/marketlib.php';

if($user['license'] == 'no')
{
  header('Location: ./storage.php');
  exit();
}

$command = 'SELECT itemid FROM psypets_custom_item_store WHERE ownerid=' . $user['idnum'];
$for_sale = $database->FetchMultipleBy($command, 'itemid', 'fetching customs for sale');

$command = 'SELECT favor FROM psypets_favor_history WHERE userid=' . $user['idnum'] . ' AND timestamp>1272690000 ORDER BY timestamp DESC';
$favors = $database->FetchMultiple($command, 'fetching favors');

$customs = array();

if(count($favors) > 0)
{
  foreach($favors as $favor)
  {
    if(substr($favor['favor'], 0, 15) == 'custom item - "')
      $customs[] = substr($favor['favor'], 15, strlen($favor['favor']) - 16);
    else if(substr($favor['favor'], 0, 22) == 'custom avatar item - "')
      $customs[] = substr($favor['favor'], 22, strlen($favor['favor']) - 23);
  }
}

foreach($customs as $custom)
{
  $details = get_item_byname($custom);

  if(!array_key_exists($details['idnum'], $for_sale))
    $available[] = $details;
}

if($_POST['action'] == 'List Items for Sale')
{
  foreach($available as $avail)
  {
    $value = (int)$_POST[itemname_to_form_value($avail['itemname'])];
  
    if($value >= 300 && $value <= 999)
    {
      $message_list[] = 'List ' . $avail['itemname'] . ' (item id ' . $avail['idnum'] . ') for ' . $value . ' Favor';
      
      $markup = $value - 300;
      
      $command = '
        INSERT INTO psypets_custom_item_store
        (ownerid, itemid, markup)
        VALUES
        (' . $user['idnum'] . ', ' . $avail['idnum'] . ', ' . $markup . ')
      ';
      $database->FetchNone($command, 'listing custom items for sale');
      
      header('Location: ./myfavorstore.php');
      exit();
    }
  }
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; <?= $user['display'] ?>'s Custom Item Store &gt; Add an Item</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';
?>
     <h4><a href="myfavorstore.php"><?= $user['display'] ?>'s Custom Item Store</a> &gt; Add an Item</h4>
     <ul class="tabbed">
      <li><a href="storage.php">Storage</a></li>
      <li><a href="incoming.php">Incoming</a></li>
      <li><a href="mystore.php">My Store</a></li>
      <li class="activetab"><a href="myfavorstore.php">My Favor Store</a></li>
      <li><a href="outgoing.php">Outgoing</a></li>
     </ul>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
<?php
if(count($available) == 0)
  echo '<p class="failure">You have no custom items available to list.  (Either you\'ve listed them all, or have none to begin with.)</p>';
else
{
  echo '
    <div class="infotip">
     <p>Custom items cost 300 Favor, therefore the minimum you may ask for is 300 Favor.  Any additional amount you ask for is the amount you will receive when someone buys the item.  For example, if you list an item for 400 Favor, you will receive 100 Favor whenever a copy of that item is purchased.</p>
     <p class="nomargin">If you don\'t want to list an item for sale, leave the price blank.</p>
    </div>
    <form action="myfavorstore_add.php" method="post">
    <table>
     <thead>
      <tr class="titlerow"><th>Price (Favor)</th><th></th><th>Item</th></tr>
     </thead>
     <tbody>
  ';

  $rowclass = begin_row_class();

  foreach($available as $item)
  {
    echo '
      <tr class="' . $rowclass . '">
       <td><input type="text" maxlength="3" size="3" name="' . itemname_to_form_value($item['itemname']) . '" /></td>
       <td class="centered">' . item_display_extra($item) . '</td>
       <td>' . $item['itemname'] . '</td>
      </tr>
    ';

    $rowclass = alt_row_class($rowclass);
  }

  echo '
     </tbody>
    </table>
    <p><input type="submit" name="action" value="List Items for Sale" class="bigbutton" /></p>
    </form>
  ';
}
?>
<ul><li><a href="/help/myfavorstore.php">Why aren't all my custom items listed here?</a></li></ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
