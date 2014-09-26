<?php
if($okay_to_be_here !== true)
  exit();

function list_items($loc)
{
  global $STATE, $messages;

  if($STATE['axe'] == $loc)
    $messages[] = '<p>There is a Troll-Slaying Axe lying around here.  This surprises you.  If it was <em>your</em> Troll-Slaying Axe, you\'d keep it more secured, so that someone who happened to find a key to your house in the bushes and subsequently used that key to enter your house without your permission couldn\'t just TAKE it from you.</p>';
  
  if($STATE['treasure'] == $loc)
    $messages[] = '<p>A Gold Widget rests peacefully on the floor.  (This is merely an instance of personification.  The Gold Widget is not actually alive.)</p>';
  
  if($loc == 4)
  {
    if($STATE['troll'] == 'alive')
      $messages[] = '<p>A nasty-looking troll is standing on the path, just ahead of you.  He has a look on his face that says, "anyone who tries to pass me, I will almost certainly eat," and as if in defiance of the unbelievability of a troll\'s presence in this otherwise ordinary world, his face is <em>very</em> believable.</p><p>All that being said, you can still try to go on past it, if you like.  It may be a bit of a spoiler to say this, but you cannot die in this game.</p>';
    else
      $messages[] = '<p>A troll lies here, split in twain (an impressive feat, if you\'ll remember).</p>';
  }
}

function win_the_game()
{
  global $STATE, $said_location, $messages, $user;
  
  $messages[] = '<h4>The End of The Game</h4>';
  
  $message = '<p>It\'s a sad but true fact that your adventure is now at an end.</p>';

  if($STATE['haswon'] != 'yes')
  {
    $message .= '<p>You fall up out of the pool - for you are indeed falling <em>up</em>, at least for a brief moment before gravity catches up with you - and then back down, into a shallow puddle beneath you.</p>' .
                '<p>You stand up and take a look around, realizing that the scenery is familiar to you:  you are standing outside the HERG laboratory building in the small town referred to informally by its residents - of whom you are a member - as "PsyPettia".</p>' .
                '<p>Brushing yourself off, you realize that you still have the three items you were carrying in the game with you: the Skeleton Key, Troll-Slaying Axe, and Golden Widget.</p>' .
                '<p>Being a bit dirty despite the brushing-off, you return home for a shower.</p>' .
                '<p style="text-align: center;">- = - = o = - = -</p>';
               
    add_inventory($user['user'], 'u:3258', 'Skeleton Key', 'Found inside The Glowing Pool', 'storage/incoming');
    add_inventory($user['user'], 'u:3258', 'Troll-Slaying Axe', 'Found inside The Glowing Pool', 'storage/incoming');
    add_inventory($user['user'], 'u:3258', 'Golden Widget', 'Found inside The Glowing Pool', 'storage/incoming');
    flag_new_incoming_items($user['user']);
  }

  $message .= '<p>If you\'d like to play again, you should be happy to know that the game is about to be automatically restarted, returning you to The Woods'; 

  if($STATE['haswon'] != 'yes')
    $message .= ', however you should be aware that subsequent play sessions will not award you with additional items, at least not from this copy of the game'; 

  $message .= '.  Whether or not you play again, I hope you had fun.  And good luck in all your adventures!</p>';
  
  $messages[] = $message;

  $STATE = array();

  $STATE['location'] = '1';
  $STATE['key'] = '0';
  $STATE['axe'] = '5';
  $STATE['treasure'] = '7';
  $STATE['troll'] = 'alive';
  $STATE['haswon'] = 'yes';

  move_to(1);
}

function move_to($loc)
{
  global $STATE, $DESC, $messages, $said_location;

  $STATE['location'] = $loc;

  $messages[] = $DESC[$loc][0];
  $said_location = true;

  if($STATE['beento:' . $loc] != 'yes')
  {
    $STATE['beento:' . $loc] = 'yes';
    $messages[] = $DESC[$loc][1];
  }

  list_items($loc);
}

function take($item, $silently = false)
{
  global $STATE, $messages;

  $STATE[$item] = 'me';
  
  if(!$silently)
    $messages[] = '<p>Taken.</p>';
}

