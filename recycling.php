<?php
$wiki = 'Recycling';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/globals.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';
require_once 'commons/hungryvalue.php';

$options = array();
$dialog_generic = false;

$begin_jerky_quest = false;
$jerky_quest = get_quest_value($user['idnum'], 'spicy jerky quest');

if($jerky_quest === false)
{
  $badges = get_badges_byuserid($user['idnum']);
  if($badges['fisher'] == 'yes')
  {
    require_once 'commons/houselib.php';
  
		$house = get_house_byuser($user['idnum']);

		if($house === false)
		{
			echo "Failed to load your house.<br />\n";
			exit();
		}

    $addons = take_apart(',', $house['addons']);

    if(array_search('Smokehouse', $addons) !== false)
    {
      $begin_jerky_quest = true;
      
      if($_GET['dialog'] == 'fishing_quest')
        add_quest_value($user['idnum'], 'spicy jerky quest', 1);
    }
  }
}
else if($jerky_quest['value'] == 5)
{
  if($_GET['dialog'] == 'despicifier_success')
  {
    $dialog_generic = '<p>You did it!</p><p>Wait, seriously?</p><p>You <em>did!</em></p><p>Amazing!  I\'m going to have Nina make me one of those, too!</p><p>Thanks a lot, ' . $user['display'] . '!  You\'ve done fishers everywhere a great service!</p>';
    update_quest_value($jerky_quest['idnum'], 6);
  }
  else
    $options[] = '<a href="?dialog=despicifier_success">Tell him about the Spicy Jerky Despicifier</a>';
}

if($_GET['dialog'] == 'tips')
{
  $dialog_generic = '<p>Tips, huh?</p><p>Well I say recycle everything you can, before selling it back to the game.  If your pets made an item, they can certainly do it again if they have the materials - gaining experience in the process.  Recycling will get you some of those very materials back, of course.  Recycling <em>and</em> reusing in one!</p>' .
                    '<p>Iron, Wood, Clay, and Glass are very good to recycle for in general, since pets can use those to expand your house.  These materials are expensive to buy from other players, and recycling is free!</p>' .
                    '<p>Hm... I don\'t know if you could count this as a tip, but I once found a book that told you how to "magically" extract Iron from things.  I remember it because it sounded kind of like recycling, but also because it sounded so ridiculous.  "Blueberry, Silver, Blueberry, White."  How could you <em>not</em> remember something so silly?  Ha, ha!</p>';  

  $options[] = '<a href="/recycling.php">Ask about Recycling</a>';
}
else
  $options[] = '<a href="?dialog=tips">Ask for recycling tips</a>';

