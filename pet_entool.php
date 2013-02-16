<?php
$whereat = 'home';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/petlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/petblurb.php';
require_once 'commons/equiplib.php';
require_once 'commons/questlib.php';

$petid = (int)$_GET['id'];
$show_all = ($_GET['showall'] == 1);

$pet = get_pet_byid($petid);

if($pet['user'] != $user['user'] || $pet['location'] != 'home' || $pet['zombie'] != 'no')
{
  header('Location: ./myhouse.php');
  exit();
}

function get_equipable_inventory(&$user)
{
  $inventory = get_houseinventory_byuser_forpets($user['user']);

  $already = array();
  $items = array('equipment' => array(), 'keys' => array());

  foreach($inventory as $i)
  {
    $item = get_item_byname($i['itemname']);
  
    if($item['is_equipment'] == 'yes')
    {
      if($already[$i['itemname']][$i['health']] !== true)
      {
        $items['equipment'][] = array('item' => $item, 'inventory' => $i);
        $already[$i['itemname']][$i['health']] = true;
      }
    }
    
    if($item['key_id'] > 0)
      $items['keys'][$i['itemname']] = array('item' => $item, 'inventory' => $i);
  }

  return $items;
}

if($_GET['action'] == 'equip')
{
  $id = (int)$_GET['tool'];

  $tool = get_inventory_byid($id);
  $item = get_item_byname($tool['itemname']);

  if($item['is_equipment'] != 'yes')
  {
    $message_list[] = '<span class="failure">Selected item is not an equipment.</span>';
  }
  else if($item['equipl33tonly'] == 'yes')
  {
    $message_list[] = '<span class="failure">Pet does not meet equipment requirements.</span>';
  }
  else if($item['equipreincarnateonly'] == 'yes' && $pet['incarnation'] == 1)
  {
    $message_list[] = '<span class="failure">Pet does not meet equipment requirements.</span>';
  }
  else if($item['req_str'] > $pet['str'] || $item['req_dex'] > $pet['dex'] || $item['req_sta'] > $pet['sta'] ||
    $item['req_per'] > $pet['per'] || $item['req_int'] > $pet['int'] || $item['req_wit'] > $pet['wit'] ||
    $item['req_athletics'] > $pet['athletics'])
  {
    $message_list[] = '<span class="failure">Pet does not meet equipment requirements.</span>';
  }
  else if($tool['user'] == $user['user'] && substr($tool['location'], 0, 4) == 'home')
  {
    if($pet['toolid'] > 0)
    {
      $command = "UPDATE monster_inventory SET location='home',user=" . quote_smart($user['user']) . ",changed='" . time() . "' WHERE idnum=" . $pet['toolid'] . ' LIMIT 1';
      $database->FetchNone($command, 'unequipping pet (1)');

      $command = "UPDATE monster_pets SET toolid=0,costumed='no' WHERE idnum=" . $pet['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'unequipping pet (2)');
    }

    $command = "UPDATE monster_inventory SET location='pet' WHERE idnum=$id LIMIT 1";
    $database->FetchNone($command, 'equipping pet (1)');

    if(substr($item['itemtype'], 0, 16) == 'clothing/costume')
      $also = ',costumed=\'yes\'';
    else
      $also = ',costumed=\'no\'';

    // unequip any pets which are somehow already equipped with this item
    $database->FetchNone('UPDATE monster_pets SET toolid=0,costumed=\'no\' WHERE toolid=' . $id);
    
    // equip this pet
    $database->FetchNone('UPDATE monster_pets SET toolid=' . $id . $also . ' WHERE idnum=' . $petid . ' LIMIT 1');

    $pet['toolid'] = $id;
    
    if($user['equip_and_home'] == 'yes')
    {
      header('Location: ./myhouse.php');
      exit();
    }
  }
}
else if($_GET['action'] == 'usekey')
{
  $id = (int)$_GET['key'];

  $key = get_inventory_byid($id);
  $item = get_item_byname($key['itemname']);

  if($item['key_id'] == 0)
  {
    $message_list[] = '<span class="failure">Selected item is not a key.</span>';
  }
  else if($key['user'] == $user['user'] && substr($key['location'], 0, 4) == 'home')
  {
    if($pet['keyid'] > 0)
    {
      $command = "UPDATE monster_inventory SET location='home',user=" . quote_smart($user['user']) . ",changed='" . time() . "' WHERE idnum=" . $pet['keyid'] . ' LIMIT 1';
      $database->FetchNone($command, 'unequipping key (1)');

      $command = "UPDATE monster_pets SET keyid=0 WHERE idnum=" . $pet['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'unequipping key (2)');
    }

    $command = "UPDATE monster_inventory SET location='pet' WHERE idnum=$id LIMIT 1";
    $database->FetchNone($command, 'equipping key (1)');

    $command = 'UPDATE monster_pets SET keyid=' . $id . ' WHERE idnum=' . $petid . ' LIMIT 1';
    $database->FetchNone($command, 'equipping key (2)');
    $pet['keyid'] = $id;

    if($user['equip_and_home'] == 'yes')
    {
      header('Location: ./myhouse.php');
      exit();
    }
  }
}

$items = get_equipable_inventory($user);

if($_GET['returnhome'] == 'yes' || $_GET['returnhome'] == 'no')
{
  $user['equip_and_home'] = $_GET['returnhome'];
  $command = 'UPDATE monster_users SET equip_and_home=\'' . $_GET['returnhome'] . '\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating equip_and_home preference');
}

$equipment_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: equipment');
if($equipment_tutorial_quest === false)
  $no_tip = true;

if($pet['toolid'] > 0)
  $tool_item = get_inventory_byid($pet['toolid']);

if($pet['keyid'] > 0)
  $key_item = get_inventory_byid($pet['keyid']);

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; My House &gt; Equip <?= $pet['petname'] ?></title>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery.tablesorter.min.js"></script>
  <script type="text/javascript">
   function move_on(petid)
   {
     if(confirm('Moving on is permanent. (Strangely, in <?= $SETTINGS['site_name'] ?>, death alone is not).\nAre you sure you want to move on?'))
       window.location.href = "moveon.php?petid=" + petid;
   }

   function color_table_rows()
   {
     $('#equiprows tr').removeClass('row');
     $('#equiprows tr').removeClass('altrow');
     $('#equiprows tr:even').addClass('altrow');
     $('#equiprows tr:odd').addClass('row');
   }

   $(function() {
     $('#equiptable').tablesorter({
       headers: {
         0: { sorter: false },
         1: { sorter: false },
         4: { sorter: false },
         5: { sorter: false }
       }
     });

     $('#equiptable').bind('sortEnd', color_table_rows);
   });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($equipment_tutorial_quest === false)
{
  include 'commons/tutorial/equipment.php';
  add_quest_value($user['idnum'], 'tutorial: equipment', 1);
}

echo '<h4><a href="/myhouse.php">My House</a> &gt; Equip ' . $pet['petname'] . '</h4>';

if($user['equip_and_home'] == 'yes')
  echo '<p><i>When I equip a pet, return me to My House (<a href="/pet_entool.php?id=' . $petid . '&returnhome=no">change this</a>).</i></p>';
else
  echo '<p><i>When I equip a pet, do not return me to My House (<a href="/pet_entool.php?id=' . $petid . '&returnhome=yes">change this</a>).</i></p>';

echo '<ul class="tabbed">';

foreach($userpets as $tabpet)
{
  if($tabpet['idnum'] == $petid)
    echo ' <li class="activetab"><a href="/pet_entool.php?id=' . $tabpet['idnum'] . '">' . $tabpet['petname'] . '</a></li>';
  else
    echo ' <li><a href="/pet_entool.php?id=' . $tabpet['idnum'] . '">' . $tabpet['petname'] . '</a></li>';
}

echo '</ul>';

pet_blurb($user, $house, 0, 1, $pet, false);

if($pet['eggplant'] == 'yes')
  echo '<p class="failure" style="padding-top:1em;">A pet afflicted by the Eggplant Curse does not gain any benefits - or penalties - from equipment.  (It\'s as if the pet was not equipped at all.)</p>';
?>
<div style="width:520px; float:left;">
<h5>Tools<a href="/help/equipment.php" class="help">?</a></h5>
<?php
if(count($items['equipment']) > 0)
{
  if($show_all)
    echo '<p>Only one of each item is shown here.  However if two or more of the same items have different durability conditions, they will all be shown.  (<a href="pet_entool.php?id=' . $petid . '">Only show me the items this pet can equip</a>.)</p>';
  else
    echo '<p>Only one of each item this pet can equip is shown here.  However if two or more of the same items have different durability conditions, they will all be shown.  (<a href="pet_entool.php?id=' . $petid . '&showall=1">Show me the items this pet cannot equip as well</a>.)</p>';

  $rowclass = begin_row_class();

  if($pet['toolid'] > 0)
    echo '<ul><li><a href="/pet_detool.php?id=' . $pet['idnum'] . '">Unequip ' . $tool_item['itemname'] . '</a></li></ul>';
?>
<table id="equiptable">
 <thead>
  <tr class="titlerow">
   <th></th>
   <th style="min-width:48px;"></th>
   <th style="min-width:100px;">Item</th>
   <th class="centered" style="padding-right:16px;">Biggest<br />Effect</th>
   <th>Condition</th>
   <th></th>
  </tr>
 </thead>
 <tbody id="equiprows">
<?php
  foreach($items['equipment'] as $i)
  {
    $item = $i['item'];
    $inventory = $i['inventory'];

    $reason = get_equip_message($item, $pet);
    
    if($reason != '' && !$show_all)
      continue;

    $equip_level = EquipLevel($item);
?>
  <tr class="<?= $rowclass ?>">
   <td><?= $reason == '' ? '<a href="/pet_entool.php?id=' . $petid . '&tool=' . $inventory['idnum'] . '&action=equip">Equip</a>' : '' ?></td>
   <td class="centered"><?= item_display($item, '') ?></td>
   <td><?= $item['itemname'] ?></td>
   <td class="centered"><?= ucfirst(EquipBonusDesc($equip_level)) ?></td>
   <td><?= durability($inventory['health'], $item['durability']) ?></td>
   <td class="failure"><?= $reason ?></td>
  </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
 </tbody>
</table>
<?php
  if($pet['toolid'] > 0)
    echo '<ul><li><a href="/pet_detool.php?id=' . $pet['idnum'] . '">Unequip ' . $tool_item['itemname'] . '</a></li></ul>';
}
else
{
  echo '<p>You do not own any equipment.  (If you have any in Storage, move them to your house first.  Items in Storage are not accessible to your pets.)</p>';

  if($pet['toolid'] > 0)
    echo '<ul><li><a href="/pet_detool.php?id=' . $pet['idnum'] . '">Unequip ' . $tool_item['itemname'] . '</a></li></ul>';
}
?>
</div>
<div style="width:320px; padding-left:10px; margin-left:10px; border-left: 1px dotted #999; float:left;">
<h5>Keys<a href="/help/equipment.php" class="help">?</a></h5>
<?php
if(count($items['keys']) > 0)
{
  echo '<p>Only one of each item is shown here.</p>';

  if($pet['keyid'] > 0)
    echo '<ul><li><a href="/pet_dekey.php?id=' . $pet['idnum'] . '">Unequip ' . $key_item['itemname'] . '</a></li></ul>';

  $rowclass = begin_row_class();
?>
<table>
 <thead>
  <tr class="titlerow">
   <th></th>
   <th style="min-width:48px;"></th>
   <th style="min-width:200px;">Item</th>
  </tr>
 </thead>
 <tbody>
<?php
  foreach($items['keys'] as $i)
  {
    $item = $i['item'];
    $inventory = $i['inventory'];
?>
  <tr class="<?= $rowclass ?>">
   <td><a href="/pet_entool.php?id=<?= $petid ?>&key=<?= $inventory['idnum'] ?>&action=usekey">Equip</a></td>
   <td class="centered"><?= item_display($item) ?></td>
   <td><?= $item['itemname'] ?></td>
  </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
 </tbody>
</table>
<?php
  if($pet['keyid'] > 0)
    echo '<ul><li><a href="/pet_dekey.php?id=' . $pet['idnum'] . '">Unequip ' . $key_item['itemname'] . '</a></li></ul>';
}
else
{
  echo '<p>You do not own any keys.  (If you have any in Storage, move them to your house first.  Items in Storage are not accessible to your pets.)</p>';

  if($pet['keyid'] > 0)
    echo '<ul><li><a href="/pet_dekey.php?id=' . $pet['idnum'] . '">Unequip ' . $key_item['itemname'] . '</a></li></ul>';
}
?>
</div>
<div style="clear:both;">
</div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
