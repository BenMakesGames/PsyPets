<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Airship Mooring';
$THIS_ROOM = 'Airship Mooring';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/blimplib.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if(!addon_exists($house, 'Airship Mooring'))
{
  header('Location: /myhouse.php');
  exit();
}

$shipid = (int)$_GET['idnum'];
$airship = get_airship_by_id($shipid);

if($airship === false || $airship['ownerid'] != $user['idnum'] || $airship['returntime'] > $now)
{
  header('Location: /myhouse/addon/airship_mooring.php');
  exit();
}

if($_POST['submit'] == 'Add Part')
{
  $itemid = (int)$_POST['item'];

  $item = get_inventory_byid($itemid);

  if($item['user'] == $user['user'])
  {
    if(array_key_exists($item['itemname'], $parts))
    {
      $details = get_item_byname($item['itemname']);
      $effects = $parts[$item['itemname']];
      
      if($item['health'] < $details['durability'])
        $errors[] = '<span class="failure">Only undamaged items may be added to an Airship.</span>';
      else if($details['bulk'] > $airship['maxbulk'] - $airship['bulk'])
        $errors[] = '<span class="failure">There is not enough Space on the airship for this part.</span>';
      else if($airship['power'] + $effects['power'] < 0)
        $errors[] = '<span class="failure">There is not enough Power to support this part.  Try adding a motor or engine first.</span>';
      else if($airship['mana'] + $effects['mana'] < 0)
        $errors[] = '<span class="failure">There is not enough Mana to support this part.  Try adding a Mana-generator first.</span>';
      else
      {
        $airship['weight'] += $details['weight'];
        $airship['bulk'] += $details['bulk'];

        $sets = array(
          'weight=weight+' . $details['weight'],
          'bulk=bulk+' . $details['bulk']
        );

        foreach($effects as $stat=>$value)
        {
          $airship[$stat] += $value;
          $sets[] = '`' . $stat . '`=`' . $stat . '`+' . $value;
        }
  
        $ship_parts = take_apart(',', $airship['parts']);
        $ship_parts[] = $item['itemname'];
        
        $sets[] = 'parts=' . quote_smart(implode(',', $ship_parts));
  
        delete_inventory_byid($itemid);
        
        $command = 'UPDATE psypets_airships SET ' . implode(', ', $sets) . ' WHERE idnum=' . $shipid . ' LIMIT 1';
        fetch_none($command, 'updating airship');
        
        $errors[] = '<span class="success">The part has been added!</span>';
      }
    }
    else
      $errors[] = '<span class="failure">The selected item doesn\'t make a very good airship part...</span>';
  }
  else
    $errors[] = '<span class="failure">The selected item doesn\'t exist, or you didn\'t select an item at all.  One of thems.</span>';
}

foreach($parts as $item=>$bonuses)
  $allowed_items[] = quote_smart($item);

$command = 'SELECT a.idnum,COUNT(a.idnum) AS c,a.itemname FROM monster_inventory AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname WHERE a.user=' . quote_smart($user['user']) . ' AND a.location LIKE \'home%\' AND a.location NOT LIKE \'home/$%\' AND a.itemname IN (' . implode(', ', $allowed_items) . ') AND a.health=b.durability GROUP BY a.itemname';
$items = fetch_multiple($command, 'fetching allowed chassis');

$bonuses = airship_crew_linear_bonus($airship, $user['user']);

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Airship Mooring &gt; <?= $airship['name'] ?> &gt; Add Part</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/airship_mooring.php">Airship Mooring</a> &gt; <a href="/myhouse/addon/airship_mooring_edit.php?idnum=<?= $shipid ?>"><?= $airship['name'] ?></a> &gt; Add Part</h4>
<?php
room_display($house);
?>
     <ul class="tabbed">
      <li class="activetab"><a href="/myhouse/addon/airship_mooring_edit.php?idnum=<?= $shipid ?>">Parts</a></li>
      <li><a href="/myhouse/addon/airship_mooring_crew.php?idnum=<?= $shipid ?>">Crew</a></li>
     </ul>
<?php
if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
?>
     <h5>Summary</h5>
     <p>Space remaining: <?= ($airship['maxbulk'] - $airship['bulk']) / 10 ?></p>
     <table>
      <tr><th>Seats</th><td><?= $airship['seats'] ?></td></tr>
      <tr><th>Weight</th><td><?= ($airship['weight'] / 10) ?></td></tr>
     </table>
     <h5>Available Parts</h5>
<?php
if(count($items) == 0)
  echo '<p>None of the items in your house make good airship parts.  (Only non-protected rooms of your house are checked.)</p>';
else
{
?>
     <p>The parts listed below are from non-protected rooms of your house.  The quantity shown is the number of undamaged items available (damaged items may not be added to an airship; repair them first), not the number you will add to the airship.  You will always only add one part at a time.</p>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Part</th><th>Qty</th><th>Weight</th><th>Bulk</th><th>Details</th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($items as $item)
  {
    $details = get_item_byname($item['itemname']);
    $effects = $parts[$item['itemname']];
    
    $space = true;
    
    if($details['bulk'] > $airship['maxbulk'] - $airship['bulk'])
      $space = false;
?>
      <tr class="<?= $rowclass ?>">
       <td><?= ($space && $power && $mana) ? '<input type="radio" name="item" value="' . $item['idnum'] . '" />' : '<input type="radio" disabled />' ?></td>
       <td class="centered"><?= item_display($details, '') ?></td>
       <td><?= $item['itemname'] ?></td>
       <td class="centered"><?= $item['c'] ?></td>
       <td class="centered"><?= ($details['weight'] / 10) ?></td>
       <td class="centered<?= $space ? '' : ' failure' ?>"><?= ($details['bulk'] / 10) ?></td>
       <td><ul class="plainlist"><?= render_airship_bonuses_as_list_xhtml($effects) ?></ul></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <p><input type="submit" name="submit" value="Add Part" /></p>
     </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
