<?php
$whereat = 'florist';
$wiki = 'The_Florist';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';

if($user['show_florist'] != 'yes')
{
  header('Location: /404');
  exit();
}

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

$v_day = (date('n') == 2 && date('j') >=1 && date('j') <= 14);

if($v_day)
  $shipping_cost = 1;
else
  $shipping_cost = 2;

$FOR_SALE = array(
  'Purple Lilac' => 4,
  'Arbutus' => 4,
  'Periwinkle' => 4,
  'Honeysuckle' => 4,
  'Yellow Acacia' => 4,
  'Primrose' => 4,
  'White Lily' => 4,
  'Scabious' => 4,
  'Narcissus' => 4,
);

if(date('n') == 12)
  $FOR_SALE['Poinsettia'] = 50;

$holiday_message_start = 14;

$florist_prefab = array(
  '*hugs*',
  '<3',
  'D:',
  'Hello :)',
  'I love you',
  'I miss you ;-;',
  'I see dead people...!',
  'Polar bear.',
  'Thank you!',
  'The florist won\'t deliver my panties.',
  'The flowers speak the truth...',
  'What inspired this amorous rhyme?  Two parts vodka, one part lime.',
  'Would you be shocked if I put on something more comfortable?',

  'Happy Birthday!',
  'A very Merry Unbirthday to you!',

  'Happy New Year!',         // january 1st
  'Happy Chinese New Year!', // variable - chinese new year
  '恭喜發財！',              // variable - chinese new year
  'Happy Valentine\'s Day!', // february 14th
  '3.14159265358979323846...', // pi day, 3/14
  'Happy Saint Patrick\'s Day!', // march somethin'
  'April Fools!',            // april 1st
  'Love you, Mom!',          // may 11th
  'Love you, Dad!',          // june-ish >_>
  'Happy Canada Day!',       // july 1st
  'Avast, me hearty!',       // september 19th
  'Happy Halloween!',        // october 31st
  'Have a bright Diwali!',   // october/november (varies)
  'Happy Thanksgiving!',     // november ?
  'Turkey is delicious! :)', // november ?
  'Happy holidays!',         // december *
  'Shabat shalom!',          // december ?
  'Merry Christmas!',        // december 25th
  'Yuletide blessings!',
);

if($_POST['action'] == 'send')
{
  $flower = str_replace('_', ' ', $_POST['flower']);
  $recipient = get_user_bydisplay($_POST['sendto']);

  if((int)$_POST['message_select'] == -1)
    $my_message = trim($_POST['message']);
  else
    $my_message = $florist_prefab[$_POST['message_select']];

  $real_shipping_cost = $shipping_cost + $FOR_SALE[$flower]; 

  if(!array_key_exists($flower, $FOR_SALE))
    $error_message = '<span class="failure">Which flower?</span>';
  else if($user['money'] < $real_shipping_cost)
    $error_message = '<span class="failure">Sorry, but you don\'t have enough money.</span>';
  else if($recipient === false)
    $error_message = '<span class="failure">I can\'t seem to find a resident by that name.  Are you sure you spelled it correctly?</span>';
  else if($recipient['user'] == $user['user'] && $flower !== 'Narcissus')
    $error_message = '<span class="failure">May I suggest the Narcissus instead?</span>';
  else if(is_enemy($recipient, $user) || is_enemy($user, $recipient))
    $error_message = '<span class="failure">Sorry, I\'m not allowed to send flowers to that Resident.</span>';
  else if($recipient['flower_receipt'] == 'no' && (int)$_POST['message_select'] == -1)
    $error_message = '<span class="failure">That Resident is only accepting messages from my personal list of messages, and not custom messages.  Sorry.</span>';
  else
  {
    take_money($user, $real_shipping_cost, 'Purchase from The Florist');

    $id = add_inventory($recipient['user'], '', $flower, '', 'storage/incoming');

    $command = 'UPDATE monster_inventory SET message2=' . quote_smart($my_message) . ' WHERE idnum=' . $id . ' LIMIT 1';
    $database->FetchNone($command, 'customizing flower message');

    flag_new_incoming_items($recipient['user']);

    $petname = random_name(mt_rand(0, 1) == 0 ? 'male' : 'female');

    if($recipient['user'] == $user['user'] && $flower == 'Narcissus')
      $message = '<span class="success">Okay, then... I\'ll have ' . $petname . ' deliver it immediately...</span>';
    else
    {
      $message = '<span class="success">Great!  I\'ll have ' . $petname . ' deliver it immediately!</span>';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Anonymous Flowers Sent', 1);
    }

    $_POST['sendto'] = '';
    $my_message = '';
    $flower = '';
    
    $user['money'] -= $real_shipping_cost;
  }
}

