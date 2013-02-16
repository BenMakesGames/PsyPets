<?php
$wiki = 'Temple';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/questlib.php';
require_once 'commons/templelib.php';

// get today's timestamp
$now = time();
$one_day = 24 * 60 * 60;
$today = (int)($now / $one_day) * $one_day;
$tomorrow = $today + $one_day;

// read the info about the gods
$gods = $database->FetchMultipleBy('SELECT * FROM monster_gods', 'id');

$attitude = array(
  -4 => 'angry',
  -3 => 'angry',
  -2 => 'displeased',
  -1 => 'concerned',
   0 => 'ambivalent',
   1 => 'content',
   2 => 'pleased',
   3 => 'rapturous',
   4 => 'rapturous'
);

if($_POST['action'] == 'donate' && $user['license'] == 'yes')
{
  if($_POST['god'] != 'rigzivizgi' && $_POST['god'] != 'gijubi' && $_POST['god'] != 'kirikashu')
  {
    $error_message = 'Please choose a deity to contribute to.';
  }
  else if($_POST['amount'] <= 0 || (int)$_POST['amount'] != $_POST['amount'])
  {
    $error_message = 'You must contribute at least one money, and not a fractional amount.';
  }
  else if((int)$_POST['amount'] > $user['money'])
  {
    $error_message = 'You do not have that much money.';
  }
  else
  {
    $amount = (int)$_POST['amount'];

    $user['money'] -= $amount;
    $gods[$_POST['god']]['contributions'] += $amount;
    $gods[$_POST['god']]['currentvalue'] += $amount;

    $database->FetchNone("UPDATE monster_gods SET contributions=contributions+$amount,currentvalue=currentvalue+$amount WHERE id='" . $_POST['god'] . "' LIMIT 1");
    $database->FetchNone("UPDATE monster_users SET money=money-$amount WHERE idnum=" . $user['idnum'] . ' LIMIT 1');

    add_transaction($user['user'], $now, 'Temple contribution', -$amount);

    if($amount >= 100)
    {
      $percent = (int)($amount / 100) * 1.2;
      if($percent > 90)
        $percent = 90;

      if(rand(1, 100) <= $percent)
      {
        $t = rand(1, 100);

        if($t >= 1 && $t <= 25)
          $item = 'Maze Piece Summoning Scroll';
        else if($t >= 26 && $t <= 40)
          $item = 'Vanilla Candle';
        else if($t >= 41 && $t <= 66)
        {
          if($_POST['god'] == 'rigzivizgi')
            $item = 'Air of Mistrust';
          else if($_POST['god'] == 'gijubi')
            $item = 'Air of Sarcasm';
          else if($_POST['god'] == 'kirikashu')
            $item = 'Air of Enthusiasm';
        }
        else if($t >= 67 && $t <= 100)
        {
          if($_POST['god'] == 'rigzivizgi')
            $item = 'Antique Armor';
          else if($_POST['god'] == 'gijubi')
            $item = 'Owl Totem';
          else if($_POST['god'] == 'kirikashu')
            $item = 'Cognitive Meliorator Blueprint';
        }

        add_inventory($user['user'], $SETTINGS['site_ingame_mailer'], $item, 'Gifted to you from ' . $gods[$_POST['god']]['name'], $user['incomingto']);
      }
    }

    $_POST['god'] = '';
    $_POST['amount'] = '';

    donate_to_temple($user, $amount);
  }
}

if($user['idnum'] <= 31443)
{
  $hungry_tapestry = get_quest_value($user['idnum'], 'GotHungryTapestry');
  if($hungry_tapestry === false)
  {
    add_inventory($user['user'], '', 'Hungry Tapestry (level 0)', 'Given to you by Lance, the Temple monk', $user['incomingto']);
    $error_message .= 'Ah, ' . $user['display'] . '.  I was told to give you one of these...</p><p><i>(You received a Hungry Tapestry (level 0)!  You can find it in your ' . $user['incomingto'] . '.)</i>';
    add_quest_value($user['idnum'], 'GotHungryTapestry', 1);
  }
}

