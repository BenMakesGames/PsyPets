<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';
require_once 'commons/formatting.php';
require_once 'commons/totemlib.php';
require_once 'commons/userlib.php';

if($user['show_totemgardern'] == 'no')
{
  header('Location: ./myhouse.php');
  exit();
}

$log = get_item_byname('Log');
$mytotem = get_totem_byuserid($user['idnum']);
$totems = take_apart(',', $mytotem['totem']);
$height = count($totems);
$message = '';

if($mytotem['rating'] < 30)
{
  header('Location: ./totemgarden.php');
  exit();
}

$lak_rooms = array('living room', 'dining room', 'bedroom', 'foyer', 'library');
$l_room = $lak_rooms[array_rand($lak_rooms)];

mt_srand($totem['rating']);

if($_GET['offer'] == 1)
{
  add_inventory_quantity($user['user'], '', 'Log', 'Traded with Ian for your totem pole', $user['incomingto'], $height);

  delete_totem_byuserid($user['idnum']);

  $message = 'Great!  I\'ll send the totem over to Ian\'s, and have the Logs sent to ' . $user['incomingto'] . '!';
}

if($mytotem['rating'] >= 55)
{
  if($_GET['offer'] == 6)
  {
    $matalie = get_user_bydisplay('Matalie Mansur', 'idnum');

    add_inventory($user['user'], 'u:' . $matalie['idnum'], 'Cardinal Totem', 'Traded with Matalie for your totem pole', $user['incomingto']);

    delete_totem_byuserid($user['idnum']);

    $message = 'Awesome, thanks a lot!  You know, I made that totem myself!  I know it looks a lot like the Jay Totem... but you have to start somewhere, right?';
  }
}

if($mytotem['rating'] >= 80 && !($mytotem['rating'] >= 400))
{
  if($_GET['offer'] == 3)
  {
    $amount = ceil(.65 * $log['value'] * $height);

    give_money($user, $amount, 'Sold your totem pole to Lakisha');
    $user['money'] += $amount;

    delete_totem_byuserid($user['idnum']);

    $message = 'Eeee h-hee!  Oh, I have to call Lakisha to let her know!  She\'ll be thrilled!  It\'s going to look <em>great</em> in her ' . $l_room . '!  Anyway, here\'s your money, and thanks again!';
  }
}

if($mytotem['rating'] >= 120)
{
  if($_GET['offer'] == 4)
  {
    add_inventory_quantity($user['user'], '', 'Maze Piece Summoning Scroll', 'For your singular totem, we thank you', $user['incomingto'], 4);

    delete_totem_byuserid($user['idnum']);

    $message = 'Really, you\'re gonna do it!?  Well, sign the letter, then, and we\'ll see what happens.';
  }
}

if($mytotem['rating'] >= 150)
{
  if($_GET['offer'] == 2)
  {
    $nina = get_user_bydisplay('Nina Faber', 'idnum');

    add_inventory($user['user'], 'u:' . $nina['idnum'], 'Hephaestus\' Hammer', 'Traded with Nina for your totem pole', $user['incomingto']);

    delete_totem_byuserid($user['idnum']);

    $message = 'Ooh cool, Nina will be happy to hear it!  I\'ll have her send you the hammer as soon as possible!';
  }
}

if($mytotem['rating'] >= 200)
{
  if($_GET['offer'] == 12)
  {
    add_inventory($user['user'], '', 'The Importance of Being Earnest: Act III', 'Traded with Marian for your totem pole', $user['incomingto']);

    delete_totem_byuserid($user['idnum']);

    $message = 'I read that play recently myself!  It\'s pretty funny, actually.';
  }
}

if($mytotem['rating'] >= 250)
{
  if($_GET['offer'] == 13)
  {
    add_inventory($user['user'], '', 'White Dragon', 'For your singular totem, we thank you', $user['incomingto']);

    delete_totem_byuserid($user['idnum']);
    
    $message = 'Really, you\'re gonna do it!?  Well, sign the letter, then, and we\'ll see what happens.';
  }
}

