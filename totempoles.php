<?php
$wiki = 'Totem_Pole_Garden';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/totemlib.php';

if($user['show_totemgardern'] == 'no')
{
  header('Location: /myhouse.php');
  exit();
}

$st_patricks = (date('M d') == 'Mar 17');

$blocks = array();

if(array_key_exists('resident', $_GET))
{
  $totem_user = get_user_bydisplay($_GET['resident']);

  if($totem_user !== false)
  {
    if($totem_user['idnum'] == $user['idnum'])
      $owners = 'My';
    else
      $owners = $totem_user['display'] . "'s";

    $totem = get_totem_byuserid($totem_user['idnum']);

    if($totem === false)
      $message = $_GET['resident'] . ' does not have a totem pole.';
  }
  else
  {
    $totem = false;
    $message = 'There is no resident by the name "' . $_GET['resident'] . '".';
  }
}
else
{
  $totem = get_totem_byuserid($user['idnum']);
  $owners = 'My';
  if($totem === false)
    $message = 'You do not have a totem pole.';
}
/*
if($user['display'] == 'Anju')
  $totem['remove_cost'] = 0;
*/
if($totem !== false)
{
  $blocks = take_apart(',', $totem['totem']);
  $blocks = array_reverse($blocks);

  if($owners == 'My' && $_GET['action'] == 'remove' && count($blocks) > 0)
  {
    $return_top_totem = false;

    if($totem['last_add'] > $now - (30 * 60))
      $return_top_totem = true;
    else if($_GET['free'] == 'yes')
      $message = 'Sorry, it\'s been too long to let you remove that top totem for free.  However for ' . ($totem['remove_cost'] * 100) . '<span class="money">m</span> it could be done.';
    else
    {
      if($user['money'] >= $totem['remove_cost'] * 100)
      {
        take_money($user, $totem['remove_cost'] * 100, 'Totem Garden fee for recovering top totem');
        $user['money'] -= $totem['remove_cost'] * 100;
        $return_top_totem = true;

        $command = 'UPDATE psypets_totempoles SET remove_cost=remove_cost*2 WHERE userid=' . $user['idnum'] . ' LIMIT 1';
        $database->FetchNone($command, 'totempoles.php');
        $totem['remove_cost'] *= 2;
      }
      else
        $message = 'Sorry, you don\'t seem to have enough money to afford the service.';
    }

    if($return_top_totem)
    {
      $top_totem = $blocks[0];

      $command = 'SELECT itemname FROM monster_items WHERE itemtype=\'craft/sculpture/totem\' AND graphic=\'totem_x' . $top_totem . '.png\' LIMIT 1';
      $item = $database->FetchSingle($command, 'totempoles.php');

      if($item === false)
      {
        $message = 'Hm, odd... I actually don\'t recognize your top-most totem.</p><p>You should let ' . $SETTINGS['author_resident_name'] . ' know about this.  It\'s probably a bug he should fix...';
        $return_top_totem = false;
      }
    }

    if($return_top_totem)
    {
      unset($blocks[0]);

      $totem['last_add'] = 0;

      if(count($blocks) > 0)
      {
        $totem['rating'] = totem_score($blocks);
        $real_blocks = array_reverse($blocks);
        replace_totem_byuserid($user['idnum'], $real_blocks);
        $message = 'Alright.  You\'ll find the ' . $item['itemname'] . ' in your ' . $user['incomingto'] . '.  If you want another totem removed, however, it will cost you ' . ($totem['remove_cost'] * 100) . '<span class="money">m</span>.';
      }
      else
      {
        replace_totem_byuserid($user['idnum'], array());
        $message = 'Alright.  You\'ll find the ' . $item['itemname'] . ' in your ' . $user['incomingto'] . '.  If you want another totem removed, however, it will cost you ' . ($totem['remove_cost'] * 100) . '<span class="money">m</span>.';
        $totem = false;
      }

      add_inventory($user['user'], '', $item['itemname'], 'Recovered from ' . $user['display'] . '\'s totem pole', $user['incomingto']);

      require_once 'commons/statlib.php';

      record_stat($user['idnum'], 'Totems Removed from Totem Poles', 1);
    }
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Totem Pole Garden &gt; Browse &gt; <?= $owners ?> Totem Pole</title>
<?php include "commons/head.php"; ?>
  <style type="text/css">
   #totempole img
   {
     border: 0;
     margin: 0 auto;
     padding: 0;
     display: block;
   }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="totemgarden.php">The Totem Pole Garden</a> &gt; <a href="totemgardenview.php">Browse</a> &gt; <?= $owners ?> Totem Pole</h4>
     <ul class="tabbed">
      <li><a href="totemgarden.php">Information</a></li>
      <li class="activetab"><a href="totemgardenview.php">Browse</a></li>
<?= $st_patricks ? '<li class="stpatrick"><a href="stpatricks.php?where=totem">St. Patrick\'s Day Competition</a></li>' : '' ?>
     </ul>
<?php
// TOTEM POLE GARDEN NPC MATALIE
echo '<a href="/npcprofile.php?npc=Matalie Mansur"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/totemgirl.jpg" align="right" width="350" height="501" alt="(Totem Pole aficionado Matalie)" /></a>';

include 'commons/dialog_open.php';

$options[] = '<a href="totemgarden.php?dialog=1">Ask about the ranking system</a>';

if($owners != 'My')
  $options[] = '<a href="userprofile.php?user=' . $_GET['resident'] . '">View ' . $totem_user['display'] . '\'s Profile</a>';
else if($totem !== false && count($blocks) > 0)
{
  if($totem['last_add'] > $now - (30 * 60))
    $options[] = '<a href="totempoles.php?action=remove&amp;free=yes" onclick="return confirm(\'Really remove the top totem on your pole for free?\');">Have the top totem removed free of charge</a>';
  else
    $options[] = '<a href="totempoles.php?action=remove" onclick="return confirm(\'Really remove the top totem on your pole for ' . ($totem['remove_cost'] * 100) . ' moneys?\');">Have the top totem removed for ' . ($totem['remove_cost'] * 100) . '<span class="money">m</span></a>';
}

if(strlen($message) > 0)
  echo '<p>' . $message . '</p>';
else if($totem !== false)
{
  $say_owner = ($owners == 'My' ? 'your' : $owners);
?>
<p>This is <?= $say_owner ?> Totem Pole.  It has <?= count($blocks) ?> totem<?= count($blocks) != 1 ? 's' : '' ?> on it.  Overall, I'd rank it as being <?= totem_rating($totem['rating'], true) . ($user['user'] == 'telkoth' ? ' (' . $totem['rating'] . ')' : '') ?> pole.</p>
<?php
  if($owners == 'My')
  {
    if($totem['last_add'] > $now - (30 * 60))
      echo '<p>If you added that last totem by accident, I can have it removed and returned to you.  There would usually be a ' . ($totem['remove_cost'] * 100) . ' money fee, but since you added it so recently I don\'t mind removing it for free.</p>';
    else
      echo '<p>By the way, I can have your top-most totem removed and returned to you, but it will cost ' . ($totem['remove_cost'] * 100) . '<span class="money">m</span> to have done.  Also, this fee doubles every time the service is performed on a given pole, so I recommend using this service only sparingly.</p>';
  }
}
include 'commons/dialog_close.php';

if($admin['manageitems'] == 'yes' && $admin['clairvoyant'] == 'yes')
  $options[] = '<a href="/admin/totempolestats.php?userid=' . $totem['userid'] . '">See stats for this pole (admin)</a>';

if($totem !== false && count($blocks) > 0)
{
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
<table id="totempole"><tr><td>
<?php
  foreach($blocks as $block)
    echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/items/totem_x' . $block . '.png" height="32" alt="" />';
?><img src="gfx/totem_base.png" height="8" width="48" alt="" />
</td></tr></table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
