<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Game Room';
$THIS_ROOM = 'Game Room';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/gameroomlib.php';

if(!addon_exists($house, 'Game Room'))
{
  header('Location: /myhouse.php');
  exit();
}

if($_POST['action'] == 'Buy')
{
  $quantity = (int)$_POST['tokens'];
  
  $cost = $quantity * 10;
  
  if($cost > $user['money'])
    $token_message = '<p class="failure">' . $quantity . ' Game Tokens would cost you ' . $cost . '<span class="money">m</span>; you only have ' . $user['money'] . '<span class="money">m</span>.</p>';
  else
  {
    take_money($user, $cost, 'Bought ' . $quantity . ' Game Tokens');
    credit_game_room($user['idnum'], $quantity);
  }
}

$game_room = get_game_room($user['idnum'], true);

$game_room_games = get_game_room_games($user['idnum']);

$game_count = get_total_game_count();

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Game Room</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Game Room</h4>
<?php
echo $message;

room_display($house);

echo '
  <ul class="tabbed">
   <li class="activetab"><a href="/myhouse/addon/game_room.php">Game Room</a></li>
   <li><a href="/myhouse/addon/game_room_prizes.php">Prize Shop</a></li>
  </ul>
  <h5>Game Tokens</h5>
  <p>You have ' . $game_room['money'] . ' Game Tokens.</p>
  <p>Playing games at the Game Room requires Game Tokens.  If you have any Game Tokens, your pets are very likely to play in the Game Room as their hourly activity.</p>
  <h6>Buy Game Tokens</h6>
  ' . $token_message . '
  <p>You may buy 1 Game Token for 10<span class="money">m</span>.</p>
  <form method="post">
  <p><input name="tokens" type="number" min="0" max="' . ($user['money'] / 10) . '" size="4" maxlength="' . strlen($user['money'] / 10) . '" /> &times; 10<span class="money">m</span> <input type="submit" name="action" value="Buy" /></p>
  </form>
  <h5>Games (' . count($game_room_games) . '/' . $game_count . ')</h5>
  <p>Your pets have played the following games:</p>
';

if(count($game_room_games) > 0)
{
  echo '
    <table>
     <thead>
      <tr class="titlerow"><th></th><th>Game</th><th>Token Cost</th></tr>
     </thead>
     <tbody>
  ';
  
  $rowclass = begin_row_class();
  
  foreach($game_room_games as $game)
  {
    echo '
      <tr class="' . $rowclass . '">
       <td><img src="//' . $SETTINGS['static_domain'] . '/gfx/games/' . $game['graphic'] . '.png" width="32" height="32" alt="" /></td>
       <td>
        ' . $game['name'] . '<br />
        <i>' . $game['type'] . '</i>
       </td>
       <td class="centered">' . $game['level'] . '</td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
  }
  
  echo '
     </tbody>
    </table>
  ';
}
else
  echo '<p>Your pets have not beat any games yet.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