if($_GET['custom'] == 'disable')
{
  $user['flower_receipt'] = 'no';
  $command = 'UPDATE monster_users SET flower_receipt=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'disabling custom flower sends');
  $message = '<span class="success">Alright, I understand.  I won\'t let people send you custom messages anymore.  People <em>will</em> still be able to send you flowers with any of my pre-made messages, remember.</span>';
}
else if($_GET['custom'] == 'enable')
{
  $user['flower_receipt'] = 'yes';
  $command = 'UPDATE monster_users SET flower_receipt=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'enabling custom flower sends');
  $message = '<span class="success">Cool.  I\'ll let people send you custom messages again.</span>';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Florist &gt; Flower Delivery</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
    function choose_message()
    {
      document.getElementById('messageselect').style.display = 'block';
      document.getElementById('messageselect').style.top = document.getElementById('custom_message').style.top;
      document.getElementById('messageselect').style.left = ((document.body.offsetWidth - document.getElementById('messageselect').style.width) / 2 - 100) + 'px';
      document.getElementById('messageselect').style.zIndex = 255; 
    }
    
    function select_message(id, text)
    {
      document.getElementById('message_select').value = id;
      document.getElementById('custom_message').value = text;
      document.getElementById('custom_message2').value = text;
      document.getElementById('messageselect').style.display = 'none';
    }

    $(function() {
      $('#buddylist').change(function() {
        $('#recipient').val($('#buddylist').val());
      });
    });
  </script>
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? '<p style="color:blue;">' . $check_message . '</p>' : '') ?>
     <h4>The Florist &gt; Flower Delivery</h4>
     <ul class="tabbed">
      <li><a href="florist.php">Flower Shop</a></li>
      <li class="activetab"><a href="florist_anonymous.php">Flower Delivery</a></li>
      <li><a href="florist_exchange.php">Exchanges</a></li>
      <li><a href="giftwrapping.php">Gift-wrapping</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// VANESSA ROSELLE
echo '<a href="/npcprofile.php?npc=Vanessa+Roselle"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/flowergirl.jpg" align="right" width="350" height="706" alt="(Vanessa the Florist)" /></a>';

include 'commons/dialog_open.php';
if($error_message)
  echo "<p>$error_message</p>";
else
{
  if(strlen($message) > 0)
    echo $message;
  else
  {
?>
<p>I have a couple pets that can deliver a flower to anyone you like anonymously!  You can even leave a custom message to go with it!</p>
<p>Each flower sent this way will cost <?= $shipping_cost + 4 ?> moneys.</p>
<?php
  }
}
include 'commons/dialog_close.php';

if($user['flower_receipt'] == 'yes')
  echo '<ul><li><a href="florist_anonymous.php?custom=disable">Ask her to NOT let people send you custom flower messages</a></li></ul>';
else
  echo '<ul><li><a href="florist_anonymous.php?custom=enable">Tell her it\'s OK for people send you custom flower messages</a></li></ul>';

?>
<div class="shadowed-box" id="messageselect" style="display: none; position: absolute;"><div style="background-color: #eee; position: relative; bottom: 6px; right: 6px; border: 1px solid #000;">
<table class="nomargin">
 <tr>
  <td valign="top">
   <p><b>General Messages</b></p>
<ul class="plainlist">
<?php
foreach($florist_prefab as $id=>$message)
{
  if($id == $holiday_message_start)
    echo '</ul></td><td valign="top"><p><b>Holiday Messages</b></p><ul class="plainlist">';

  echo '<li><a href="#" onclick="select_message(' . $id . ', ' . quote_smart($message) . '); return false;">' . $message . '</a></li>';
}
?>
</ul>
  </td>
 </tr>
 <tr>
  <td colspan="2">
   <p><b>Custom Message</b></p>
   <p><input name="flower_text" id="flower_text" style="width:300px;" /> <button onclick="select_message(-1, document.getElementById('flower_text').value);">Use This Message</button></p>
  </td>
 </tr>
</table>
</div></div>
<form action="<?= $_SERVER['SCRIPT_NAME'] ?>" method="post">
<table>
 <tr>
  <th><label for="recipient">To:</label></th>
  <td>
   <input id="recipient" name="sendto" value="<?= $_POST['sendto'] ?>" style="width:150px;">
   <span class="size13">&larr;</span>
   <select id="buddylist" style="width:200px;">
    <option value=""></option>
<?php
$friends = $database->FetchMultiple('
  SELECT b.display
  FROM psypets_user_friends AS a
    LEFT JOIN monster_users AS b
      ON a.friendid=b.idnum
  WHERE a.userid=' . (int)$user['idnum'] . '
  ORDER BY b.display ASC
');

foreach($friends as $friend)
  echo '<option value="' . $friend['display'] . '">' . $friend['display'] . '</option>';
?>
   </select>
  </td>
 </tr>
 <tr>
  <th valign="top">Message:</th>
  <td>
   <input type="hidden" name="message_select" id="message_select" value="-1" />
   <input type="hidden" id="custom_message2" name="message" value="<?= str_replace('"', '&quot;', $my_message) ?>" />
   <input id="custom_message" style="width:200px;" value="<?= str_replace('"', '&quot;', $my_message) ?>" disabled="disabled" onclick="choose_message(); return false;" />
   <a href="#" onclick="choose_message(); return false;">Choose Message</a>
  </td>
 </tr>
 <tr>
  <td colspan="2">
   <p><b>Flower:</b></p>
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Flower</th>
  <th></th>
 </tr>
<?php
$rowclass = begin_row_class();

foreach($FOR_SALE as $itemname=>$price)
{
  $itemdetails = get_item_byname($itemname);
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="radio" name="flower" value="<?= str_replace(' ', '_', $itemname) ?>"<?= ($itemname == $flower ? ' checked ' : '') ?> /></td>
  <td class="centered"><?= item_display($itemdetails, '') ?></td>
  <td><?= $itemname ?></td>
  <td><?= ($itemname == 'Poinsettia' ? '(costs 52<span class="money">m</span> to send)' : '') ?></td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
  </td>
 </tr>
 <tr>
  <td>&nbsp;</td><td><input type="hidden" name="action" value="send" /><input type="submit" value="Deliver" /></td>
 </tr>
</table>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
