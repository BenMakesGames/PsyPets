<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Aquarium';
$THIS_ROOM = 'Aquarium';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/aquariumlib.php';
require_once 'commons/houselib.php';

if(!addon_exists($house, 'Aquarium'))
{
  header('Location: /myhouse.php');
  exit();
}

$aquarium = get_aquarium($user['idnum']);

$dialog_extra = '';

if($aquarium['trouble_time'] > 0 && $now >= $aquarium['trouble_time'] && $aquarium['item_needed'] == '')
{
  reset_aquarium_needed_item($aquarium);
}

if($_GET['action'] == 'helpout')
{
  if($aquarium['trouble_time'] == 0)
  {
    help_aquarium($aquarium, true); // "true" for first time

    $dialog_extra .= '<p>You have our thanks, ' . $user['display'] . '!</p>';
  }
  else if($now >= $aquarium['trouble_time'])
  {
    $deleted = delete_inventory_fromhome($user['user'], $aquarium['item_needed'], 1);
    
    if($deleted > 0)
    {
      if($aquarium['trouble_time'] == 1)
        $dialog_extra .= '
          <p>As we have only just arrived, we are not prepared to offer you something in return, however you have my word that your good deed will not go unrewarded!</p>
          <p>See me again this time tomorrow.  I will see to it personally that you do not leave empty-handed at that time.</p>
          <p>In the meanwhile, I will spare what people I can to entertain your pets.</p>
        ';
      else
        $dialog_extra .= '
          <p>You\'ve saved us again!  The Merkingdom will not forget your kindness.</p>
          <p>Come see us tomorrow.  We will have something for you then.</p>
        ';

      help_aquarium($aquarium);
    }
  }
}
else if($_GET['action'] == 'dismiss')
{
  if($now >= $aquarium['trouble_time'])
  {
    dismiss_aquarium($aquarium);
    
    $dialog_extra .= '
      <p>Of course.  We understand.</p>
      <p>Thank you for considering our offer.</p>
    ';
  }
}

if($aquarium['next_reward'] != '' && $now >= $aquarium['trouble_time'])
{
  $dialog_extra .= '<p>Please, accept this ' . item_text_link($aquarium['next_reward']) . ' as thanks for your continued support.</p>';

  $reward_item = $aquarium['next_reward'];

  clear_aquarium_reward($aquarium); // clears $aquarium['next_reward'], which is why we take a copy of it before-hand

  add_inventory($user['user'], 'u:38942', $reward_item, 'Given to ' . $user['display'] . ' by The Merkingdom', 'home');
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Aquarium</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Aquarium</h4>
<?php
room_display($house);
echo $message;

if($aquarium['trouble_time'] == 0)
{
  echo '
    <p>Wh-- what\'s this?!  Your Aquarium appears to be populated with tiny merpeople!</p>
    <a href="/npcprofile.php?npc=The+Merkingdom"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/merking.png" alt="' . $aquarium['king_name'] . ' the King" align="right" width="100" height="60" /></a>
  ';

  require 'commons/dialog_open.php';

  echo '
    <p>Allow me to introduce myself.  I am ' . $aquarium['king_name'] . ', king of the merfolk who live in this Aquarium.</p>
    <p>I hate to impose upon you during our first meeting, but life in an Aquarium is not as easy as you\'d think.  Certainly we are free of predators, however there is also a lack of resources... while we can grow some Seaweed and raise some feeder fish, it is nowhere near enough to support ourselves...</p>
    <p>However, I would not deign to ask for your help without offering something in return!  We are skilled craftsmen with a rich culture, and would be happy to not only to share our crafts with you, but to entertain your pets while you are away.</p>
    <p>What say you?</p>
  ';

  require 'commons/dialog_close.php';
  
  echo '<ul><li><a href="/myhouse/addon/aquarium.php?action=helpout">Tell him it\'s a deal!</a></li></ul>';
}
else
{
  $offer_help = 0;

  echo '<a href="/npcprofile.php?npc=The+Merkingdom"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/merking.png" alt="' . $aquarium['king_name'] . ' the King" align="right" width="100" height="60" /></a>';

  require 'commons/dialog_open.php';

  echo $dialog_extra;

  if($now >= $aquarium['trouble_time'])
  {
    if($aquarium['trouble_time'] == 1)
      echo '
        <p>Many of us are having trouble adjusting to our new life here, and some families are in want for ' . item_text_link($aquarium['item_needed']) . '.</p>
        <p>Can you spare any?</p>
      ';
    else
      echo '
        <p>We must impose upon you once again, ' . $user['display'] . ', for we are in great need of ' . item_text_link($aquarium['item_needed']) . '!</p>
        <p>Can you spare any?</p>
      ';

    $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=' . quote_smart($aquarium['item_needed']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\'';
    $item = fetch_single($command, 'fetching needed item');
    
    $offer_help = ($item['c'] > 0);
  }
  else if($aquarium['happy'] == 'yes')
    echo '<p>Thank you, ' . $user['display'] . '!</p>';
  else
    echo '<p>Until next time, ' . $user['display'] . '.</p>';

  require 'commons/dialog_close.php';

  if($offer_help === true)
    echo '
      <ul>
       <li><a href="/myhouse/addon/aquarium.php?action=helpout">Give one to The Merkingdom</a> <i>(you have ' . $item['c'] . ' at home)</i></li>
      </ul>
    ';
  else if($offer_help === false)
    echo '
      <ul>
       <li class="dim">Give one to The Merkingdom (you do not have any at home)</li>
       <li><a href="/myhouse/addon/aquarium.php?action=dismiss">Explain that, unfortunately, this will not be possible</a></li>
      </ul>
    ';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
