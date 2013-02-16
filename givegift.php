<?php
$wiki = 'Giving_Tree';
$whereat = 'storage';
$require_petload = 'yes';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

$given = get_quest_value($user['idnum'], 'gift value');

$my_inventory = $database->FetchMultiple("SELECT a.* FROM monster_inventory AS a,monster_items AS b WHERE a.user=" . quote_smart($user["user"]) . " AND a.location='storage' AND a.itemname=b.itemname AND b.cursed='no' AND b.noexchange='no' ORDER BY a.itemname ASC");
$num_inventory_items = count($my_inventory);

if($_POST['action'] == 'Change This')
{
  $user['receive_giving_tree_gifts'] = ($user['receive_giving_tree_gifts'] == 'yes' ? 'no' : 'yes');
  $database->FetchNone('UPDATE monster_users SET receive_giving_tree_gifts=' . quote_smart($user['receive_giving_tree_gifts']) . ' WHERE idnum=' . (int)$user['idnum'] . ' LIMIT 1');
}

if($user['receive_giving_tree_gifts'] == 'yes')
  $giving_tree_preference = 'I <strong>love</strong> receiving stuff from the giving tree!';
else
  $giving_tree_preference = 'I <strong>don\'t want</strong> to receive stuff from the giving tree!';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Giving Tree</title>
<?php include "commons/head.php"; ?>
  <script type="text/javascript">
   function check_all()
   {
     if(document.giftlist.checkall.checked)
     {
       if(!confirm('Really?  Every single one?'))
       {
         document.giftlist.checkall.checked = false;
         return;
       }
     }

     i = document.giftlist.elements.length;
     for(j = 0; j < i - 1; ++j)
     {
       document.giftlist.elements[j].checked = document.giftlist.checkall.checked;
     }
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>Giving Tree</h4>
<?php
if(strlen($_GET["msg"]) > 0)
  $error_message = form_message(explode(',', $_GET["msg"]));

if($error_message)
  echo "<p>$error_message</p>";

echo '
  <div style="width:340px; float:right; clear:right; margin-left:1em; margin-bottom:1em; padding:5px; border: 1px solid #666;"><form method="post">
    <p>"' . $giving_tree_preference . '"</p>
    <p style="margin-left:20px;margin-bottom:2px;"><input type="submit" name="action" value="Change This" class="bigbutton" /></p>
  </form></div>
  <p>Select items you wish to donate to the Giving Tree.  They will be randomly distributed to other, active Residents.</p>
';

if((int)$given['value'] >= 20)
  echo '<p><i>(By the way, you\'ve given a total of ' . $given['value'] . '<span class="money">m</span> worth of items to the Giving Tree.)</i></p>';

if($num_inventory_items > 0)
{
  $SPECIAL_CHECKALL = true;
?>
     <div style="clear:both;"></div>
     <form action="givegift_a.php" method="post" name="giftlist" id="giftlist">
<?php display_inventory($whereat, $my_inventory, $user, $userpets); ?>
     <p><input type="submit" value="Give Gifts" /> <input type="checkbox" name="credit" value="yes" id="credit" style="margin-left:1em;" checked="checked" /> <label for="credit">Add my name to the items' comments</label></p>
     </form>
<?php
}
else
  echo '<p>(Only items in your Storage may be given; you currently have no items in your Storage.)</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
