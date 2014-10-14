<?php
$wiki = 'My_Favor_Store';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/questlib.php';

if($user['license'] == 'no')
{
  header('Location: ./storage.php');
  exit();
}

$my_favor_store_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: my favor store');
if($my_favor_store_tutorial_quest === false)
  $no_tip = true;

$command = 'SELECT a.idnum,a.markup,b.graphic,b.graphictype,b.itemname FROM psypets_custom_item_store AS a LEFT JOIN monster_items AS b ON a.itemid=b.idnum WHERE a.ownerid=' . $user['idnum'] . ' ORDER BY b.itemname ASC';
$for_sale = $database->FetchMultiple($command, 'fetching customs for sale');

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; <?= $user['display'] ?>'s Custom Item Store</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';

if($my_favor_store_tutorial_quest === false)
  include 'commons/tutorial/myfavorstore.php';
?>
     <h4><?= $user['display'] ?>'s Custom Item Store</h4>
     <ul class="tabbed">
      <li><a href="storage.php">Storage</a></li>
      <li><a href="storage_locked.php">Locked Storage</a></li>
      <li><a href="incoming.php">Incoming</a></li>
      <li><a href="mystore.php">My Store</a></li>
      <li class="activetab"><a href="myfavorstore.php">My Custom Item Store</a></li>
      <li><a href="outgoing.php">Outgoing</a></li>
     </ul>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
<?php
if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';
?>
     <ul><li><a href="myfavorstore_add.php">Add an Item</a></li></ul>
<?php
if(count($for_sale) == 0)
  echo '<p>None of your custom items are currently listed for sale.</p>';
else
{
  echo '
    <table>
     <thead>
      <tr class="titlerow"><th></th><th></th><th>Item</th><th>Price (Favor)</th></tr>
     </thead>
     <tbody>
  ';

  $rowclass = begin_row_class();

  foreach($for_sale as $item)
  {
    echo '
      <tr class="' . $rowclass . '">
       <td><a href="myfavorstore_remove.php?id=' . $item['idnum'] . '"><b style="color:red;">X</b></a></td>
       <td class="centered">' . item_display($item) . '</td>
       <td>' . $item['itemname'] . '</td>
       <td class="righted">' . ($item['markup'] + 300) . '</td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
  }

  echo '
     </tbody>
    </table>
  ';
}
?>
     <h5 style="padding-top: 2em;">A Tip</h5>
     <p>
      The code for a link to your store is:<br />
      <span style="white-space:pre; font-family:monospace;">{link <?= $SETTINGS['protocol'] ?>://psypets.net/favorstore.php?resident=<?= link_safe($user['display']) ?>}</span>
     </p>
     <p>Use this link to advertise your store in the <a href="viewplaza.php?plaza=5">Commerce</a> section of the plaza, or in an <a href="broadcast.php">in-game ad</a>!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