$DESC = array(
  1 => array('<h4>The Woods</h4>', '<p>You are in the woods.  It is a very pretty woods, with beech and maple trees, but don\'t worry: those details are not important to your adventure here today.<p><p>There is some kind of hut to the east, and a cave to the north.  You hear a river to the west, providing atmosphere, and also another location to visit.</p>'),
  2 => array('<h4>The River</h4>', '<p>You are on the west bank of a river.  You cannot ford the river, or jump over it, or anything else, so don\'t bother trying.  The only direction you can go is east, deeper into the woods</p><p>Some very conspicuous bushes litter the vicinity, which is not to say you can pick them up for recycling or trash, just that they might be deserving of further attention.</p>'),
  3 => array('<h4>Outside The Hut</h4>', '<p>You are standing outside a logger\'s hut.  Usually you can only move directions such as NORTH or WEST, but in this case, if you wanted, you could move IN to the hut (and later, OUT of it).</p><p>You can tell it\'s a logger\'s hut on account of all the logs lying around.  As for the logger, he is no where to be seen.</p>'),
  4 => array('<h4>The Dirt Path</h4>', '<p>You are standing on a dirt path which runs south, into the woods, and north, into a cave.</p>'),
  5 => array('<h4>Inside The Hut</h4>', '<p>You are inside a small hut, which means the only way you can go is OUT again.</p><p>There is a bed, and a chair, and a table, and probably even a fireplace (you know, for burning all the logs).  That being said, while these furnishings add to the realism of the scene, any attempt to intract with them would disrupt the game-world immersion experience.  It is therefore recommended you leave them alone.</p>'),  
  6 => array('<h4>Just Inside The Cave</h4>', '<p>You stand just inside a cave.  Light streams in from the opening at the south, illuminating both a spiral staircase leading DOWN, and a small passage to the north.</p>'),
  7 => array('<h4>The Treasure Room</h4>', '<p>It is a sad but true fact that some locations in text adventure games lack any descriptive features of their own, being defined entirely by the objects which, at the start of the game, lie within them.  These kinds of rooms are probably visited by players only once, who remove said item or items, leave, and never LOOK at it again.</p><p>This is such a room.</p>'),
  8 => array('<h4>The Cave, B1</h4>', '<p>If this cave had floors, or stories, like a building, this would be the B1 floor.</p><p>The spiral staircase continues both up and down.</p>'),
  9 => array('<h4>The Cave, B2</h4>', '<p>You are now two "floors" below the surface-level of the cave.  The stairs continue up and down.</p><p>You can see a small glowing pool of light below you.  This conveniently explains how you are able to still see at this depth, and also provides a hint as to what\'s below.'),
  10 => array('<h4>The Cave, B3 (The Glowing Pool)</h4>', '<p>Not to distract from the spiral staircase which offers an escape UPward, but before you lies a pool of shimmering beauty the likes of which you\'ve never seen.  (You\'ll have to trust my word on this, since graphics are not part of this game.)  It gives off a radiant light which fills the entire room, and possibly even your soul with a sense of accomplishment.</p><p>And that\'s not the last of it!</p><p>Even without looking closely at this pool - which you may still feel free to do so, if you like - you can see at its bottom not a rocky floor, but the sky, and clouds!  It seems to be a reasonable conclusion that this pool is a kind of portal!</p>'),  
  11 => array('<h4>In The Glowing Pool</h4>', '<p>You are now wading in the glowing pool.  Below you is the sky, as if it were placed there for the sole purpose of disorienting you, or possibly so that the creator of this game could say, since you must enter this pool and then explicity go DOWN into it, "See?  It has 11 rooms, which is more than 10, as promised!"'),
);

if(strlen($this_inventory['data']) == 0)
{
  $this_inventory['data'] = 'key=0;axe=5;treasure=7;troll=alive';
  $initializing = true;
}

$infos = explode(';', $this_inventory['data']);

foreach($infos as $info)
{
  list($key, $value) = explode('=', $info);

  $STATE[$key] = $value;
}

if($initializing === true)
{
  $messages[] = '<p>Since you haven\'t played before, allow me a moment to explain this game to you.</p><p>This is a "text adventure" game.  This means that everything is text, and you have to do some reading.  Also, you have to type what you want to do, such as "GO NORTH" (or simply "NORTH" or "N", if you\'re feeling lazy), "LOOK", "LOOK AT BUSHES", "TAKE AXE", or "KILL TROLL".  (Yes, this game features a troll.)</p><p>Due to all the descriptive texts, these kinds of games are heralded as being very immersive.  Like a book.</p><p>This game features over 10 locations, approximately 4 interactive items, and one troll (which you may or may not count as being an interactive item), so you\'re bound to have at least a minor adventure, and hopefully a good amount of fun.</p><p>If you need help at any time, type "HELP".</p><p>Oh, and even though all the commands were given in all capitals (in the tradition of old text adventure games), you do not have to hold down Shift all the time, or engage your Caps Lock, for this game.  Lowercase is fine.</p><p>Now go forth!  Puzzles await!</p>';

  move_to(1);
}

