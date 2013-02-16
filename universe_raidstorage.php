<?php
$whereat = 'home';
$wiki = 'Multiverse';
$THIS_ROOM = 'Multiverse';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/universelib.php';
require_once 'commons/messages.php';

if($user['show_universe'] != 'yes')
{
  header('Location: ./myhouse.php');
  exit();
}

$universe = get_universe($user['idnum']);

if($universe === false || $universe['stage'] != 'gameplay')
{
  header('Location: ./myuniverse.php');
  exit();
}

$currencies = array(
  'rocks' => 'Rocks',
  'stars' => 'Stars',
  'hydrogen' => 'Hydrogen',
  'gasgiants' => 'Gas Giants',
  'supernova' => 'Supernova',
  'galaxies' => 'Galaxies',
);

$raidables = array(
  'Hydrogen' => array('hydrogen' => 1),
  'Small Rock' => array('rocks' => 1),
  'Large Rock' => array('rocks' => 2),
  'Really Enormously Tremendous Rock' => array('rocks' => 3),
  'Iron' => array('rocks' => 1),
  'Tin' => array('rocks' => 1),
  'Gold' => array('rocks' => 1),
  'Copper' => array('rocks' => 1),
  'Silver' => array('rocks' => 1),
  'Coal' => array('rocks' => 1),
  'Supernova' => array('supernova' => 1),
  'Shooting Star' => array('stars' => 1),
  'Evening Star' => array('stars' => 1),
  'Echeclus' => array('stars' => 1),
  'Mercury' => array('rocks' => 4),
  'Venus' => array('rocks' => 5),
  'Mars' => array('rocks' => 4),
  'Jupiter' => array('gasgiants' => 1),
  'Saturn' => array('gasgiants' => 1),
  'Uranus' => array('gasgiants' => 1),
  'Neptune' => array('gasgiants' => 1),
  'Pluto and Charon' => array('rocks' => 3),
  'Orion' => array('galaxies' => 1),
  'Pleiades' => array('stars' => 2, 'hydrogen' => 7),
  'Starry Kimono' => array('stars' => 1),
);

$inventory_command = '
  SELECT COUNT(a.idnum) AS qty,a.itemname,b.graphictype,b.graphic
  FROM monster_inventory AS a LEFT JOIN monster_items AS b
  ON a.itemname=b.itemname
  WHERE
    a.user=' . quote_smart($user['user']) . ' AND
    a.location=\'storage\' AND
    a.itemname IN (\'' . implode('\',\'', array_keys($raidables)) . '\')
  GROUP BY(a.itemname)
  ORDER BY a.itemname ASC
';
$inventory = $database->FetchMultipleBy($inventory_command, 'itemname', 'fetching inventory');

if($_POST['action'] == 'Raid')
{
  require_once 'commons/statlib.php';

  $raided = array();

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 2) == 'q_')
    {
      $itemcode = substr($key, 2);
      $itemname = itemname_from_form_value($itemcode);
      $itemname = stripslashes($itemname);

      $quantity = (int)$value;

      if(array_key_exists($itemname, $inventory))
      {
        if($quantity > $inventory[$itemname]['qty'])
          $quantity = $inventory[$itemname]['qty'];

        if($quantity > 0)
        {
          $quantity = delete_inventory_byname($user['user'], $itemname, $quantity, 'storage');
          
          if($quantity > 0)
          {
            if($quantity == $inventory[$itemname]['qty'])
              unset($inventory[$itemname]);
            else
              $inventory[$itemname]['qty'] -= $quantity;

            foreach($raidables[$itemname] as $currency=>$amount)
              $raided[$currency] += $amount * $quantity;

            record_stat($user['idnum'], 'Items Raided for The Multiverse', $quantity);
          }
        }
      }
    }
  }
  
  if(count($raided) > 0)
  {
    $currency_list = array();
  
    foreach($raided as $currency=>$amount)
    {
      $updates[] = $currency . '=' . $currency . '+' . $amount;
      $currency_list[] = $currencies[$currency] . ' &times;' . $amount;
    }

    $message_list[] = '<span class="success">You raided ' . implode(', ', $currency_list) . '</span>';

    $command = '
      UPDATE psypets_universes
      SET ' . implode(', ', $updates) . '
      WHERE ownerid=' . $user['idnum'] . '
      LIMIT 1
    ';
    $database->FetchNone($command, 'updating universe currencies');
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Universe &gt; Raid Storage</title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/multiverse_1.css" />
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/multiverse.php">The Multiverse</a> &gt; Raid Storage</h4>
<p>Some items can be raided for Hydrogen, Rocks, Stars, and other materials for use inside <a href="myuniverse.php">your Universe</a>.</p>
<p>Only items in <a href="/storage.php">your Storage</a> are listed here.</p>
<?php
if(count($inventory) == 0)
  echo '<p>There are no raidable items in your Storage.</p>';
else
{
  echo '
    <form action="universe_raidstorage.php" method="post">
    <table>
     <tr class="titlerow"><th colspan="2" class="centered">Quantity</th><th></th><th>Item</th><th>Yields</th></tr>
  ';

  $rowclass = begin_row_class();
  
  foreach($inventory as $item)
  {
    $raids = array();
    foreach($raidables[$item['itemname']] as $currency=>$amount)
      $raids[] = $currencies[$currency] . ' &times;' . $amount;
  
    echo '
      <tr class="' . $rowclass . '">
       <td><input type="text" size="2" maxlength="' . strlen($item['qty']) . '" name="' . itemname_to_form_value('q_' . $item['itemname']) . '" /></td>
       <td>/ ' . $item['qty'] . '</td>
       <td class="centered">' . item_display_extra($item) . '</td>
       <td>' . $item['itemname'] . '</td>
       <td>' . implode('<br />', $raids) . '</td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
  }

  echo '
    </table>
    <p><input type="submit" name="action" value="Raid" /></p>
    </form>
  ';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
