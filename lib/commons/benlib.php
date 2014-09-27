<?php
$RATINGS = array(
  'pets' => array(
    'yesgood' => array(10, 'I interact with my pets, and enjoying doing so'),
    'yesok' => array(5, 'I interact with my pets, but it\'s kind of a chore'),
    'nook' => array(-5, 'I interact with them only sometimes; mostly I leave them alone'),
    'nobad' => array(-10, 'I don\'t interact with my pets at all, because I feel it\'s a complete waste of time'),
    'na' => array(0, 'No Comment'),
  ),
  'community' => array(
    'yesgood' => array(10, 'People are generally nice and helpful'),
    'yesok' => array(5, 'People are generally nice and helpful, but sometimes fighting ruins it'),
    'yesbad' => array(-5, 'There are nice and helpful people, but they are few and far between '),
    'nobad' => array(-10, 'I avoid interacting with the community due to all the anger and hatred'),
    'na' => array(0, 'I avoid interacting with the community, for no particular reason / No Comment'),
  ),
  'gameplay' => array(
    'funyes' => array(10, 'It\'s a fun game, and there is plenty to do'),
    'funno' => array(5, 'It\'s a fun game, but there\'s not enough to do'),
    'overwhelming' => array(-7, 'The game is overwhelming; there is too much to do and learn!'),
    'lostinterest' => array(-5, 'The game used to be fun, but it\'s not anymore (but I\'m still here for some reason)'),
    'boredorannoyed' => array(-2, 'The game is not fun; it has never been fun (but I\'m still here for some reason)'),
    'na' => array(0, 'No Comment'),
  ),
);

function rate_graphic($rating)
{
  if($rating <= -7)
    echo '<img src="//saffron.psypets.net/gfx/emote/skull.png" alt="-" width="16" height="16" /> <img src="//saffron.psypets.net/gfx/emote/skull.png" alt="-" width="16" height="16" /> <img src="//saffron.psypets.net/gfx/emote/skull.png" alt="-" width="16" height="16" />';
  else if($rating <= -4)
    echo '<img src="//saffron.psypets.net/gfx/emote/skull.png" alt="-" width="16" height="16" /> <img src="//saffron.psypets.net/gfx/emote/skull.png" alt="-" width="16" height="16" /> <img src="/gfx/shim.png" alt="" width="16" height="16" />';
  else if($rating <= -1)
    echo '<img src="//saffron.psypets.net/gfx/emote/skull.png" alt="-" width="16" height="16" /> <img src="/gfx/shim.png" alt="" width="16" height="16" /> <img src="/gfx/shim.png" alt="" width="16" height="16" />';
  else if($rating >= 7)
    echo '<img src="//saffron.psypets.net/gfx/emote/hee.gif" alt="+" width="16" height="16" /> <img src="//saffron.psypets.net/gfx/emote/hee.gif" alt="+" width="16" height="16" /> <img src="//saffron.psypets.net/gfx/emote/hee.gif" alt="+" width="16" height="16" />';
  else if($rating >= 4)
    echo '<img src="//saffron.psypets.net/gfx/emote/hee.gif" alt="+" width="16" height="16" /> <img src="//saffron.psypets.net/gfx/emote/hee.gif" alt="+" width="16" height="16" /> <img src="/gfx/shim.png" alt="" width="16" height="16" />';
  else if($rating >= 1)
    echo '<img src="//saffron.psypets.net/gfx/emote/hee.gif" alt="+" width="16" height="16" /> <img src="/gfx/shim.png" alt="" width="16" height="16" /> <img src="/gfx/shim.png" alt="" width="16" height="16" />';
  else // (-1 to 1)
    echo '<img src="/gfx/shim.png" alt="" width="16" height="16" /> <img src="/gfx/shim.png" alt="" width="16" height="16" /> <img src="/gfx/shim.png" alt="" width="16" height="16" />';
}
?>
