<?php
if($okay_to_be_here !== true)
  exit();

$destination = $this_inventory['location'];

$i = mt_rand(1, 15);
$now = time();

if(array_key_exists('i', $_GET) && $user['admin']['alphalevel'] >= 10)
  $i = (int)$_GET['i'];

$AGAIN_WITH_SAME = true;

switch($i)
{
  case 1: // changes your avatar to a 404 graphic
    $dialog = '<p>I\'ll change your avatar into something AMAZING!  Wait and see!</p>';
    $message = '<p>The wand twitches - or perhaps trembles - in your hand.</p><p>You wait to hear more from the wand, but it remains conspicuously silent.</p><p><i>(Your avatar has been changed!)</i></p>';

    if(mt_rand(1, 2) == 1)
      $graphic = 'special-secret/404_moz.png';
    else
      $graphic = 'special-secret/404_ie.png';

    $database->FetchNone('UPDATE monster_users SET graphic=' . quote_smart($graphic) . ',is_a_whale=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');
    break;

  case 2: // summons a Dragon Plushy
    $message = '<p>The wand begins the incantations to summon a <em>powerful dragon</em>, but forgets the last syllable...</p><p><i>(You received a Dragon Plushy!)</i></p>';

    add_inventory($user['user'], '', 'Dragon Plushy', 'Summoned by a Wand of Blunders', $this_inventory['location']);
    break;

  case 3: // does nothing
    $message = '<p>The wand fires a bullet, which ricochets off a couple walls before striking the wand, knocking it out of your hand!</p>';

    break;

  case 4: // summons 2-4 Broken Glass
    $glass = mt_rand(2, mt_rand(3, 4));

    $message = '<p>The wand makes a noise like it\'s clearing its throat, then begins to sing a lovely tune...</p><p>... then hits a bad note, and shatters several of the glasses in your house.</p><p><i>(You receive ' . $glass . ' Broken Glass.)</i></p>';

    add_inventory_quantity($user['user'], '', 'Broken Glass', 'Broken by a Wand of Blunders', 'home', $glass);
    break;

  case 5: // summons a Small Rock
    $message = '<p>You try to use the wand, but stumble on a Small Rock.</p><p><i>(Where\'d that come from, anyway?)</i></p>';

    add_inventory($user['user'], '', 'Small Rock', '', $this_inventory['location']);
    break;

  case 6: // summons an Old Boot
    $message = '<p>You try to use the wand, but trip on an Old Boot.</p><p><i>(Where\'d that come from, anyway?)</i></p>';

    add_inventory($user['user'], '', 'Old Boot', '', $this_inventory['location']);
    break;

  case 7: // break all rocks in house, if there are at least 3, otherwise do action 8 instead
    $command = 'SELECT idnum,itemname FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND itemname IN (\'Small Rock\', \'Large Rock\', \'Really Enormously Tremendous Rock\')';
    $inventory = $database->FetchMultiple($command, 'fetching rocks');

    if(count($inventory) > 2)
    {
      require_once 'commons/rocks.php';

      $RECOUNT_INVENTORY = true;

      $dialog = '<p>I can help you organize your house!  Just you watch!</p>';

      $message = '<p>The wand organizes all of the rocks in your house, from smallest to largest.  Once done, however, the wand utters an "oops" as the largest rock begins to roll, bumping into the next-largest rock and starting a chain reaction that results in the destruction of every last rock.</p>';

      $itemlist = array();

      $num_items = 0;

      foreach($inventory as $item)
      {
        if($item['itemname'] == 'Small Rock')
          $num_items += (rand() % 4) - 1;
        else if($item['itemname'] == 'Large Rock')
          $num_items += (rand() % 7) - 1;
        else if($item['itemname'] == 'Really Enormously Tremendous Rock')
          $num_items += (rand() % 11) - 1;

        delete_inventory_byid($item['idnum']);
      }

      $itemlist = GenerateItemsFromRocks($num_items);

      if($num_items > 1)
      {
        $say_rocks = 'rocks';
        $say_them = 'them';
      }
      else
      {
        $say_rocks = 'rock';
        $say_them = 'it';
      }

      if(count($itemlist) > 0)
      {
        sort($itemlist);

        $items = 0;
        $listtext = '';
        foreach($itemlist as $itemname)
        {
          add_inventory($user['user'], '', $itemname, 'Recovered by accident!', $this_inventory['location']);

          $items++;

          if($items > 1)
            $listtext .= ($items == count($itemlist) ? ' and ' : ', ');

          $listtext .= $itemname;
        }

        $message .= '<p><i>(You receive ' . $listtext . '.)</i></p>';
      }

      break;
    }

  case 8: // do nothing
    $message = '<p>The wand attempts to make you a cup of tea.</p><p>It fails.</p>';
    break;

  case 9: // summon a Sugar
    $message = '
      <p>The wand begins the incantations to conjur a jar of Honey, but gets distracted half-way through.  The spell fizzles, resulting in a small lump of plain Sugar.</p>
      <p><i>(You receive Sugar...)</i></p>
    ';
    add_inventory($user['user'], '', 'Sugar', 'Summoned by a Wand of Blunders.', $this_inventory['location']);
    break;

  case 10: // quote a recent profile comment; if there are none, do action 11 instead
    $command = 'SELECT comment FROM psypets_profilecomments WHERE timestamp>' . ($now - 24 * 60 * 60) . ' ORDER BY RAND() LIMIT 1';
    $comment = $database->FetchSingle($command, 'fetching recent profile comment');

    if($comment !== false)
    {
      $dialog = '
        <p>I saw an Oscar Wilde play once!  I\'ll recite my favorite bit for you!</p><p>*ahem*</p>
        <p style="margin-left: 2em; font-style: italic;">' . ucfirst($comment['comment']) . '</p>
        <p>It was something like that, anyway.</p>
        <p>Brilliant man, Oscar Wilde.  Brilliant man.</p>
      ';
      
      break;
    }

  case 11: // summon a Dull Edge
    $dialog = '
      <p>I stashed away quite a few swords during the course of my lifetime, and believe me, I\'ve been around for quite some time!</p>
      <p>I\'d like you to have one!  I\'ll draw out the best one I have!  It\'s been resting for over 1000 years, and it\'s about time it was restored to the light of day and the glory of the battlefield!</p>
    ';
    $message = '<p><i>(You receive...</p><p>... a Dull Edge.  It really must have been sitting there for over 1000 years >_>)</i></p>';

    add_inventory($user['user'], '', 'Dull Edge', 'Retrieved by a Wand of Blunders.', $this_inventory['location']);
    break;

  case 12:
    $dialog = '<p>I may not look like it, but I\'m really a skilled painter.</p><p>Hey, I know!  Let\'s repaint the outside of your house!</p>';
    $message = '<p><i>(Your profile background has been changed!)</i></p>';
    
    $command = 'UPDATE monster_users SET profile_wall=\'walls/paint_brown.png\',profile_wall_repeat=\'horizontal\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'changing your profile background');
    break;

  case 13:
    $message = '<p>The wand looks like it\'s about to speak, stops, then makes a noise like it\'s choking on something.</p><p>After a moment it coughs up a Rupee!</p>';

    $dialog2 = '
      <p>Ugh!  *cough*  Where\'d that come from!</p>
      <p>Blegh!  Geh!</p>
      <p>Anyway... oh!  Right!  I was going to tell you something!</p><p>...</p><p>I forgot what I was going to tell you!</p><p>Sorry!  Nevermind then!  It must not have been important!  I\'ll remember it later, anyway!  It\'ll just randomly pop into my head again!  You know how that happens.</p><p>...</p><p>Really, though: how\'d I get that Rupee thing stuck in me?  Gah.  I still feel a little messed up from it.  Does my voice sound alright?  I feel likt it\'s kind of scratchy-sounding... maybe I should get a drink of water, or something...</p>';

    $command = 'UPDATE monster_users SET rupees=rupees+1 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating rupee count');
    $user['rupees']++;

    break;

  case 14:
    $dialog = '<p>Someone told me you liked Flours, so I got you this!</p>';
    $message = '<p><i>(You received Flour >_>)</i></p>';

    add_inventory($user['user'], '', 'Flour', 'A present from a Wand of Blunders.', $this_inventory['location']);
    break;

  case 15:
    $dialog = '<p>I think... I think something\'s happening to me!  I feel kind of... strange.  Like... some kind of force or energy is flowing into me!</p><p>I think... this might be goodbye!</p><p>I know... we didn\'t get to spend... a lot of time together... but it was fun... while it lasted!</p><p>Au revoir! ... Sayonara! ... Good...bye!</p>';
    $message = '<p><i>(The Wand of Blunders transforms into a Wand of Wonder!)</i></p>';

    $command = 'UPDATE monster_inventory SET itemname=\'Wand of Wonder\',changed=' . $now . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'transforming wand of blunders into a wand of wonder');
    
    $AGAIN_WITH_SAME = false;
    $AGAIN_WITH_ANOTHER = true;
    break;
}

if($dialog != '')
{
  echo '<img src="gfx/npcs/wand_of_blunders.png" align="right" height="64" width="64" alt="(Wand of Blunders)" />';
  include 'commons/dialog_open.php';
  echo $dialog;
  include 'commons/dialog_close.php';
}

echo $message;

if($dialog2 != '')
{
  echo '<img src="gfx/npcs/wand_of_blunders.png" align="right" height="64" width="64" alt="(Wand of Blunders)" />';
  include 'commons/dialog_open.php';
  echo $dialog2;
  include 'commons/dialog_close.php';
}
?>
