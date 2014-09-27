<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';

$command = 'SELECT favor FROM psypets_favor_history WHERE userid=' . $user['idnum'] . ' AND value<0 ORDER BY timestamp DESC';
$favors = $database->FetchMultiple($command, 'fetching favors');

$allowed_items = array();

if(count($favors) > 0)
{
  foreach($favors as $favor)
  {
    if(substr($favor['favor'], 0, 15) == 'custom item - "')
    {
      $itemname = substr($favor['favor'], 15, strlen($favor['favor']) - 16);
      
      $item = get_item_byname($itemname);
      
      if($item['is_equipment'] == 'yes' && $item['equip_is_revised'] == 'no')
        $allowed_items[$item['idnum']] = $item;
    }
  }
}

if(count($allowed_items) == 0)
{
  header('Location: ./recombination_na.php');
  exit();
}

$command = 'SELECT a.itemid,b.idnum,b.itemname,b.graphictype,b.graphic FROM psypets_museum AS a LEFT JOIN monster_items AS b ON a.itemid=b.idnum WHERE a.userid=' . $user['idnum'] . ' AND b.is_equipment=\'yes\' AND b.cancombine=\'yes\' ORDER BY b.itemname ASC';
$museum_items = $database->FetchMultipleBy($command, 'itemid', 'fetching museum items');

$museum_command = $command;

if($_POST['action'] == 'Recombine!')
{
  $custom_item_id = (int)$_POST['item'];
  $museum_item_1 = (int)$_POST['combine'][0];
  $museum_item_2 = (int)$_POST['combine'][1];
  $errored = false;
  
  if(count($_POST['combine']) != 2)
  {
    $errored = true;
    $message_list[] = '<span class="failure">You must select exactly two items to combine.</span>';
  }
  else if(!array_key_exists($museum_item_1, $museum_items) || !array_key_exists($museum_item_2, $museum_items))
  {
    $errored = true;
    $message_list[] = '<span class="failure">You must select two items to combine.</span>';
  }

  if(!array_key_exists($custom_item_id, $allowed_items))
  {
    $errored = true;
    $message_list[] = '<span class="failure">You did not specify the custom item to recombine.</span>';
  }
  
  if(!$errored)
  {
    $item1 = get_item_byid($museum_item_1);
    $item2 = get_item_byid($museum_item_2);

    $reqs = array(
      'req_str',
      'req_dex',
      'req_sta',
      'req_per',
      'req_int',
      'req_wit'
    );
    
    foreach($reqs as $key)
      $updates[] = $key . '=' . max($item1[$key], $item2[$key]);
    
    $effects = array(
      'equip_open',
      'equip_independent',
      'equip_extraverted',
      'equip_conscientious',
      'equip_str',
      'equip_dex',
      'equip_sta',
      'equip_per',
      'equip_int',
      'equip_wit',
      'equip_mining',
      'equip_lumberjacking',
      'equip_fishing',
      'equip_painting',
      'equip_sculpting',
      'equip_carpentry',
      'equip_jeweling',
      'equip_electronics',
      'equip_mechanics',
      'equip_adventuring',
      'equip_hunting',
      'equip_gathering',
      'equip_smithing',
      'equip_tailoring',
      'equip_leather',
      'equip_crafting',
      'equip_binding',
      'equip_chemistry',
      'equip_piloting',
      'equip_gardening',
      'equip_stealth',
      'equip_athletics',
      'equip_fertility'
    );
    
    $updates[] = 'equipreincarnateonly=\'' . (($item1['equipreincarnateonly'] == 'yes' || $item2['equipreincarnateonly'] == 'yes') ? 'yes' : 'no') . '\'';
    
    foreach($effects as $key)
      $updates[] = $key . '=' . ($item1[$key] + $item2[$key]);

    $command = 'UPDATE monster_items SET equip_is_revised=\'yes\',' . implode(',', $updates) . ' WHERE idnum=' . $custom_item_id . ' LIMIT 1';
    $database->FetchNone($command, 'recombining custom item');
    
    header('Location: ./recombination.php?msg=93');
    exit();
  }
  
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Recombination Station</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Recombination Station</h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";
?>
<form action="recombination.php" method="post">
<h5>Custom Equipment to Recombine</h5>
<p>Doing this will <strong>only</strong> update the equipment properties of the item, and it will do so for all copies of the item in existence!  Any other attributes - such as hourly effects, toy effects, and usable effects - will not be affected.</p>
<p>If recombination is not the solution for you, I can migrate items to the new system for you!  I'll of course keep the stats as faithful to the original as possible (in many cases, 100% possible).  PsyMail me (That Guy Ben) with the name of the item(s) if you'd like to do this.</p>
<table>
<tr class="titlerow"><th></th><th></th><th>Item</th></tr>
<?php
$rowclass = begin_row_class();
foreach($allowed_items as $item)
{
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="radio" name="item" value="<?= $item['idnum'] ?>" /></td>
  <td class="centered"><?= item_display_extra($item) ?></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<h5>Equipment You Have Donated to the Museum (<?= count($museum_items) ?>)</h5>
<?php
if(count($museum_items) == 0)
  echo '<p>You have not donated any equipment to the museum!</p>';
else
{
?>
<p>Select any two.  They will <strong>not</strong> be consumed.  You can also check the same item twice, if you want to double-up on that item for recombination.</p>
<table>
 <tr class="titlerow"><th></th><th></th><th>Item</th></tr>
<?php
  $rowclass = begin_row_class();
  foreach($museum_items as $item)
  {
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="checkbox" name="combine[]" value="<?= $item['idnum'] ?>" /> <input type="checkbox" name="combine[]" value="<?= $item['idnum'] ?>" /></td>
  <td class="centered"><?= item_display_extra($item) ?></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<?php
}
?>
<p><input type="submit" name="action" value="Recombine!" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
