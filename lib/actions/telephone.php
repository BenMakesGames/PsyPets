<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';
  
$call = (array_key_exists('call', $_GET) ? str_replace('*', '+', trim($_GET['call'])) : '');
$step = (int)$_GET['step'];

$try_again = false;

$amys_number = '9+0' . amelia_earhart_number($user);
$called_amy = get_quest_value($user['idnum'], 'called amelia earhart');

if(substr($this_inventory['location'], 0, 4) != 'home' && substr($this_item['itemtype'], 0, 25) != 'electronic/telephone/cell')
  echo '<p>The Telephone must be at home to work.</p>';
else if($call == '')
{
  $try_again = true;
}
else if($call == '0')
{
?>
<p>This is the Automated HERG Telephone Help System.</p>
<p>If you have an emergency, hang up and call extension 11111 immediately.</p>
<p>...</p>
<p>For HERG reception, dial extension 10000.</p>
<p>For Resident Services, dial extension 20000.</p>
<p>If you know the researcher you wish to contact, dial 12, followed by the researcher's 3-digit code.</p>
<p>To dial a number outside of HERG Labs, dial 9, a plus-sign or asterisk, and the full telephone number starting with the country code.</p>
<hr />
<?php
  $try_again = true;
}
else if(strlen($call) < 5 || !preg_match('/[0-9+-]+/', $call) || (strlen($call) > 5 && $call[0] != '9'))
{
?>
<p>*beep, b-beep*</p>
<p>The number you dialed was not recognized.</p>
<p>Please check the number, and try again, or dial 0 for the Automated HERG Telephone Help System.</p>
<hr />
<?php
  $try_again = true;
}
else if($call == '22444' && $step == 1)
{
  $adjectives = array('quiet', 'bored-sounding', 'squeaky', 'professional-sounding');
  $opening = array(
    ' What can I get you today?',
    ' What can I get you?',
    ' Can I take your order, please?',
    ' What would you like today?',
    ' Can I help you?',
    '',
  );
?>
<i>After a few rings, a <?= $adjectives[array_rand($adjectives)] ?>, <?= mt_rand(1, 2) == 1 ? 'male' : 'female' ?> voice says:</i></p>
<?php
  if($_GET['dialog'] != 'no')
    echo '<p>"In Time Pizza Pizzeria.' . $opening[array_rand($opening)] . '"</p>';

  $cp = get_item_byname('Cheese Pizza');
  $mp = get_item_byname('Meat Pizza');
  $mlp = get_item_byname('Meat Lover\'s Pizza');
?>
<form action="itemaction.php?call=22444&amp;step=2&amp;idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p>
 <input name="quantity" maxlength="1" size="1" value="1" />
 <select name="pizza">
  <option value="1">Cheese Pizza (<?= $cp['value'] + 4 ?>m)</option>
  <option value="2">Meat Pizza (<?= $mp['value'] + 4 ?>m)</option>
  <option value="3">Meat Lover's Pizza (<?= $mlp['value'] + 4 ?>m)</option>
 </select>
</p>
<p><input type="submit" name="action" value="Place Order" class="bigbutton" /></p>
</form>
<?php
}
else if($call == '29212' || $call == '29218')
{
  echo '<p><i>(The phone rings and rings before an answering machine finally picks up.  Disappointed, you hang up.)</i></p>';
  $try_again = true;
}
else if($call == '22444' && ($step == 2 || $step == 3))
{
  $prank = false;

  $quantity = (int)$_POST['quantity'];

  if($_POST['pizza'] == 1)
    $itemname = 'Cheese Pizza';
  else if($_POST['pizza'] == 2)
    $itemname = 'Meat Pizza';
  else if($_POST['pizza'] == 3)
    $itemname = 'Meat Lover\'s Pizza';
  else
    $prank = true;

  if($quantity < 1 || $quantity > 9)
    $prank = true;

  $details = get_item_byname($itemname, 'value');
  if($details === false)
    $prank = true;

  if($prank)
  {
    $command = 'SELECT display FROM monster_users ORDER BY lastactivity DESC LIMIT 3';
    $yay = $database->FetchMultiple($command, 'itemaction.php');
    $name = $yay[mt_rand(0, 2)]['display'];

    $try_again = true;
?>
<p>"Hey!  Is this <?= $name ?>?  You'd better stop prank-calling us!"</p>
<p><i>*click*</i></p></hr>
<?php
  }
  else
  {
    $price = ($details['value'] + 4) * $quantity;

    if($step == 2)
    {
?>
<p>"<?= $quantity . ' ' . $itemname . ($quantity != 1 ? 's' : '') ?>?  Your total is <?= $price ?><span class="money">m</span>."</p>
<?php
      if($price > $user['money'])
      {
        echo '<p>"Oh, you don\'t have enough money?  Well, please call again another time."</p>' .
             '<p><i>*click*</i></p><hr />';
        $try_again = true;
      }
      else
      {
?>
<form action="?call=22444&amp;step=3&amp;idnum=<?= $this_inventory['idnum'] ?>" method="post">
<input type="hidden" name="quantity" value="<?= $quantity ?>" /><input type="hidden" name="pizza" value="<?= $_POST['pizza']?>" />
<p><input type="submit" name="action" value="Confirm Order" class="bigbutton" /> <input type="button" value="Change Order" onclick="window.location='itemaction.php?call=1&step=1&dialog=no&idnum=<?= $this_inventory['idnum'] ?>';" class="bigbutton" /></p>
</form>
<?php
      }
    }
    else if($step == 3)
    {
      if($price > $user['money'])
      {
        echo '<p>"Oh, you don\'t have enough money?  Well, please call again another time."</p>' .
             '<p><i>*click*</i></p><hr />';
        $try_again = true;
      }
      else
      {
        $closing = array('Got it. ', 'Thanks. ', 'Thank you. ', '');

        take_money($user, $price, 'In Time Pizza delivery: ' . $itemname . ' x' . $quantity);
        $user['money'] -= $price;

        add_inventory_quantity($user['user'], '', $itemname, 'In Time Pizza', 'storage/incoming', $quantity);
        flag_new_incoming_items($user['user']);

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'In Time Pizza Pizzas Ordered', $quantity);

        echo '"' . $closing[array_rand($closing)] . 'Your pizza will get there in time."</p>' .
             "<script language=\"javascript\">\ndocument.getElementById('moneysonhand').innerHTML='" . $user['money'] . "';\n</script>" .
             '<p><i>*click*</i><hr />';
             
        $try_again = true;
      }
    }
  }
}
else if($call >= 20000 && $call <= 29999)
{
  echo '<p><i>That\'s a "Resident Services" number.  Honestly, it\'s easier just to walk to most places.</i></p><hr />';
  $try_again = true;
}
else if($call == '10000')
{
  echo '<p><i>That\'s the "Help Desk" number.  Honestly, it\'s easier just to walk to there.</i></p><ul><li><a href="/help/">Visit the Help Desk</a></li></ul><hr />';
  $try_again = true;
}
else if($call == '11111')
{
  echo '<p><i>That\'s the emergency number, but it\'s not an emergency!</i></p><hr />';
  $try_again = true;
}
else if($call > 12000 && $call <= 12999)
{
  echo '<p><i>You get someone\'s voicemail.</i></p><hr />';
  $try_again = true;
}
else if($call == $amys_number && $called_amy === false)
{
  add_quest_value($user['idnum'], 'called amelia earhart', 1);
  add_inventory($user['user'], 'u:' . $user['idnum'], 'Scrap of Paper with Number on It', '', $this_inventory['location']);
?>
<p>"Fred, is that you?</p>
<p>"Oh, sorry.  Who is this?</p>
<p>"<?= $user['display'] ?>... I see.  I was told someone like you might call.</p>
<p>"Sorry, I don't have much time, and you won't be able to call this number again.  Grab something to write on, and write this number down.</p>
<p>"Are you ready?</p>
<p>"The number is '<?= book_code_number($user) ?>'."</p>
<p><i>(You write the number down on a Scrap of Paper!)</i></p>
<p>"Did you get that?</p>
<p>"Sorry I can't explain more, <?= $user['display'] ?>.</p>
<p>"Oh, and I'm Amy.</p>
<p>"Good luck!"</p>
<p>*click*</p>
<hr />
<?php
  $try_again = true;
}
else
{
  echo '<p><i>No one picks up.</i></p><hr />';
  $try_again = true;
}

if($try_again)
{
  $fbi_quest = get_quest_value($user['idnum'], 'close encounter');
  
  echo '<p>Who would you like to call?</p><h5>Address Book</h5><ul>';
  
  $numbers = array(22444 => 'In Time Pizza');

  if($fbi_quest['value'] == 7)
  {
    $numbers[29212] = 'Agent Sculder';
    $numbers[29218] = 'Agent Mully';
  }
  
  foreach($numbers as $number=>$name)
  {
    echo '<li><a href="?idnum=' . $this_inventory['idnum'] . '&amp;call=' . $number . '&amp;step=1" title="' . $number . '">' . $name . '</a></li>';
  }

  echo '
    </ul>
    <h5>Manual Dial</h5>
    <form method="get">
    <input type="hidden" name="idnum" value="' . $this_inventory['idnum'] . '" />
    <input type="hidden" name="step" value="1" />
    <p><input type="text" name="call" /> <input type="submit" value="Dial" /></p>
    </form>
  ';
}
?>