$recycled = get_quest_value($user['idnum'], 'recycle count');
$recycle_count = (int)$recycled['value'];
/*
if($_GET['view'] == 'ungrouped' || $_GET['view'] == 'grouped')
{
  if($_GET['view'] != $user['recycling_view'])
  {
    $command = 'UPDATE monster_users SET recycling_view=\'' . $_GET['view'] . '\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating recycling view preference');
    
    $user['recycling_view'] = $_GET['view'];
  }
}
*/
if($_POST['action'] == 'recycle')
{
  $greenhouse_points = 0;

  require 'commons/recycling/grouped_recycle.php';
  
  $num_recovered = count($recovered);

  if($num_recovered > 0)
  {
    if($num_recovered > 10)
      $message = 'You recovered ' . $num_recovered . ' materials';
    else
    {
      sort($recovered);
      $message = 'You recovered ' . implode(', ', $recovered);
    }
  }
  else if($recovery)
    $message = 'No items were recovered this time';

  if($greenhouse_points > 0)
  {
    $command = 'UPDATE monster_users SET greenhouse_points=greenhouse_points+' . $greenhouse_points . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'giving greenhouse points');

    if($num_recovered > 0)
      $message .= ', and received ';
    else
      $message .= ', but you still received ';
      
    $message .= $greenhouse_points . ' Greenhouse Point' . ($greenhouse_points != 1 ? 's' : '') . ' (for use at my <a href="/greenhouse.php">Greenhouse</a>).';
  }

  if($recycle_count > (int)$recycled['value'])
  {
    if($recycled === false)
      add_quest_value($user['idnum'], 'recycle count', $recycle_count);
    else
      update_quest_value($recycled['idnum'], $recycle_count);

    $badges = get_badges_byuserid($user['idnum']);
    if($badges['recycler'] == 'no' && $recycle_count >= 1000)
    {
      set_badge($user['idnum'], 'recycler');

      $body = 'Wow, you\'ve recycled a total of ' . $recycle_count . ' items!  It\'s great to see such devoted people.<br /><br />' .
              'Here, take this as a token of my appreciation...<br /><br />' .
              '{i}(You earned the Recycler Badge!){/}';

      psymail_user($user['user'], 'ihobbs', 'You\'ve recycled over 1000 items!', $body);
    }
    else if($badges['yaynature'] == 'no' && $recycle_count >= 5000)
    {
      set_badge($user['idnum'], 'yaynature');

      $body = 'Wow, you\'ve recycled a total of ' . $recycle_count . ' items!  Fantastic!<br /><br />' .
              'Here, take this as a token of my appreciation...<br /><br />' .
              '{i}(You earned the Yay, Nature! Badge!){/}';

      psymail_user($user['user'], 'ihobbs', 'You\'ve recycled over 5000 items!', $body);
    }
  }
}
/*
if($user['recycling_view'] == 'ungrouped')
{
  $items = $database->FetchMultiple('
    SELECT
      a.idnum,a.itemname,
      b.recycle_for,b.graphictype,b.graphic,
      a.creator,a.message,a.message2
    FROM
      monster_inventory AS a,
      monster_items AS b
    WHERE
      a.itemname=b.itemname
      AND b.recycle_for!=\'\'
      AND b.can_recycle=\'yes\'
      AND a.user=' . quote_smart($user['user']) . '
      AND a.location=\'storage\'
    ORDER BY a.itemname ASC
  ');
}
else*/
{
  $items = $database->FetchMultiple('
    SELECT
      COUNT(a.idnum) AS quantity,a.itemname,a.health,a.idnum,
      b.durability,b.recycle_for,b.graphictype,b.graphic
    FROM
      monster_inventory AS a,
      monster_items AS b
    WHERE
      a.itemname=b.itemname
      AND b.recycle_for!=\'\'
      AND b.can_recycle=\'yes\'
      AND a.user=' . quote_smart($user['user']) . '
      AND a.location=\'storage\'
    GROUP BY a.itemname,a.health
    ORDER BY a.itemname ASC,a.health DESC
  ');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Recycling</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Recycling</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="recycling.php">Recycling</a></li>
      <li><a href="greenhouse.php">Greenhouse</a></li>
      <li><a href="recycling_gamesell.php">Refuse Store</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// RECYCLING CENTER NPC IAN
echo '<a href="/npcprofile.php?npc=Ian Hobbs"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/hippy2.png" align="right" width="350" height="493" alt="(Ian the Recycling Center Hippy)" /></a>';

include 'commons/dialog_open.php';

if($error_message)
  echo '<p class="failure">' . $error_message . '</p>';
else if(strlen($message) > 0)
  echo '<p class="success">' . $message . '</p>';
else if($dialog_generic !== false)
  echo $dialog_generic;
else if($begin_jerky_quest && $_GET['dialog'] != 'recycling')
{
  if($_GET['dialog'] == 'fishing_agree')
  {
    echo '<p>I just got back from fishing myself.  It\'s a great way to relax, right?  But you know what drives me nuts?</p><p>Spicy Jerky.</p><p>Fish <em>refuse</em> to bite when you use Spicy Jerky as bait.  It\'s just not even worth trying.  I guess they have weak stomachs or something?</p>';
    $options[] = '<a href="?dialog=fishing_dotdotdot">...</a>';
  }
  else if($_GET['dialog'] == 'fishing_dotdotdot')
  {
    echo '<p>What if there was some way to <em>remove</em> the spice from Spicy Jerky?  Then it\'d be useable!  Hm...</p><p>You know, if anyone could make something like that - something to remove the spice from Spicy Jerky - it\'d be Nina.  That girl can make <em>anything</em>.</p><p>' . $user['display'] . ', you should definitely run this idea by her.  See what she thinks.</p>';
    $options[] = '<a href="?dialog=fishing_quest">Agree to do it.</a>';
  }
  else if($_GET['dialog'] == 'fishing_quest')
  {
    echo '<p>Awesome!  Let me know how it goes, yeah?</p>';
    $options[] = '<a href="?dialog=recycling">Ask about recycling.</a>';
  }
  else if($_GET['dialog'] == 'DENIED')
  {
    echo '<p>Oh...</p>';
    $options[] = '<a href="?dialog=recycling">Ask about recycling.</a>';
  }
  else
  {
    echo '<p>Hey, ' . $user['display'] . '!  Do you by any chance like fishing?</p>';
    $options[] = '<a href="?dialog=fishing_agree">"Yes."</a>';
    $options[] = '<a href="?dialog=DENIED">"No."</a>';
  }
}
else
{
?>
     <p>Just give the word, and we'll reduce any of several items to their constituent parts.  Plushies into Fluff and Dye, for example.</p>
     <p>Not everything is recoverable - in fact sometimes nothing is recoverable - but on average we recover 2/3 of the materials!  Pretty good, huh?  Of course, damaged items yield fewer resources.</p>
<?php
  if($recycle_count >= 5)
    echo '     <p>Oh, and you\'ve recycled a total of ' . $recycle_count . ' items so far - nice!</p>';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

require 'commons/recycling/grouped_view.php';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