$command = strtolower(trim($_POST['command']));

$understood = true;
$no_go = false;

$location = (int)$STATE['location'];

// COMMAND PRE-PROCESSING (distills synonymous commands)

if(substr($command, 0, 5) == 'l at ')
  ; // no change
else if(substr($command, 0, 8) == 'look at ')
  $command = 'l at ' . substr($command, 8);
else if(substr($command, 0, 8) == 'examine ')
  $command = 'l at ' . substr($command, 8);
else if(substr($command, 0, 2) == 'x ')
  $command = 'l at ' . substr($command, 2);

else if(substr($command, 0, 5) == 'take ')
  ; // no change
else if(substr($command, 0, 4) == 'get ')
  $command = 'take ' . substr($command, 4);
else if(substr($command, 0, 6) == 'steal ')
  $command = 'take ' . substr($command, 6);

else if(substr($command, 0, 7) == 'attack ')
  ; // no change
else if(substr($command, 0, 5) == 'kill ')
  $command = 'attack ' . substr($command, 5);

// PROCESS COMMAND

if($command == 'look' || $command == 'l')
{
  $messages[] = $DESC[$location][1];

  list_items($location);
}
else if($command == 'north' || $command == 'n' || $command == 'go north' || $command == 'go n')
{
  if($location == 1)
    move_to(4);
  else if($location == 3)
    $messages[] = '<p>Sorry, I didn\'t mean to deceive you, but you can\'t actually go NORTH here.  WEST and IN are the only options.</p>';
  else if($location == 4)
  {
    if($STATE['troll'] == 'alive')
      $messages[] = '<p>The nasty-looking troll will not let you pass so easily, the bastard.</p><p>If only you had some kind of weapon, you could ATTACK it...</p>';
    else
      move_to(6);
  }
  else if($location == 6)
    move_to(7);
  else
    $no_go = true;
}
else if($command == 'east' || $command == 'e' || $command == 'go east' || $command == 'go e')
{
  if($location == 1)
    move_to(3);
  else if($location == 2)
    move_to(1);
  else
    $no_go = true; 
}
else if($command == 'south' || $command == 's' || $command == 'go south' || $command == 'go s')
{
  if($location == 4)
    move_to(1);
  else if($location == 6)
    move_to(4);
  else if($location == 7)
    move_to(6);
  else
    $no_go = true; 
}
else if($command == 'west' || $command == 'w' || $command == 'go west' || $command == 'go w')
{
  if($location == 1)
    move_to(2);
  else if($location == 3)
    move_to(1);
  else
    $no_go = true; 
}
else if($command == 'up' || $command == 'go up')
{
  if($location == 8)
    move_to(6);
  else if($location == 9)
    move_to(8);
  else if($location == 10)
    move_to(9);
  else
    $no_go = true; 
}
else if($command == 'down' || $command == 'go down')
{
  if($location == 6)
    move_to(8);
  else if($location == 8)
    move_to(9);
  else if($location == 9)
    move_to(10);
  else if($location == 11)
    win_the_game();
  else
    $no_go = true;
}
else if($command == 'in' || $command == 'inside' || $command == 'go in' || $command == 'go inside')
{
  if($location == 3)
  {
    if($STATE['key'] == 'me')
    {
      $messages[] = '<p>The door to the hut is locked, but that\'s OK.  You use the Skeleton Key to get in.  Amazingly, the Skeleton Key does not crumble to dust, and so you pocket it again as you step inside.</p>';
      move_to(5);
    }
    else
      $messages[] = '<p>The door to the hut is locked.  You will need a key to get in.</p>';
  }
  else if($location == 10)
  {
    if($STATE['treasure'] == 'me')
      move_to(11);
    else
      $messages[] = '<p>I feel that it is only fair of me to warn you that while you <em>could</em> jump into the pool at this time, there is still a piece of treasure remaining in this game that you have not yet acquired.</p>';
  }
  else
    $no_go = true;
}
else if($command == 'out' || $command == 'outside' || $command == 'go out' || $command == 'go outside')
{
  if($location == 5)
    move_to(3);
  else if($location == 11)
    move_to(10);
  else if($location == 6)
    move_to(4);
  else
    $no_go = true;
}
else if(substr($command, 0, 5) == 'l at ')
{
  $item = substr($command, 5);

  if(($item == 'axe' || $item == 'troll-slaying axe') && ($STATE['axe'] == $location || $STATE['axe'] == 'me'))
    $messages[] = '<p>This is quite a formidable-looking axe.  With an axe like this, you could probably expect to ATTACK a troll, and win to tell the tale, impressing all the girls and/or guys (your preference).</p>';
  else if(($item == 'widget' || $item == 'gold widget') && ($STATE['treasure'] == $location || $STATE['treasure'] == 'me'))
    $messages[] = '<p>If you had to guess (and you have to, having no appraisal skills to speak of in this game), you\'d say this Widget is worth <em>at least</em> a jillion moneys.  It is probably worth much less.</p>';
  else if(($item == 'key' || $item == 'skeleton key') && ($STATE['key'] == $location || $STATE['key'] == 'me'))
    $messages[] = '<p>This is one of those standard Skeleton Keys which opens a door, treasure chest, or some other locked item, and then promptly and inexplicably crumbles to dust after use.</p>';
  else if(($item == 'troll' || $item == 'nasty-looking troll' || $item == 'bastard' || $item == 'bastard troll' || $item == 'nasty-looking troll' || $item == 'nasty-looking bastard troll' || $item == 'bastard nasty-looking troll') && $location == 4)
  {
    if($STATE['troll'] == 'alive')
      $messages[] = '<p>There\'s not much more to say about the ' . $item . ', really.</p>';
    else
      $messages[] = '<p>The ' . $item . ' is quite dead.  It\'s probably bleeding all over the place, looking very gross, so let\'s not think much more on it than we have to.</p>';
  }
  else if(($item == 'bushes' || $item == 'bush' || $item == 'conspicuous bushes' || $item == 'conspicuous-looking bushes') && $location == 2)
  {
    if($STATE['key'] != 'me')
    {
      take('key', true);
      $messages[] = '<p>Rummaging around the bushes reveals a Skeleton Key!  You pocket it, proud of your find.</p><p>By the way, you can check your inventory at any time by typing "INVENTORY" or "INV" or even just "I".  You can also "LOOK AT KEY" or other things you\'re carrying at any time.</p>';
    }
    else
      $messages[] = '<p>You\'ve already robbed the bushes of their treasure.  They have nothing left to give you.</p>';
  }
  else if(($item == 'pool' || $item == 'glowing pool' || $item == 'shimmering pool' || $item == 'portal') && $location == 10)
  {
    if($STATE['treasure'] == 'me')
      $messages[] = '<p>Don\'t be timid.  Get IN.</p>';
    else
      $messages[] = '<p>I feel that it is only fair of me to warn you that while you <em>could</em> get IN the pool at this time, there is still a piece of treasure remaining in this game that you have not yet acquired.</p>';
  }
  else if(($item == 'hut' || $item == 'logger\'s hut') && $location == 3)
    $messages[] = '<p>You have seen a logger\'s hut before, right?  This one looks exactly like any other, with a door IN and everything.</p>';
  else if($item == 'river' && $location == 2)
    $messages[] = '<p>The river is flowing gently, as rivers in these kinds of places do.</p><p>You\'re still thinking about how to get across, aren\'t you?  Seriously, though, you can\'t.  Banish the thought.</p>';
  else
    $messages[] = '<p>You don\'t see one of those around.</p>';
}
else if(substr($command, 0, 5) == 'take ')
{
  $item = substr($command, 5);
  
  if(($item == 'axe' || $item == 'troll-slaying axe') && $STATE['axe'] == $location)
    take('axe');
  else if(($item == 'widget' || $item == 'gold widget') && $STATE['treasure'] == $location)
    take('treasure');
  else if(($item == 'troll' || $item == 'nasty-looking troll' || $item == 'bastard' || $item == 'bastard troll' || $item == 'nasty-looking troll' || $item == 'nasty-looking bastard troll' || $item == 'bastard nasty-looking troll') && $location == 4)
  {
    if($STATE['troll'] == 'alive')
      $messages[] = '<p>The ' . $target . ' does not want to be taken, and believe me, it has a very strong say in the matter.</p>';
    else
      $messages[] = '<p>Gross.  No.  Stop it.  I know you\'re supposed to pick stuff up in text adventures, but really?  Here, how about this:</p><p>You are not strong enough to carry the dead, bleeding troll that you shouldn\'t want to carry anyway.  Sorry.';
  }
  else
    $messages[] = '<p>You don\'t see one of those around to take.</p>';
}
else if(substr($command, 0, 7) == 'attack ')
{
  $target = substr($command, 7);
  
  if($target == 'troll' || $target == 'nasty-looking troll' || $target == 'bastard' || $target == 'bastard troll' || $target == 'nasty-looking troll' || $target == 'nasty-looking bastard troll' || $target == 'bastard nasty-looking troll')
  {
    if($location == 4)
    {
      if($STATE['troll'] == 'alive')
      {
        if($STATE['axe'] == 'me')
        {
          $messages[] = '<p>You swing the axe with all your might at the unsuspecting ' . $target . ', cleaving it in twain, a difficult feat considering its hideous disregard for symmetry.</p><p>You are perhaps more skilled with axes than given credit for.  Have you considered lumberjacking as a profession?</p>';
          $STATE['troll'] = 'dead';
        }
        else
          $messages[] = '<p>You swing at the troll with your fists, and hit!</p><p>The troll laughs before shoving you away.</p><p>Then it laughs some more.</p>';
      }
      else
        $messages[] = '<p>The ' . $target . ' is quite dead.  It\'s probably bleeding all over the place, looking very gross, so let\'s not think much more on it than we have to.</p>';
    }
    else
      $message[] = '<p>There is no ' . $target . ' here.  Or if there is, it\'s not an enemy.</p>';
  }
  $message[] = '<p>There is no ' . $target . ' here.  Or if there is, it\'s not an enemy.</p>';
}
else if($command == 'take' || $command == 'get' || $command == 'steal' || $command == 'attack' || $command == 'kill' || $command == 'l at' || $command == 'look at' || $command == 'examine' || $command == 'x')
  $messages[] = '<p>' . ucfirst($command) . '?  ' . ucfirst($command) . ' what?</p>';
