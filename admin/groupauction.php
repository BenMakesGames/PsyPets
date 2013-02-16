<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/grouplib.php';
require_once 'commons/itemlib.php';

if($admin['manageaccounts'] != 'yes' && $admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$groupid = (int)$_GET['groupid'];

if($groupid > 0)
{
  $group = get_group_byid($groupid);
  $psypets = get_user_byuser($SETTINGS['site_ingame_mailer'], 'idnum');
  
  if($_GET['action'] == 'go')
  {
    $itemid = add_inventory($SETTINGS['site_ingame_mailer'], 'u:' . $psypets['idnum'], 'Group Ownership', 'Grants control of ' . $group['name'], 'outgoing');
    
    $command = 'UPDATE monster_inventory SET data=\'' . $groupid . '\' WHERE idnum=' . $itemid . ' LIMIT 1';
    $database->FetchNone(($command, 'assigning item data');
    
    $ldesc = '
      Using this item will give you control of the Group "' . $group['name'] . '".  The group organizer has not been active, or no longer exists, and there are no members of the group which are suited to take over.<br />
      <br />
      This item cannot be resold (or given to The Museum, of course!)
    ';
    
    $command = '
      INSERT INTO monster_auctions (`ownerid`, `itemid`, `itemname`, `ldesc`, `bidvalue`, `bidtime`)
      VALUES (' . $psypets['idnum'] . ', ' . $itemid . ', \'Group Ownership\', ' . quote_smart($ldesc) . ', 1, ' . (time() + (8 * 60 * 60)) . ')
    ';

    $database->FetchNone(($command, 'creating auction');
    
    $auctionid = $database->InsertID();
    
    header('Location: ./auctiondetails.php?auction=' . $auctionid);
    exit();
  }
}
else
  $group = false;

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Auction Group Ownership</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';
?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Auction Group Ownership</h4>
<?php
if($group === false)
{
?>
     <h5>Group Number</h5>
     <form action="/admin/groupauction.php" method="get">
     <p><input name="groupid" maxlength="3" size="3" /> <input type="submit" value="Search" /></p>
     </form>
<?php
}
else
{
?>
     <h5><?= $group['name'] ?></h5>
     <p>Create an auction for this group's ownership?</p>
     <ul><li><a href="/admin/groupauction.php?groupid=<?= $groupid ?>&amp;action=go">Oui!</a></li></ul>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
