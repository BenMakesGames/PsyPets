<?php
$whereat = "graveyard";
$wiki = "The_Graveyard";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/checkpet.php';
require_once 'commons/userlib.php';
require_once 'commons/gravelib.php';

$tombid = (int)$_GET['id'];

$tombstone = get_tombstone_byid($tombid);

if($tombstone['tombstone'] > 0)
{
  header('Location: ./graveyard.php');
  exit();
}

$command = 'SELECT a.itemname,a.idnum,b.graphic,b.graphictype FROM monster_inventory AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname WHERE b.itemtype=\'craft/tombstone\' AND a.user=' . quote_smart($user['user']) . ' AND a.location=\'storage\''; 
$tombs = $database->FetchMultipleBy($command, 'idnum', 'fetching tombs from storage');

if($_POST['submit'] == 'Sanctify!')
{
  $itemid = (int)$_POST['item'];

  if(array_key_exists($itemid, $tombs))
  {
    $tombvalue = (int)substr($tombs[$itemid]['graphic'], 10, 2);

    delete_inventory_byid($itemid);

    $command = 'UPDATE psypets_graveyard SET tombstone=' . $tombvalue . ' WHERE idnum=' . $tombid . ' LIMIT 1';
    $database->FetchNone($command, 'sanctifying grave');

    $reward = mt_rand(1, 3);

    if($reward == 1)
    {
      $command = 'UPDATE monster_users SET rupees=rupees+1 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'granting 1 rupee');
      
      $msg = 121;
    }
    else if($reward == 2)
    {
      require_once 'commons/challengelib.php';
      
      $challenge = get_challenge($user['idnum']);
      if($challenge === false)
      {
        create_challenge($user['idnum']);
        $challenge = get_challenge($user['idnum']);
        if($challenge === false)
          die('error loading daily challenge information.  this is bad.');
      }
      
      $challenge['copper']++;     
      update_challenge($challenge);

      $msg = 122;
    }
    else if($reward == 3)
    {
      $command = 'UPDATE monster_users SET stickers_to_give=stickers_to_give+2 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'granting 2 gold star stickers');
      
      $msg = 123;
    }
    
    header('Location: ./graveyard.php?plot=' . (int)$_GET['plot'] . '&msg=' . $msg);
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Graveyard &gt; Sanctify Empty Grave</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="graveyard.php?plot=<?= (int)$_GET['plot'] ?>">The Graveyard</a> &gt; Sanctify Empty Grave</h4>
<?php
if(count($tombs) > 0)
{
  echo '<p>Choose a tombstone to put on this grave...</p>' .
       '<form action="fillgrave.php?plot=' . (int)$_GET['plot'] . '&id=' . $tombid . '" method="post">' .
       '<table>' .
       '<tr class="titlerow"><th></th><th></th><th>Tombstone</th></tr>';

  $rowclass = begin_row_class();

  foreach($tombs as $idnum=>$tomb)
  {
    echo '<tr class="' . $rowclass . '"><td><input type="radio" name="item" value="' . $idnum . '" /></td><td>' . item_display($tomb, '') . '</td><td>' . $tomb['itemname'] . '</td></tr>';
    $rowclass = alt_row_class($rowclass);
  }
  
  echo '</table>' .
       '<p><input type="submit" name="submit" value="Sanctify!" /></p>' .
       '</form>';
}
else
  echo '<p>You do not have any tomb stones in your storage.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
