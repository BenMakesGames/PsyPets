<?php
$whereat = 'smithery';
$wiki = 'The_Smithery';
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

$special_offer = (($now_month == 10 && $now_day >= 21) || ($now_month == 11 && $now_day <= 3));
$special_offer = $special_offer || (($now_month == 12 && $now_day >= 12) || $now_month == 1);

$command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname=\'Duct Tape\'';
$data = $database->FetchSingle($command, 'fetching duct tape count');
$duct_tape = (int)$data['c'];

if($_POST['action'] == 'repair')
{
  $ids = array();
  $repaircost = 0;

  foreach($_POST as $key=>$value)
  {
    if($key{0} == 's' && $key{1} == '_')
    {
      $itemid = (int)substr($key, 2);
      
      $ok_owner = false;

      $item = get_inventory_byid($itemid);
      
      if($item['idnum'] != $itemid)
      {
        $error_message = "This item ($itemid) doesn't exist...?  That is strange.";
        break;
      }

      if($item['user'] == $user['user'])
        $ok_owner = true;
      else if($item['location'] == 'pet')
      {
        foreach($userpets as $pet)
        {
          if($pet['toolid'] == $itemid)
          {
            $ok_owner = true;
            break;
          }
        }
      }

      if($ok_owner === false)
      {
        $error_message = 'You do not own this ' . $item['itemname'] . '!';
        break;
      }

      $details = get_item_byname($item['itemname']);

      if($item['health'] < $details['durability'] && $details['norepair'] == 'no')
      {
        $ids[$itemid] = $details['durability'];
        $repaircost++;
      }
    }
  }

  if(strlen($error_message) == 0)
  {
    if(count($ids) == 0)
      $error_message = 'Which items did you say you want me to fix?';
    else if($repaircost > $duct_tape)
    {
      if(count($ids) == 1)
        $error_message = '<span class="failure">Sorry, you do not have enough Duct Tape to fix this item.</span>';
      else
        $error_message = '<span class="failure">Sorry, you do not have enough Duct Tape to fix these items.</span>';
    }
    else
    {
      $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Duct Tape\' AND location=\'storage\' LIMIT ' . $repaircost;
      $database->FetchNone($command, 'deleting duct tape');
      
      $duct_tape -= $repaircost;

      foreach($ids as $idnum=>$durability)
      {
        $command = "UPDATE monster_inventory SET health=$durability WHERE idnum=$idnum LIMIT 1";
        $database->FetchNone($command, 'repairing item');
      }

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Repaired an Item with Duct Tape', $repaircost);

      $error_message = '<span class="success">Everything is fixed!  Let me know if there is anything else you need.</span>';
    }
  }
}

// get items from pets
$command = 'SELECT a.*,b.durability,b.norepair,b.value,b.graphic,b.graphictype,c.idnum AS petidnum,c.petname,c.graphic AS petgraphic,c.changed,c.dead FROM monster_inventory AS a,monster_items AS b,monster_pets AS c WHERE c.user=' . quote_smart($user['user']) . ' AND c.location=\'home\' AND a.itemname=b.itemname AND a.idnum=c.toolid AND a.health<b.durability ORDER BY a.itemname';
$tools = $database->FetchMultiple($command, 'fetching items to repair from equipment');

// get items from storage
$command = 'SELECT a.*,b.durability,b.norepair,b.value,b.graphic,b.graphictype FROM monster_inventory AS a,monster_items AS b WHERE a.itemname=b.itemname AND a.user=' . quote_smart($user['user']) . ' AND a.health<b.durability AND a.location=\'storage\' ORDER BY a.itemname';
$items = $database->FetchMultiple($command, 'fetching items to repair from storage');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Smithery</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery.tablesorter.min.js"></script>
  <script type="text/javascript">
   function check_most(start, count)
   {
     for(i = start; i < start + count; ++i)
     {
       document.repairlist.elements[i].checked = document.repairlist.elements[start - 1].checked;
     }
   }

   $(function() {
     $('#repair-pet-equipment').tablesorter({
       textExtraction: function(node) { return node.getAttribute('data-sort') || node.innerHTML; },
       headers: {
         0: { sorter: false },
         1: { sorter: false },
         3: { sorter: false },
         6: { sorter: false }
       }
     });

     $('#repair-pet-equipment').bind('sortEnd', function() {
       $('#repair-pet-equipment tbody tr').removeClass('row altrow');
       $('#repair-pet-equipment tbody tr:even').addClass('altrow');
       $('#repair-pet-equipment tbody tr:odd').addClass('row');
     });
   });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Smithery &gt; Repair</h4>
     <ul class="tabbed">
      <li><a href="/smith.php">Smith</a></li>
      <li class="activetab"><a href="/repair.php">Repair</a></li>
      <li><a href="/af_getrare2.php">Unique Item Shop</a></li>
      <li><a href="/af_combinationstation3.php">Combination Station</a></li>
<?php
if($special_offer)
  echo '<li><a href="/specialoffer_smith.php">Special Offer <i style="color:red;">ooh!</i></a></li>';
