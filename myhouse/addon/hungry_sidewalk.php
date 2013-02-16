<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Hungry Sidewalk';
$THIS_ROOM = 'Hungry Sidewalk';

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
require_once 'commons/sidewalklib.php';

if(!addon_exists($house, 'Hungry Sidewalk'))
{
  header('Location: /myhouse.php');
  exit();
}

$my_sidewalk = get_sidewalk_by_user($user['idnum']);

if($my_sidewalk === false)
{
  create_sidewalk($user['idnum']);
  $my_sidewalk = get_sidewalk_by_user($user['idnum']);
}

if($my_sidewalk['pigeons'] == 0)
{
  $feeds = array_keys($sidewalk_feeds);

  $command = 'SELECT itemname,COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' AND itemname IN (\'' . implode('\', \'', $feeds) . '\') GROUP BY(itemname)';
  $items = fetch_multiple_by($command, 'itemname', 'fetching sidewalk feed from house');

  if(array_key_exists('harvest', $_GET))
  {
    $key = $_GET['harvest'];
    
    if(array_key_exists($key, $sidewalk_harvestables) && $my_sidewalk['progress_' . $key] >= $sidewalk_harvestables[$key][1])
    {
      $amount = harvest_sidewalk($my_sidewalk, $user, $key);
      
      if($key != 'moneys')
      {
        if($amount == 1)
          $message = '<p class="success">Success!  You\'ll find the item at home.</p>';
        else
          $message = '<p class="success">Success!  You\'ll find the ' . $amount . ' items at home.</p>';
      }
      else
      {
        $message = '<p class="success">Success!  You found ' . $amount . '<span class="money">m</span>!</p>';
        $user['money'] += $amount;
      }

      if($key == 'chalk' || $key == 'clay')
      {
        if(array_key_exists(ucfirst($key), $items))
          $items[ucfirst($key)]['c'] += $amount;
        else
          $items[ucfirst($key)] = array('itemname' => ucfirst($key), 'c' => $amount);
      }
    }
    else
      $message = '<p class="failure">That item cannot be harvested...</p>';
  }
  else if($_POST['action'] == 'Feed')
  {
    $itemname = urldecode($_POST['itemname']);
    $quantity = (int)$_POST['quantity'];
    
    if(array_key_exists($itemname, $sidewalk_feeds))
    {
      $quantity = delete_inventory_fromhome($user['user'], $itemname, $quantity);

      if($quantity > 0)
      {
        feed_sidewalk($my_sidewalk, $sidewalk_feeds[$itemname] * $quantity);

        if($items[$itemname]['c'] > $quantity)
          $items[$itemname]['c'] -= $quantity;
        else
          unset($items[$itemname]);

        if(mt_rand(1, 25) <= $quantity)
          add_sidewalk_pigeons($my_sidewalk);

        $message = '<p class="success">The Hungry Sidewalk devours it all in one bite!</p>';
      } // if we successfully consumed the item from home
      else
        $message = '<p class="failure">You do not have any at home.</p>';
    } // if we selected a valie item
    else
      $message = '<p class="failure">The Hungry Sidewalk does not eat those!</p>';
  }
}

if($my_sidewalk['pigeons'] > 0)
{
  $demand_details = get_item_byid($my_sidewalk['pigeons']);

  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' AND itemname=' . quote_smart($demand_details['itemname']);
  $data = fetch_single($command, 'fetching demand item from house');
  $demand_quantity = (int)$data['c'];

  if($_GET['action'] == 'acquiesce' && $demand_quantity > 0)
  {
    if(delete_inventory_fromhome($user['user'], $demand_details['itemname'], 1) > 0)
    {
      feed_sidewalk($my_sidewalk, $demand_details['value'] * 2);
      remove_sidewalk_pigeons($my_sidewalk);
      header('Location: /myhouse/addon/hungry_sidewalk.php?pigeons=success');
      exit();
    }
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Hungry Sidewalk</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Hungry Sidewalk</h4>
<?php
echo $message;

room_display($house);

if($_GET['pigeons'] == 'success')
  echo '<p><i>The Sidewalk Pigeons fly off, leaving several Pigeon Feathers behind (which your Hungry Sidewalk greedily devours...)</i></p>';

// normal...
if($my_sidewalk['pigeons'] == 0)
{
  if(count($items) > 0)
  {
    echo '<form method="post">' .
         '<table><thead><tr class="titlerow"><th></th><th></th><th>Item</th><th>Qty.</th></tr></thead><tbody>';
  
    $row_class = begin_row_class();
  
    foreach($items as $values)
    {
      $details = get_item_byname($values['itemname']);
    
      echo '<tr class="' . $row_class . '"><td><input type="radio" name="itemname" value="' . urlencode($values['itemname']) . '" /></td><td class="centered">' .
           item_display($details, '') .
           '</td><td>' . $values['itemname'] . '</td><td class="centered">' . $values['c'] . '</td></tr>';
    
      $row_class = alt_row_class($row_class);
    }

    echo '</tbody></table>' .
         '<p>Quantity: <input type="text" name="quantity" value="1" maxlenth="3" size="3" /> <input type="submit" name="action" value="Feed" /></p></form>';
  }
  else
    echo '<p>The Hungry Sidewalk is not interested in any of the items in your house!</p>';
}
// Sidewalk Pigeons!
else
{
  echo '<img src="/gfx/npcs/sidewalkpigeons.png" align="right" width="300" height="200" alt="(Sidewalk Pigeons!)" />';

  include 'commons/dialog_open.php';

  echo '<p>Coo, coo!  Coo!  Coo!</p><p><i>(This is our sidewalk now, bub!)</i></p><p>Coo, coo!  Coo-coo, coo, coo...</p><p><i>(Though we may be convinced to leave...)</i></p><p>... coo!  Coo, coo!  <strong>Coo, <a href="/encyclopedia2.php?i=' . $my_sidewalk['pigeons'] . '">coo-coo</a>!</strong>  Coocoocoocoo!</p><p><i>(... but not for less than <strong>one <a href="/encyclopedia2.php?i=' . $my_sidewalk['pigeons'] . '">' . $demand_details['itemname'] . '</a>!</strong>  Hahahaha!)</i></p>';

  include 'commons/dialog_close.php';

  echo '<ul>';

  if($demand_quantity > 0)
  {
    if($demand_quantity == 1)
      echo '<li><a href="/myhouse/addon/hungry_sidewalk.php?action=acquiesce">Give them your one and only ' . $demand_details['itemname'] . '...</a>';
    else
      echo '<li><a href="/myhouse/addon/hungry_sidewalk.php?action=acquiesce">Give them one of your ' . say_number($demand_quantity) . ' ' . $demand_details['itemname'] . '...</a>';
  }
  else
    echo '<li class="dim">You do not have a ' . $demand_details['itemname'] . ' at home.';
  
  echo '</li></ul>';
}

echo '<h5>Harvest</h5>';

if($my_sidewalk['pigeons'] > 0)
  echo '<p>The Sidewalk Pigeons won\'t let you close enough to harvest anything!</p>';

echo '<table><thead><tr class="titlerow"><th></th><th>Item</th><th>Progress</th><th></th></tr></thead><tbody>';

$row_class = begin_row_class();

foreach($sidewalk_harvestables as $key=>$details)
{
  echo '<tr class="' . $row_class . '"><td class="centered">';

  if($details['0'] != 'Some Moneys')
  {
    $item_details = get_item_byname($details[0]);
    echo item_display($item_details, '');
  }
  
  echo '</td><td>' . $details[0] . '</td><td>';

  if($my_sidewalk['progress_' . $key] > 0)
    echo '<div class="progressbar" onmouseover="Tip(\'' . floor($my_sidewalk['progress_' . $key] * 100 / $details[1]) . '%\');"><div style="height: 10px; width: ' . min(50, ceil($my_sidewalk['progress_' . $key] * 50 / $details[1])) . 'px;"></div></div>';
  else
    echo '<i class="dim">no progress</i>';

  echo '</td><td>';

  if($my_sidewalk['progress_' . $key] >= $details[1])
  {
    if($my_sidewalk['pigeons'] == 0)
      echo '<a href="/myhouse/addon/hungry_sidewalk.php?harvest=' . $key . '">Harvest</a>';
    else
      echo '<span class="dim">Harvest</span>';
  }

  echo '</td></tr>';

  $row_class = alt_row_class($row_class);
}

echo '</tbody></table>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
