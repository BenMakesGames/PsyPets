<?php
require_once 'commons/init.php';

/*
  reduces max house space by 100
  allows you to store infinite number of books (duplicates allowed)
  add/remove/read copies at will
    disallow reading of books that may destroy themselves
      will have to keep an array of such books within the addon
  show off library in profile
  other features?
    secret trapdoor
    tutor
*/

$whereat = 'home';
$wiki = 'Library_(add-on)#Bat_Cave';
$THIS_ROOM = 'Bat Cave';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/librarylib.php';
require_once 'commons/questlib.php';

if(!addon_exists($house, 'Library'))
{
  header('Location: /myhouse.php');
  exit();
}

$book_count = get_library_book_count($user['idnum']);

if($book_count < 10)
{
  header('Location: /myhouse/addon/library.php');
  exit();
}

$dialog = false;

$profile = get_user_profile($user['idnum']);
$badges = get_badges_byuserid($user['idnum']);

$sir = ($profile['gender'] == 'female' ? 'madam' : 'sir');

$bat_costume = get_item_byname('Bat Costume');
$costume_price = floor($bat_costume['value'] * 2.5);

if($_GET['action'] == 'buycostume')
{
  if($user['money'] >= $costume_price)
  {
    take_money($user, $costume_price, 'Purchased a Bat Costume from your Bat Cave');

    $user['money'] -= $costume_price;

    add_inventory($user['user'], '', 'Bat Costume', 'Purchased from ' . $user['display'] . '\'s Bat Cave', 'home');

    $dialog = '<p>Of course, ' . $sir . '.  I\'ve placed it in your Common room.</p>';

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Bat Costumes Purchased from Bat Cave', 1);
  }
  else
    $dialog = '<p>I\'m afraid you don\'t have enough money, ' . $sir . '.</p>';

  $dialog .= '<p>Anything else I can get for you?</p>';
}
else if($_GET['action'] == 'fightcrime')
{
  $command = 'UPDATE monster_users SET title=\'Caped Crusader\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'updating title');
  
  $user['title'] = 'Caped Crusader';
  
  $dialog = 'Yes, ' . $sir . '.  Very good.';
}

if(addon_exists($house, 'Basement'))
{
  if($_GET['dialog'] == 'basementspace')
  {
    if($badges['island'] == 'no')
      $dialog = '<p>An interesting idea, ' . $sir . '!  However, I\'m afraid the estate is not large enough to accomodate the kind of expansion you\'re talking about.  We\'ll need the Island Badge before we can proceed...</p>';
    else
    {
      $expansion_quest = get_quest_value($user['idnum'], 'bat cave basement');

      if($expansion_quest === false)
      {
        add_quest_value($user['idnum'], 'bat cave basement', 1);

        $dialog = '<p>An interesting idea, ' . $sir . '!  However, the plans for expanding the Basement into the Bat Cave would be extremely complex!  To add another floor... maybe 500 Basement Blueprints would be enough?</p>' .
                  '<p>Perhaps this solution would be more amenable: rather than adding an entire floor all at once, make Basement expansions piecemeal: 5 Basement Blueprints would adequately describe a 1-space expansion.</p>';
      }
      else
        $dialog = '<p>As we discussed, 5 Basement Blueprints will be enough to get 1 space added to the Basement by expanding it into the Bat Cave.</p>';

      $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Basement Blueprint\' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\'';
      $data = fetch_single($command, 'fetching blueprints');
      $blueprints = (int)$data['c'];
      $space = floor($blueprints / 5);

      $dialog .= '<p>We have ' . $blueprints . ' <a href="/encyclopedia2.php?item=Basement%20Blueprint">Basement Blueprint</a>s in the house.  With that we could get ' . $space . ' space added to the Basement.';

      if($space > 0)
        $options[] = '<a href="/myhouse/addon/library_batcave.php?action=expand">Make it so (' . ($space * 5) . ' Basement Blueprints will be used)</a>';
    }
  }
  else if($_GET['action'] == 'expand')
  {
    $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Basement Blueprint\' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\'';
    $data = fetch_single($command, 'fetching blueprints');
    $blueprints = (int)$data['c'];
    $space = floor($blueprints / 5);

    $deleted = delete_inventory_fromhome($user['user'], 'Basement Blueprint', $space * 5);

    $space = floor($deleted / 5);
    
    if($space > 0)
    {
      $command = 'UPDATE monster_houses SET maxbasement=maxbasement+' . $space . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
      fetch_none($command, 'increasing basement size');
      
      $dialog = '<p>Consider it done, ' . $sir . '.</p>';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Extended Your Basement with a Basement Blueprint', $space);
    }
  }
  else
    $options[] = '<a href="/myhouse/addon/library_batcave.php?dialog=basementspace">Explain that you\'d like to use the Bat Cave to expand your Basement</a>';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Library &gt; Bat Cave</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/library.php">Library</a> &gt; Bat Cave</h4>
<?php
room_display($house);

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";
?>
<h5>Butler</h4>
<?php
// BUTLER NPC
//echo '<img src="/gfx/npcs/smithy2.png" align="right" width="350" height="280" alt="(Nina the Smithy)" />';

include 'commons/dialog_open.php';

if(strlen($_GET['msg']) > 0)
  $error_messages[] = form_message(explode(',', $_GET['msg']));

if($dialog === false)
{
  $local_hour = gmdate('H', $now + (60 * 60 * $user['timezone']) + ($user['daylightsavings'] == 'yes' ? 3600 : 0));

  if($local_hour > 4 && $local_hour < 12)
    $greetings = 'Good morning, ' . $sir . '.';
  else if($local_hour < 18)
    $greetings = 'Good afternoon, ' . $sir . '.';
  else if($local_hour < 23)
    $greetings = 'Good evening, ' . $sir . '.';
  else
    $greetings = 'Up late tonight, are we ' . $sir . '?';

  echo '<p>' . $greetings . '  What can I help you with?</p>';
}
else
  echo $dialog;

include 'commons/dialog_close.php';

if($user['title'] != 'Caped Crusader')
  $options[] = '<a href="/myhouse/addon/library_batcave.php?action=fightcrime">Ask to have your title changed to "Caped Crusader"</a>';

echo '<ul>';

if(count($options) > 0)
  echo '<li>' . implode('</li><li>', $options) . '</li>';

if($user['money'] >= $costume_price)
  echo '<li><a href="/myhouse/addon/library_batcave.php?action=buycostume">Tell him you need a brand-new costume (' . $costume_price . '<span class="money">m</span>)</a></li>';
else
  echo '<li class="dim">Tell him you need a brand-new costume (' . $costume_price . '<span class="money">m</span>)</li>';

if($profile['gender'] == 'none')
  echo '<li><a href="/myaccount/searchable.php">Update your gender</a></li>';

echo '</ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
