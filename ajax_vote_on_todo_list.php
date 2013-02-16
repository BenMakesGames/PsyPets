<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/wishlist.php';

$item_id = (int)$_GET['itemid'];
$new_vote = (int)$_GET['vote'];

$vote = get_todo_list_vote($item_id, $user['idnum']);

if($new_vote == 0)
{
  if($vote !== false)
    delete_todo_list_vote($item_id, $user['idnum']);

  $my_new_vote = 0;
}
else if($new_vote == -1 || $new_vote == -2 || $new_vote == 1 || $new_vote == 2)
{
  if($vote !== false)
  {
    if($vote['vote'] != $new_vote)
      update_todo_list_vote($item_id, $user['idnum'], $new_vote);
  }
  else
    create_todo_list_vote($item_id, $user['idnum'], $new_vote);

  $my_new_vote = $new_vote;
}
else
{
  echo 'nd';
  $my_new_vote = false;
}

if($my_new_vote !== false)
{
  echo
    $item_id, "\n",
    ($my_new_vote == 2 ? '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/yeah.gif" alt="want!" class="inlineimage" />' : '<a href="#" onclick="todo_vote(' . $item_id . ', 2); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/yeah.gif" alt="want!" class="transparent_image inlineimage" /></a>'), ' ',
    ($my_new_vote == 1 ? '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/hee.gif" alt="yes, please." class="inlineimage" />' : '<a href="#" onclick="todo_vote(' . $item_id . ', 1); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/hee.gif" alt="yes, please." class="transparent_image inlineimage" /></a>'), ' ',
    ($my_new_vote == 0 ? '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/neutral.png" alt="whatever." class="inlineimage" />' : '<a href="#" onclick="todo_vote(' . $item_id . ', 0); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/neutral.png" alt="whatever." class="transparent_image inlineimage" /></a>'), ' ',
    ($my_new_vote == -1 ? '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/mmf.gif" alt="no, thank you." class="inlineimage" />' : '<a href="#" onclick="todo_vote(' . $item_id . ', -1); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/mmf.gif" alt="no, thank you." class="transparent_image inlineimage" /></a>'), ' ',
    ($my_new_vote == -2 ? '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/grr.png" alt="super bad!" class="inlineimage" />' : '<a href="#" onclick="todo_vote(' . $item_id . ', -2); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/grr.png" alt="super bad!" class="transparent_image inlineimage" /></a>')
  ;
}
?>