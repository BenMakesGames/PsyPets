<?php
$whereat = 'mystore';
$wiki = 'My_Store';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/sellermarketlib.php';
require_once 'commons/marketlib.php';
require_once 'commons/questlib.php';
require_once 'commons/houselib.php';
require_once 'commons/economylib.php';
require_once 'commons/messages.php';
require_once 'commons/itemstats.php';
require_once 'commons/sketchbooklib.php';

if($user['license'] == 'no')
{
  header('Location: ./storage.php');
  exit();
}

$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);

if($house['locid'] != $locid)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$addons = take_apart(',', $house['addons']);

$my_store_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: my store');
if($my_store_tutorial_quest === false)
  $no_tip = true;

$command = 'SELECT COUNT(idnum) AS c ' .
           'FROM monster_inventory ' .
           'WHERE `user`=' . quote_smart($user['user']) . ' AND location=\'storage/mystore\' AND forsale>0 LIMIT 1';
$data = $database->FetchSingle($command, 'mystore.php');

$items_for_sale = (int)$data['c'];

$items = array();

$total_size = 0;

$messages = array();

if($_POST['submit'] == 'Update Inventory')
{
	$items = $database->FetchMultiple(
		'SELECT idnum,itemname,forsale FROM monster_inventory ' .
		'WHERE `user`=' . quote_smart($user['user']) . ' AND `location`=\'storage/mystore\''
	);

  $items_for_sale = 0;
  $updated = false;
	$updates = array();

  foreach($items as $this_item)
  {
    $sell_value = (int)$_POST[$this_item['idnum'] . '_sellfor'];

    if($sell_value > 0)
      $items_for_sale++;
    else
      $sell_value = 0;

    // don't do anything if the sell value hasn't changed
    if($sell_value != $this_item['forsale'])
    {
      $details = get_item_byname($this_item['itemname']);
      if($details['cursed'] == 'yes' || $details['noexchange'] == 'yes')
        continue;

      if($sell_value > 0 && $details['nosellback'] == 'no')
      {
        $sellback = ceil($details['value'] * sellback_rate());
        if($sell_value <= $sellback)
        {
          record_item_disposal($this_item['itemname'], 'sold', 1);
          delete_inventory_byid($this_item['idnum']);

          $gamesell_money += $sellback;
          $gamesell_items++;

          $updated = true;

          continue;
        }
      }

			$updates[$sell_value][] = $this_item['idnum'];
    }
  }
	
	if(count($updates) > 0)
	{
		foreach($updates as $new_value=>$item_id_list)
		{
			$database->FetchNone('
				UPDATE monster_inventory
				SET forsale=' . (int)$new_value . '
				WHERE idnum ' . $database->In($item_id_list) . '
				LIMIT ' . count($item_id_list) . '
			');
		}

    $updated = true;
	}

  if($updated)
    $messages[] = '<span class="success">Store inventory has been updated.</span>';
  else
    $messages[] = '<span class="progress">Your store inventory would have been updated, if there were any changes to record :P</span>';

  if($gamesell_money > 0)
  {
    $messages[] = '<p class="progress">Ian Hobbs notices your cheap item' . ($gamesell_items > 1 ? 's, and buys them' : ', and buys it') . '.  He insists on paying no less than Gamesell value.  (You receive ' . $gamesell_money . '<span class="money">m</span>.)</p>';
    give_money($user, $gamesell_money, 'Sold ' . $gamesell_items . ' item' . ($gamesell_items == 1 ? '' : 's') . ' to Ian Hobbs');
    $user['money'] += $gamesell_money;
  }

  if($items_for_sale == 0 && $user['openstore'] == 'yes')
  {
    $user['openstore'] = 'no';

    $command = 'UPDATE monster_users ' .
               'SET `openstore`=' . quote_smart($user['openstore']) . ' ' .
               'WHERE `idnum`=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'mystore.php');
       
    $messages[] = '<span class="progress">Your store was automatically closed, since you are no longer listing inventory for sale.</span>';
  }
} // update inventory
else if($_POST['action'] == 'Open Store' && $user['openstore'] == 'no')
{
  if($items_for_sale > 0)
  {
    $user['openstore'] = 'yes';

    $command = 'UPDATE monster_users ' .
               'SET `openstore`=' . quote_smart($user['openstore']) . ' ' .
               'WHERE `idnum`=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'mystore.php');
    
    $messages[] = '<span class="success">Your store is now open!  (w00t!)</span>';
  }
  else
    $messages[] = '<span class="failure">You cannot open a store with no inventory.</span>';
}
else if($_POST['action'] == 'Close Store' && $user['openstore'] == 'yes')
{
  $user['openstore'] = 'no';

  $command = 'UPDATE monster_users ' .
             'SET `openstore`=' . quote_smart($user['openstore']) . ' ' .
             'WHERE `idnum`=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'mystore.php');

  $messages[] = '<span class="success">Your store has been closed... (aw...)</span>';
}
else if($_POST['submit'] == 'Rename')
{
  $_POST['storename'] = trim(stripslashes($_POST['storename']));
   
  if(strlen($_POST['storename']) < 3 || strlen($_POST['storename']) > 48)
    $messages[] = '<span class="failure">Your store name name must be between 3 and 48 characters.  No more.  No less.</span>';
  else
  {
    $user['storename'] = $_POST['storename'];

    $command = 'UPDATE monster_users ' .
               'SET storename=' . quote_smart($user['storename']) . ' ' .
               'WHERE `idnum`=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'mystore.php');
     
    $messages[] = '<span class="success">Store\'s updated successfully.</span>';
  }
}
else if($_GET['action'] == 'clearclosed' && $user['storeclosed'] == 'yes')
{
  $command = 'UPDATE monster_users SET storeclosed=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'clearing store close notification');
  
  $user['storeclosed'] = 'no';
}
else if($_POST['action'] == 'Hide Maker')
{
  $command = 'UPDATE monster_users SET stack_mystore_items=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'stacking store items');
  
  $user['stack_mystore_items'] = 'yes';
  
  $messages[] = '<span class="success">The maker field for items you sell are no longer be listed, allowing for a more compact view of your store.  However, when a player buys items from your store, the maker will be forgotten.</span>';
}
else if($_POST['action'] == 'Show Maker')
{
  $command = 'UPDATE monster_users SET stack_mystore_items=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'un-stacking store items');
  
  $user['stack_mystore_items'] = 'no';

  $messages[] = '<span class="success">The maker field for items you sell are now listed.</span>';
}
 
$command = 'SELECT * FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage/mystore\' ORDER BY itemname ASC';
$my_inventory = $database->FetchMultiple($command, 'fetching store inventory');

$num_inventory_items = count($my_inventory);

$total_items = 0;
$total_value = 0;

if($num_inventory_items > 0)
{
  foreach($my_inventory as $item)
  {    if($item['forsale'] > 0)
    {
      $total_items++;
      $total_value += $item['forsale'];
    }
  }
}

$rooms[] = 'Storage';
$rooms[] = 'Locked Storage';
$rooms[] = 'Common';

if(strlen($house['rooms']) > 0)
{
  $m_rooms = explode(',', $house['rooms']);
  foreach($m_rooms as $room)
    $rooms[] = $room;
}

if(array_search('Library', $addons) !== false)
  $rooms[] = 'Library Add-on';
if(array_search('Basement', $addons) !== false)
  $rooms[] = 'Basement';

$sketch_id = get_store_sketch_id($user['idnum']);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Store, "<?= $user['storename'] ?>"</title>
<?php include 'commons/head.php'; ?>
<?php include 'commons/ajaxinventoryjs.php'; ?>
  <script type="text/javascript">
   function copyprice(itemid, invid)
   {
     var price = document.getElementById('i' + itemid + 'n' + invid).value;
     var search = 'i' + itemid + 'n';
     
     for(var i = 0; i < document.getElementById('homeaction').elements.length; ++i)
     {
       var element = document.getElementById('homeaction').elements[i];
       if(element.id.substr(0, search.length) == search)
         element.value = price;
     }
   }
   
   function edit_image()
   {
     $('#shopkeep').html(
       '<applet code="main.PsyPetsSketch" java_codebase="./" archive="<?= $PSYPETS_SKETCH_VERSION ?>.jar" width="350" height="604">' +
       '<param name="code" value="main.PsyPetsSketch">' +
       '<param name="codebase" value="./">' +
       '<param name="archive" value="<?= $PSYPETS_SKETCH_VERSION ?>.jar">' +
       '<param name="type" value="application/x-java-applet;version=1.6">' +
       '<param name="userid" value="<?= $user['idnum'] ?>" />' +
       '<param name="sessionid" value="<?= $user['sessionid'] ?>" />' +
       '<param name="sketchid" value="<?= $sketch_id ?>" />' +
       '<param name="shopkeep" value="1" />' +
       '</applet>'
     );
   }

   $(function() {
     $('.list_price').change(function() {
       var gs_element = $(this).parent().parent().parent().find('.gamesell_value');
       var gs_value = parseInt(gs_element.html());
       var list_price = parseInt($(this).val());

       if(list_price > 0 && list_price <= gs_value)
       {
         gs_element.parent().parent().css({'background-color': '#fed'});
         gs_element.parent().addClass('failure');
       }
       else
       {
         gs_element.parent().parent().css({'background-color': 'transparent'});
         gs_element.parent().removeClass('failure');
       }
     });
   });
  </script>
 </head>
 <body>
<?php
include 'commons/header_2.php';

if($my_store_tutorial_quest === false)
  include 'commons/tutorial/mystore.php';
?>
     <form method="post">
     <h4 class="nomargin"><?= $user['display'] ?>'s Store, <input type="hidden" name="submit" value="Rename"><input name="storename" value="<?= $user['storename'] ?>" maxlength="48" style="width:192px;" /> <input type="submit" value="Rename" /> <a href="storagesummary.php"><img src="gfx/summary.png" width="18" height="16" alt="(summary)" border="0" /></a></h4>
<?php
echo '<p style="padding-left: 2em;"><a href="autosort.php?applyto=storage/mystore">auto-sort items</a> | <a href="autosort_edit.php">configure auto-sorter</a>';

if($user['autosorterrecording'] == 'yes')
  echo ' | <span id="recordingautosort"><a href="#" onclick="stop_recording(); return false;">&#9632;</a> <blink style="color:red;">recording moves</blink></span>';
else
  echo ' | <span id="recordingautosort"><a href="#" onclick="start_recording(); return false;" style="color:red;">&#9679;</a></span>';

echo '</p>';
?>
     <p><i>(Storage may be filled beyond its maximum capacity, however you will pay for the additional space when you collect <a href="allowance.php">allowance</a>.)</i></p>
<?php
if($user['storeclosed'] == 'yes')
{
  echo '
    <div class="failure-message-box">
     <p>Your store was closed automatically because you have not run house hours in a while.  It will be re-opened automatically the next time you run house hours.</p>
     <ul class="nomargin"><li><a href="mystore.php?action=clearclosed">Do not automatically re-open my store.</a></li></ul>
    </div>
  ';
}
?>
     <ul class="tabbed">
      <li><a href="storage.php">Storage</a></li>
      <li><a href="storage_locked.php">Locked Storage</a></li>
      <li><a href="incoming.php">Incoming</a></li>
      <li class="activetab"><a href="mystore.php">My Store</a></li>
      <li><a href="myfavorstore.php">My Custom Item Store</a></li>
      <li><a href="outgoing.php">Outgoing</a></li>
     </ul>
<?php
echo '<div style="width:340px; float:right; clear:right; margin-left:1em; margin-bottom:1em; padding:5px; border: 1px solid #000;"><div>';

if($user['openstore'] == 'yes')
  echo '
    <form method="post" id="store-state-form">
    <p>Your store is <strong style="color:green;">open</strong>.</p>
    <p style="margin-left:20px;"><input type="submit" name="action" value="Close Store" /></p>
    </form>
  ';
else
{
  echo '
    <form method="post" id="store-state-form">
    <p>Your store is <strong style="color:red;">closed</strong>.</p><p>';

  if($house['lasthour'] > $now - (48 * 60 * 60))
    echo ($items_for_sale > 0 ? '<p style="margin-left:20px;"><input type="submit" name="action" value="Open Store" /></p>' : '<p>Your store has no inventory for sale.</p>');
  else
    echo '<p>You have not run house hours recently.</p>';
  
  echo '</form>';
}

echo '</div><div style="border-top: 1px dashed #888; padding-top: 5px;">';

echo '<form method="post">';

if($user['stack_mystore_items'] == 'yes')
  echo '
    <p>Players buying from your store will not see the maker of each item for sale.</p>
    <p style="margin-left:20px; margin-bottom: 0;"><input type="submit" name="action" value="Show Maker" /></p>
  ';
else
  echo '
    <p>Players buying from your store will see the maker of each item for sale.
    <p style="margin-left:20px; margin-bottom: 0;"><input type="submit" name="action" value="Hide Maker" /></p>
  ';

echo '</form>';

echo '</div></div>';
?>
<div style="width:350px; float:right; clear:right; margin-left:1em; margin-bottom:1em; padding:0; border: 1px solid #000;" id="shopkeep">
<p><img src="sketch.php?id=<?= $sketch_id ?>" width="350" height="500" alt="" /></p>
<center>(<a href="#" onclick="edit_image(); return false;">edit image</a>)</center>
</div>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';

if($total_items > 0)
  echo '<p>You currently have a total of ' . $total_value . '<span class="money">m</span> in ' . $total_items . ' item' . ($total_items != 1 ? 's' : '') . ' listed.</p>';

if($num_inventory_items > 0)
{
?>
     <p>If you do not want to sell an item, leave the price blank, or move it out of your store.</p>
     <div id="message_area"></div>
<form action="mystore.php" method="post" id="homeaction" name="homeaction">
<table>
 <tr>
  <td><input type="submit" name="submit" value="Update Inventory" class="bigbutton" /></td>
  <td>
   <input type="button" value="Move to" onclick="move_items('move')" />&nbsp;<select id="move1" name="move1" onchange="document.getElementById('move2').selectedIndex = this.selectedIndex">
<?php
  foreach($rooms as $room)
    echo '    <option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>' . "\n";
?>
   </select>
  </td>
 </tr>
</table>
<?php
  render_inventory_xhtml_2_mystore($my_inventory, true);
?>
<table>
 <tr>
  <td><input type="submit" name="submit" value="Update Inventory" class="bigbutton" /></td>
  <td>
   <input type="button" value="Move to" onclick="move_items('move')" />&nbsp;<select id="move2" name="move2" onchange="document.getElementById('move1').selectedIndex = this.selectedIndex">
<?php
  foreach($rooms as $room)
    echo '    <option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>' . "\n";
?>
   </select>
  </td>
 </tr>
</table>
</form>
<?php
}
else
  echo '<p>Your store is empty.</p>';
?>
     <h5 style="padding-top: 2em; clear:both;">A Few Tips</h5>
     <p>
      The code for a link to your store is:<br />
      <span style="white-space:pre; font-family:monospace;">{link <?= $SETTINGS['protocol'] ?>://psypets.net/userstore.php?user=<?= link_safe($user['display']) ?>}</span>
     </p>
     <p>
      If you want to make the name of your store appear as the link, instead of the URL, use this:<br />
      <span style="white-space:pre; font-family:monospace;">{link <?= $SETTINGS['protocol'] ?>://psypets.net/userstore.php?user=<?= link_safe($user['display']) ?> <?= $user['storename'] ?>}</span>
     </p>
     <p>Use these links to advertise your store in the <a href="viewplaza.php?plaza=5">Commerce</a> section of the plaza, or in an <a href="broadcast.php">in-game ad</a>!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
