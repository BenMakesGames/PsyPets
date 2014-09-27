<?php
$wiki = 'Favor_Store';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/favorlib.php';
require_once 'commons/messages.php';

if($user['license'] == 'no')
{
  header('Location: ./ltc.php');
  exit();
}

$resident = $_GET['resident'];

$owner = get_user_bydisplay($resident, 'idnum,display');

if($owner['idnum'] == $user['idnum'])
{
  header('Location: ./myfavorstore.php');
  exit();
}

if($owner === false)
{
  header('Location: ./favorstores.php');
  exit();
}

$command = 'SELECT a.idnum,a.markup,b.graphic,b.graphictype,b.itemname FROM psypets_custom_item_store AS a LEFT JOIN monster_items AS b ON a.itemid=b.idnum WHERE a.ownerid=' . $owner['idnum'] . ' ORDER BY b.itemname ASC';
$for_sale = $database->FetchMultipleBy($command, 'idnum', 'fetching customs for sale');

if($_POST['action'] == 'Buy')
{
  $saleid = (int)$_POST['item'];
  
  if(array_key_exists($saleid, $for_sale))
  {
    $item = $for_sale[$saleid];

    $itemid = add_inventory('psypets', 'u:' . $owner['idnum'], $item['itemname'], 'Bought from ' . $owner['display'] . '\'s Favor Store', 'storage/incoming');

    spend_favor($user, $item['markup'] + 300, 'bought item - ' . $item['itemname'] . ' - from ' . $owner['display'], $itemid);
    credit_favor($owner, $item['markup'], $user['display'] . ' purchased a copy of your ' . $item['itemname']);

    $command = 'UPDATE monster_inventory SET user=' . quote_smart($user['user']) . ' WHERE idnum=' . $itemid . ' LIMIT 1';
    $database->FetchNone($command, 'giving resident custom item');

    header('Location: ./favorstore.php?resident=' . link_safe($owner['display']) . '&msg=144:' . $item['itemname']);
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $owner['display'] ?>'s Custom Item Store</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';
?>
     <h4><?= $owner['display'] ?>'s Custom Item Store</h4>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if(count($for_sale) == 0)
  echo '<p>' . $owner['display'] . ' is not currently listing any items for sale.</p>';
else
{
  echo '
    <p>You currently have ' . $user['favor'] . ' Favor.  (<a href="buyfavors.php">Get more Favor</a>.)</p>
    <form action="favorstore.php?resident=' . link_safe($owner['display']) . '" method="post">
    <table>
     <thead>
      <tr class="titlerow"><th></th><th></th><th>Item</th><th>Price (Favor)</th></tr>
     </thead>
     <tbody>
  ';

  $rowclass = begin_row_class();

  foreach($for_sale as $item)
  {
    $price = $item['markup'] + 300;
    
    $disabled = ($price > $user['favor'] ? ' disabled="disabled"' : '');
  
    echo '
      <tr class="' . $rowclass . '">
       <td><input type="radio" name="item" value="' . $item['idnum'] . '"' . $disabled . ' /></td>
       <td class="centered">' . item_display($item) . '</td>
       <td>' . $item['itemname'] . '</td>
       <td class="righted">' . $price . '</td>
      </tr>
    ';

    $rowclass = alt_row_class($rowclass);
  }

  echo '
     </tbody>
    </table>
    <p><input type="submit" name="action" value="Buy" /></p>
    </form>
  ';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