if(($now_month == 12 && $now_day == 31 && $now_year == 2009) ||
  ($now_month == 1 && $now_day == 1 && $now_year == 2010))
{
  $nyebm = get_quest_value($user['idnum'], 'new year eve blue moon');
  
  if($nyebm === false)
  {
    add_inventory($user['user'], '', 'New Year Eve Blue Moon Wand', 'Given to you by Lance, the Temple monk', $user['incomingto']);
    $error_message .= 'The Blue Moon is one of Kaera\'s favorite creations, but to have one on New Year\'s Eve is truly unique!</p><p>Here, take this, and have a wonderful New Year! :)</p><p><i>(You received a New Year Eve Blue Moon Wand!  You can find it in your ' . $user['incomingto'] . '.)</i>';
    add_quest_value($user['idnum'], 'new year eve blue moon', 1);
  }
}

$temple_donations = get_quest_value($user['idnum'], 'temple donations');
$donations = (int)$temple_donations['value'];

$quest_totem = get_quest_value($user['idnum'], 'totem quest');

if($quest_totem['value'] == 6)
{
  $command = 'SELECT idnum FROM monster_inventory WHERE itemname=' . quote_smart('Julio\'s Notes') . ' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\' LIMIT 1';
  $notesid = $database->FetchSingle($command, 'fetching julio\'s notes');

  if($notesid !== false)
  {
    if($_GET['dialog'] == 6)
    {
      $give_notes_dialog = true;

      delete_inventory_byid($notesid['idnum']); 

      update_quest_value($quest_totem['idnum'], 7);

      add_inventory($user['user'], 'u:28359', 'Cup of Knowledge', 'Given to you by Lance Sussman', $user['incomingto']);
    }
    else
      $options[] = '<a href="temple.php?dialog=6">Give Lance Julio\'s Notes</a>';
  }
  else
    $options[] = '<i class="dim">Julio\'s Notes need to be in your Storage</i></a>';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Temple</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Temple</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="temple.php">Donations</a></li>
      <li><a href="temple_exchange.php">Exchanges</a></li>
     </ul>
<?php
// MONK LANCE
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/monk.png" align="right" width="350" height="535" alt="(Lance the Monk)" />';

include 'commons/dialog_open.php';

if($error_message)
  echo "<p>$error_message</p>";
else
{
  if($give_notes_dialog)
  {
    echo '<p>From Julio?</p>' .
         '<p><i>(Lance takes Julio\'s Notes and flips through them.)</i></p>' .
         '<p>Hm?  What?  Is this in earnest?</p>' .
         '<p>If Julio\'s assessment is correct, I will be spending a very long time going back over every other text we have collected to date, to make sure that we have not, in our ignorance, misinterpreted them.  It\'s entirely possible that under this new light, we might see something new in the old texts.</p>' .
         '<p>I must thank you for taking the time to deliver this to me...</p>' .
         '<p>Here: a cup from Kaera\'s fountain.  The water from that fountain is blessed, and those who drink of it are said to gain clairty of thought.  Use it well.</p>' .
         '<p>And thank you again.</p>' .
         '<p><i>(You have received a Cup of Knowledge.  You can find it in ' . $user['incomingto'] . '.)</i></p>'; 

    $options[] = '<a href="temple.php?dialog=1">Ask about Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=2">Ask about making donations</a>';
  }
  else if($_GET['dialog'] == 1)
  {
    echo '<p>Ki Ri Kashu is God of All - He who sacrificed Himself in order to become the universe we now live in, and His Children: Kaera, Gizubi, and Rizi Vizi Kaera.</p>';

    $options[] = '<a href="temple.php?dialog=3">Ask about Kaera Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=4">Ask about Gizubi Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=5">Ask about Rizi Vizi Kaera Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=2">Ask about making donations</a>';
  }
  else if($_GET['dialog'] == 2)
  {
    echo '<p>The Children of Ki Ri Kashu are at conflict with each other, and we are part of that conflict: a happy god is a merciful god, while an angry god can be cruel...</p>' .
         '<p>If you wish to support a particular god, you may make a donation here.  Generous donations are sometimes rewarded by the gods themselves; there are some who have come with 500 moneys, and left with an Owl Totem or other gift.</p>' .
         '<p>Furthermore, the donations are tallied every day, and each god\'s esteem will rise or fall depending on how well it was supported compared to the other two.  In this way, each individual contributes to the happiness of the gods, and therefore the happiness of the domain which they oversee.</p>';

    $options[] = '<a href="temple.php?dialog=3">Ask about Kaera Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=4">Ask about Gizubi Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=5">Ask about Rizi Vizi Kaera Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=1">Ask about Ki Ri Kashu</a>';
  }
  else if($_GET['dialog'] == 3)
  {
    echo '<p>Kaera is the first-born of Ki Ri Kashu and thus represents, and is responsible for, beginnings.  She is a god that places logic, order, and reason above all things.</p><p>It is Kaera\'s strength that makes us confident and lucid - sometimes stubborn - giving us the ability to do what we know is just.  Many say that it is Kaera that gave us the Natural Laws.</p>';
    $options[] = '<a href="temple.php?dialog=4">Ask about Gizubi Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=5">Ask about Rizi Vizi Kaera Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=1">Ask about Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=2">Ask about making donations</a>';
  }
  else if($_GET['dialog'] == 4)
  {
    echo '<p>Gizubi is Ki Ri Kashu\'s second-born, and his only son.  He seeks to make the most of life, placing pleasure and indulgence above all things.</p><p>It is Gizubi\'s influence that makes us whimsical, open, and sometimes careless.  Though these properties make Gizubi seem completely irresponsible, it is Gizubi that oversees the growth and development of life from right after it begins until right before it ends.</p>';
    $options[] = '<a href="temple.php?dialog=3">Ask about Kaera Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=5">Ask about Rizi Vizi Kaera Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=1">Ask about Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=2">Ask about making donations</a>';
  }
  else if($_GET['dialog'] == 5)
  {
    echo '<p>Rizi Vizi Kaera is Kaera\'s identical twin, born last of the Children of Ki Ri Kashu.  Rizi Vizi wants only to oppose that which Kaera values, and thus represents, and is responsible for, the endings of things.</p><p>It is Rizi Vizi\'s strength that gives us the ability to think in unconventional, sometimes devious ways, and makes us willing to work toward our goals no matter the cost: sometimes selflessly, but sometimes selfishly.</p>';
    $options[] = '<a href="temple.php?dialog=3">Ask about Kaera Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=4">Ask about Gizubi Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=1">Ask about Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=2">Ask about making donations</a>';
  }
  else
  {
    echo '<p>Welcome to the temple of Ki Ri Kashu.</p>';

    if($donations > 0)
      echo '<p>Thank you for your support!  You\'ve donated a total of ' . $donations . '<span class="money">m</span> to the temple.</p>';

    $options[] = '<a href="temple.php?dialog=1">Ask about Ki Ri Kashu</a>';
    $options[] = '<a href="temple.php?dialog=2">Ask about making donations</a>';
  }
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

foreach($gods as $id=>$god)
{
?>
     <h5><?= $god['name'] . $god['title'] ?></h5>
     <p>
      Total contributions: <?= $god["contributions"] ?><span class="money">m</span><br />
      Today's contributions: <?= $god["currentvalue"] ?><span class="money">m</span><br />
     </p>
     <p><?= $god['name'] . ' is ' . $attitude[round($god['attitude'] / 30)] ?>.</p>
<?php
}
?>
     <h5>Make a Contribution</h5>
<?php
if($user["license"] == "yes")
{
?>
     <form action="temple.php" method="post">
     <table>
      <tr>
       <td valign="top">Deity: </td>
       <td>
<?php
  foreach($gods as $id=>$god)
  {
?>
        <input type="radio" name="god" value="<?= $id ?>" /> <?= $god['name'] ?><br />
<?php
  }
?>
       </td>
      </tr>
      <tr>
       <td>Amount: </td>
       <td><input name="amount" value="<?= $_POST["amount"] ?>" maxlength="5" style="width:64px;" /><span class="money">m</span></td>
      </tr>
      <tr>
       <td></td>
       <td><input type="hidden" name="action" value="donate" /><input type="submit" name="submit" value="Donate" /></td>
      </tr>
     </table>
     </form>
<?php
}
else
{
?>
     <p>The temple cannot accept donations from members who do not have a <a href="ltc.php">License to Commerce</a>.</p>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
