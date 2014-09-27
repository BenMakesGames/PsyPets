<?php
require_once 'commons/init.php';

$whereat = "home";
$wiki = "Fireplace";
$THIS_ROOM = 'Fireplace';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/fireplacelib.php';
require_once 'commons/utility.php';

if(!addon_exists($house, 'Fireplace'))
{
  header('Location: /myhouse.php');
  exit();
}

$first_visit = false;

$fireplace = get_fireplace_byuser($user['idnum'], $user['locid']);
if($fireplace === false)
{
  create_fireplace($user["idnum"], $user["locid"]);
  $fireplace = get_fireplace_byuser($user["idnum"], $user["locid"]);
  if($fireplace === false)
  {
    echo "Failed to load your fireplace.  Try reloading this page; if the problem persists, contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
    exit();
  }

  $first_visit = true;
}

$data = $database->FetchSingle('SELECT COUNT(*) AS qty FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'fireplace\'');

$mantle_items = $data['qty'];

if($mantle_items > 9)
{
  header('Location: /myhouse/addon/fireplace.php');
  exit();
}

if($_POST['action'] == 'additem')
{
  $idnum = (int)$_POST['itemid'];
  $inventory = get_inventory_byid($idnum);

  if($inventory['user'] == $user['user'])
  {
    $item = get_item_byname($inventory['itemname']);

    if($item['cursed'] == 'no' && $item['custom'] != 'secret')
    {
      $database->FetchNone('UPDATE monster_inventory SET location=\'fireplace\' WHERE idnum=' . $idnum . ' LIMIT 1');

      $mantle_items++;

      if($mantle_items > 9)
      {
        header('Location: /myhouse/addon/fireplace_mantle.php');
        exit();
      }
      else
        $message = '<p><span class="success">' . $inventory['itemname'] . ' has been added to the mantle.</span></p>';
    }
    else
      $message = '<p><span class="failure">That item cannot be placed on the mantle.</span></p>';
  }
  else
    $message = '<p><span class="failure">Couldn\'t find that item.  Maybe a pet used it up just now.</span></p>';

  $step = 1;
}
else if($_POST['action'] == 'search')
{
  $item_search = trim($_POST['itemname']);
  if(strlen($item_search) > 2)
  {
    $command = 'SELECT * FROM monster_inventory WHERE itemname LIKE ' . quote_smart('%' . $item_search . '%') . ' AND user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' ORDER BY location ASC,itemname ASC';
    $items = fetch_multiple($command, 'searching for items in your house by name');

    if(count($items) == 0)
    {
      $step = 1;
      $message = '<p><span class="progress">No matching items were found.  Make sure the item really is in your house and that you are spelling it correctly.  You can also try typing a smaller part of the name (for example, instead of "invisibility potion", try "potion" or "invis").</span></p>';
    }
    else
      $step = 2;
  }
  else if(strlen($item_search) > 0)
  {
    $command = 'SELECT * FROM monster_inventory WHERE itemname=' . quote_smart($item_search) . ' AND user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' ORDER BY location ASC,itemname ASC';
    $items = fetch_multiple($command, 'searching for items in your house by EXACT name');

    if(count($items) == 0)
    {
      $step = 1;
      $message = '<p><span class="failure">No items by that exact name were found.  Please type at least 3 characters if you\'d like to make a broader search.</span></p>';
    }
    else
      $step = 2;
  }
  else
  {
    $step = 1;
    $message = '<p><span class="failure">Please enter <em>something</em> to search from.</span></p>';
  }
}
else
  $step = 1;

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Fireplace &gt; Mantle</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Fireplace &gt; Mantle</h4>
<?php
echo $check_message;
echo $message;
room_display($house);
?>
<ul class="tabbed">
 <li><a href="/myhouse/addon/fireplace.php">Fire</a></li>
 <li class="activetab"><a href="/myhouse/addon/fireplace_mantle.php">Mantle</a></li>
</ul>
<?php
  if($step == 1)
  {
?>
     <form method="post">
     <p>Search your house for an item to add by entering all or part of the item's name.  Your protected rooms will be included in the search.</p>
     <p><input name="itemname" maxlength="64" size="48" value="<?= $_POST['itemname'] ?>" /> <input type="hidden" name="action" value="search" /><input type="submit" value="Search" /></p>
     </form>
<?php
  }
  else if($step == 2)
  {
?>
     <form method="post">
     <p>Search your house for an item to add by entering all or part of the item's name.  Your protected rooms will be included in the search.</p>
     <p><input name="itemname" maxlength="64" size="48" value="<?= $_POST['itemname'] ?>" /> <input type="hidden" name="action" value="search" /><input type="submit" value="Search" /></p>
     </form>
     <h5>Search Results</h5>
     <form method="post">
     <p>Select the item to add from the results below.</p>
     <p><input type="submit" value="Add to Mantle" class="bigbutton" /></p>
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Item</th>
       <th>Location</th>
       <th>Comment</th>
      </tr>
<?php
    $rowclass = begin_row_class();

    foreach($items as $item)
    {
      $details = get_item_byname($item['itemname']);
      $location = explode('/', $item['location']);
      if($location[1] == '')
        $location[1] = 'common';
      if($location[1]{0} == '$')
        $location[1] = substr($location[1], 1);
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="radio" name="itemid" value="<?= $item['idnum'] ?>" /></td>
       <td class="centered"><?= item_display_extra($details, '', false) ?></td>
       <td><?= $item['itemname'] ?></td>
       <td><?= ucfirst($location[1]) ?></td>
       <td><?= $item['message'] . '<br />' . $item['message2'] ?></td>
      </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
     </table>
     <p><input type="hidden" name="action" value="additem" /><input type="submit" value="Add to Mantle" class="bigbutton" />
     </form>
<?php
  }
?>
<?php include "commons/footer_2.php"; ?>
 </body>
</html>