else if($command == 'help')
  $messages[] = '<p>Stuck?  Here are some things to keep in mind with text adventures such as this one:</p><ul><li><p>To move, type "MOVE NORTH", or just "NORTH", or just "N".  You can move in any of the cardinal directions, and in some places "IN", "OUT", "UP", or "DOWN".</p></li><li><p>Look at stuff!  You can type just "LOOK" to see a description of the area in general, or "LOOK AT BUSH", "LOOK AT WIDGET" or other items you see.  Doing so will often-times yield clues!  Lazy people type "L AT" instead of "LOOK AT".</p></li><li><p>You can try to TAKE items, for example "TAKE AXE".  Some text adventures also let you DROP things, but not this one.</p></li><li><p>To see what you are carrying, type "INVENTORY", "INV" or "I".</p></li></ul><p>Note that text adventures tend not to bother with articles ("a", "an", "the"), so you should leave them out of your commands.<p>If you\'re still stuck, try asking around The Plaza.  Someone else will probably have figured it out, or even posted a walk-through.</p>'; 
else if($command == 'i' || $command == 'inv' || $command == 'inventory')
{
  $message = '';

  if($STATE['key'] == 'me')
    $message .= '<li>Skeleton Key</li>';

  if($STATE['axe'] == 'me')
    $message .= '<li>Troll-Slaying Axe</li>';

  if($STATE['treasure'] == 'me')
    $message .= '<li>Golden Widget</li>';

  if($message != '')
    $message = '<p>You are carrying the following:</p><ul>' . $message . '</ul>';
  else
    $message = '<p>You are not carrying anything.</p>';

  $messages[] = $message;
}
else if($command != '')
  $understood = false;

if(!$understood)
  $messages[] = '<p>I hate to break the fourth wall on you here, but the command "' . strtoupper($command) . '" is not understood.</p><p>If you\'re feeling stuck, don\'t forget that you can type "HELP" for help at any time.</p>'; 

if($no_go)
  $messages[] = '<p>Unfortunately, you cannot move in that direction.</p>';

if($said_location !== true)
  echo $DESC[$location][0];

if(count($messages) > 0)
{
  foreach($messages as $message)
    echo $message;
}

foreach($STATE as $key=>$value)
  $datas[] = $key . '=' . $value;

$command = 'UPDATE monster_inventory SET data=' . quote_smart(implode(';', $datas)) . ' WHERE idnum=' .
           $this_inventory['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'updating game state');
?>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p><input name="command" id="command" /> <input type="submit" value="Enter" /></p>
</form>
<script type="text/javascript">
document.getElementById('command').focus();
</script>