?>
<!--      <li><a href="af_replacegraphic.php">Broken Image Repair</a></li>-->
     </ul>
<?php
// SMITHY NPC NINA
echo '<a href="/npcprofile.php?npc=Nina+Faber"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/smithy2.png" align="right" width="350" height="280" alt="(Nina the Smithy)" /></a>';

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

include 'commons/dialog_open.php';

if($error_message)
  echo "<p>$error_message</p>";
else
{
?>
<p>Which items do you need to fix?  You will need one <?= item_text_link('Duct Tape') ?> for each item.</p>
<p>Remember: I cannot fix an item once it breaks completely (reduced to <?= item_text_link('Rubble') ?> or <?= item_text_link('Ruins') ?>), so come to me before that happens!</p>
<?php
}
include 'commons/dialog_close.php';

echo '<p>You have ' . $duct_tape . ' ' . item_text_link('Duct Tape') . ' in Storage.</p>';

if(count($items) > 0 || count($tools) > 0)
{
  $checkallboxes = 0;
  
  echo '<form method="post" id="repairlist" name="repairlist">';
}

echo '<h4>Items Equipped to Pets</h4>';

if(count($tools) > 0)
{
  $checkallboxes++;
?>
     <table id="repair-pet-equipment">
      <thead>
       <class="titlerow">
        <th><input type="checkbox" title="(check all)" onclick="check_most(<?= $checkallboxes ?>, <?= count($tools) ?>)" /></th>
        <th></th>
        <th>Pet</th>
        <th></th>
        <th>Item</th>
        <th><div style="padding-right:10px;">Condition</div></th>
        <th></th>
       </tr>
      </thead>
      <tbody>
<?php
  $rowclass = begin_row_class();

  foreach($tools as $item)
  {
    $mypet['idnum'] = $item['petidnum'];
    $mypet['dead'] = $item['dead'];
    $mypet['graphic'] = $item['petgraphic'];
    $mypet['changed'] = $item['changed'];

    if($item['norepair'] == 'yes')
    {
?>
       <tr class="<?= $rowclass ?>">
        <td><input type="checkbox" disabled /></td>
        <td><?= pet_graphic($mypet) ?></td>
        <td class="dim"><?= $item['petname'] ?></td>
        <td class="centered"><?= item_display($item, '') ?></td>
        <td class="dim"><?= $item['itemname'] ?></td>
        <td class="dim centered" data-sort="<?= durability_sort($item['health'], $item['durability']) ?>"><?= durability($item['health'], $item['durability']) ?></td>
        <td>This item cannot be repaired.</td>
       </tr>
<?php
    }
    else
    {
?>
       <tr class="<?= $rowclass ?>">
        <td><input type="checkbox" name="s_<?= $item['idnum'] ?>" /></td>
        <td><?= pet_graphic($mypet) ?></td>
        <td><?= $item['petname'] ?></td>
        <td class="centered"><?= item_display($item, '') ?></td>
        <td><?= $item['itemname'] ?></td>
        <td class="centered" data-sort="<?= durability_sort($item['health'], $item['durability']) ?>"><?= durability($item['health'], $item['durability']) ?></td>
        <td></td>
       </tr>
<?php
    }

    $rowclass = alt_row_class($rowclass);
  }
  
  echo '</tbody></table>';
}
else
  echo '<p>Your pets are not equipped with items that need repair.</p>';

echo '<h4>Items in Storage</h4>';

if(count($items) > 0)
{
  $checkallboxes++;
?>
     <table id="repair-storage-equipment">
      <tr class="titlerow">
       <th><input type="checkbox" title="(check all)" onclick="check_most(<?= count($tools) + $checkallboxes ?>, <?= count($items) ?>)" /></th>
       <th></th>
       <th>Item</th>
       <th>Condition</th>
       <th></th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($items as $item)
  {
    if($item['norepair'] == 'yes')
    {
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="checkbox" name="s_<?= $item['idnum'] ?>" /></td>
       <td class="centered"><?= item_display($item, '') ?></td>
       <td class="dim"><?= $item['itemname'] ?></td>
       <td class="dim centered" data-durability-sort="<?= durability_sort($item['health'], $item['durability']) ?>"><?= durability($item['health'], $item['durability']) ?></td>
       <td>This item cannot be repaired.</td>
      </tr>
<?php
    }
    else
    {
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="checkbox" name="s_<?= $item['idnum'] ?>" /></td>
       <td class="centered"><?= item_display($item, '') ?></td>
       <td><?= $item['itemname'] ?></td>
       <td class="centered" data-durability-sort="<?= durability_sort($item['health'], $item['durability']) ?>"><?= durability($item['health'], $item['durability']) ?></td>
       <td></td>
      </tr>
<?php
    }

    $rowclass = alt_row_class($rowclass);
  }
  
  echo '</table>';
}
else
  echo '<p>There are no items in your storage that require repair.</p>';

if(count($items) > 0 || count($tools) > 0)
{
?>
     <p><input type="hidden" name="action" value="repair" /><input type="submit" value="Repair" /></p>
     </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