if($mytotem['rating'] >= 300)
{
  if($_GET['offer'] == 7)
  {
    add_inventory($user['user'], '', 'Alchemist\'s Knapsack', 'Traded with Thaddeus for your totem pole', $user['incomingto']);

    delete_totem_byuserid($user['idnum']);

    $message = 'You know, I tried to open this... \'knapsack\' - to see if it had anything in it at all - but the string just wouldn\'t loosen up!  Really, though, feel how light it is!  Thaddeus seems like a nice guy and all, but there\'s definitely something weird about this thing...';
  }
}

if($mytotem['rating'] >= 400)
{
  if($_GET['offer'] == 10)
  {
    $amount = ceil(.85 * $log['value'] * $height);

    give_money($user, $amount, 'Sold your totem pole to Lakisha');
    $user['money'] += $amount;

    delete_totem_byuserid($user['idnum']);

    $message = 'Eeee h-hee!  Oh, I have to call Lakisha to let her know!  She\'ll be thrilled!  It\'s going to look <em>great</em> in her ' . $l_room . '!  Anyway, here\'s your money, and thanks again!';
  }
}

if($mytotem['rating'] >= 450)
{
  if($_GET['offer'] == 14)
  {
    add_inventory($user['user'], '', 'Red Dragon', 'For your singular totem, we thank you', $user['incomingto']);

    delete_totem_byuserid($user['idnum']);

    $message = 'Really, you\'re gonna do it!?  Well, sign the letter, then, and we\'ll see what happens.';
  }
}

if($mytotem['rating'] >= 550)
{
  if($_GET['offer'] == 5)
  {
    add_inventory($user['user'], '', 'Holy Water', 'Traded with Lance for your totem pole', $user['incomingto']);
    add_inventory($user['user'], '', 'The Passage of Time', 'Traded with Lance for your totem pole', $user['incomingto']);

    delete_totem_byuserid($user['idnum']);

    $message = 'Huh, really?  I mean, I\'m sure it\'s a fair offer, but I have no idea what you\'d do with that stuff!  Anyway, here they are then, and I\'ll get your totem pole down to the temple as soon as possible.  Thanks!';
  }
}

if($mytotem['rating'] >= 650)
{
  if($_GET['offer'] == 8)
  {
    $eve = get_user_bydisplay('Eve Heidel', 'idnum');
  
    add_inventory($user['user'], 'u:' . $eve['idnum'], 'Domesticated Robot Monkey', 'Traded with Eve for your totem pole', $user['incomingto']);

    delete_totem_byuserid($user['idnum']);

    $message = 'I wish I could get a Domesticated Robot Monkey!  They look really cute!  Well, I\'ll let Eve know right away!  Have fun with the little guy!';
  }
}

if($mytotem['rating'] >= 700)
{
  if($_GET['offer'] == 11)
  {
    add_inventory($user['user'], '', 'Golden Phoenix Totem', 'Traded with Matalie for your totem pole', $user['incomingto']);

    delete_totem_byuserid($user['idnum']);

    $message = 'Take good care of it!  You have no idea how hard it is to get a hold on one of those.';
  }
  else if($_GET['offer'] == 15)
  {
    delete_totem_byuserid($user['idnum']);
    add_inventory_quantity($user['user'], '', 'Magic Voucher', 'Traded with Thaddeus for your totem pole', $user['incomingto'], 2);
    
    $message = 'Well, here are your two vouchers, then.  Don\'t spend it all in one place!';
  }
}

if(strlen($message) == 0)
{
  header('Location: ./totemgarden.php');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Totem Pole Garden</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Totem Pole Garden</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="totemgarden.php">Information</a></li>
      <li><a href="totemgardenview.php">Browse</a></li>
     </ul>
<?php
// TOTEM POLE GARDEN NPC MATALIE
echo '<img src="gfx/npcs/totemgirl.jpg" align="right" width="350" height="501" alt="(Totem Pole aficionado Matalie)" />';

include 'commons/dialog_open.php';
echo $message;
include 'commons/dialog_close.php';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
