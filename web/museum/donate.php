<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/museumlib.php';
require_once 'commons/itemlib.php';

if($_POST['action'] == 'donate')
{
  $items_added = 0;

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 2) == 'i_' && ($value == 'yes' || $value == 'on'))
    {
      $itemid = (int)substr($key, 2);
      
      $museum_item = get_museum_item($user['idnum'], $itemid);
      if($museum_item === false)
      {
        $details = get_item_byid($itemid);

        // only standard-availability items should be donateable
        if($details['custom'] == 'no')
        {
          if(delete_inventory_byname($user['user'], $details['itemname'], 1, 'storage') > 0)
          {
            add_item_to_museum($itemid, $user['idnum'], false);
            $items_added++;
          }
        }
      }
    }
  }
  
  $dialog_text = '<p>Wonderful!  That\'s ' . $items_added . ' more item' . ($items_added == 1 ? '' : 's') . ' cataloged!</p>';

  update_museum_count($user['idnum']);
}
else
  $dialog_text = '<p>Oh?  There\'s something you\'d like to donate to our museum?  Excellent!  <em>Most</em> excellent!</p>';

$command = 'SELECT a.itemname,b.graphic,b.graphictype,b.idnum,COUNT(a.itemname) AS c FROM monster_inventory AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname WHERE b.custom=\'no\' AND a.user=' . quote_smart($user['user']) . ' AND a.location=\'storage\' GROUP BY (a.itemname) ORDER BY a.itemname ASC';
$storage = fetch_multiple($command, 'fetching item counts from storage');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Museum</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Museum</h4>
     <ul class="tabbed">
      <li><a href="/museum/">My Collection</a></li>
      <li><a href="/museum/uncollection.php">My Uncollection</a></li>
      <li class="activetab"><a href="/museum/donate.php">Make Donation</a></li>
      <li><a href="/museum/exchange.php">Exchanges</a></li>
      <li><a href="/museum/displayeditor.php">My Displays</a></li>
      <li><a href="/museum/wings.php">Wing Directory</a></li>
     </ul>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/museum.png" align="right" width="350" height="500" alt="(Museum Curator)" />';

include 'commons/dialog_open.php';

echo $dialog_text;

include 'commons/dialog_close.php';

$items_to_donate = array();

foreach($storage as $item)
{
  $museum_item = get_museum_item($user['idnum'], $item['idnum']);
  if($museum_item === false)
    $items_to_donate[] = $item;
}

echo '<p><i>(Custom, monthly, limited, and cross-game items may not be given to The Museum, and are not listed here.)</i></p>'; 

if(count($storage) == 0)
  echo '<p>You have no (donateable) items in your Storage.</p>';
else if(count($items_to_donate) == 0)
  echo '<p>None of the items in your Storage have not already been donated to The Museum.</p>';
else
{
  echo '<p>Check off the items from Storage which you would like to donate.  Note that the quantity shown is the number you have in Storage, not the number you will give.  You will always give only 1 of each item selected.</p>' .
       '<form action="/museum/donate.php" method="post" id="form1">' .
       '<table>' .
       '<tr class="titlerow"><th><input type="checkbox" class="checkall" id="check1" style="display:none;" /></th><th></th><th>Item</th><th>Qty.</th><th></th></tr>';

  $rowclass = begin_row_class();

  foreach($items_to_donate as $item)
  {
    echo '<tr class="' . $rowclass . '">' .
         '<td><input type="checkbox" name="i_' . $item['idnum'] . '" /></td><td class="centered">' . item_display($item, '') . '</td><td>' . $item['itemname'] . '</td><td class="centered">' . $item['c'] . '</td><td></td>' .
         '</tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>' .
       '<p><input type="hidden" name="action" value="donate" /><input type="submit" value="Donate" /></p>' .
       '</form>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
