<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$database->FetchNone("DELETE FROM monster_inventory WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1");
$database->FetchNone("UPDATE monster_users SET stickers_to_give=stickers_to_give+9 WHERE idnum=" . $user["idnum"] . " LIMIT 1");

if($user['stickers_to_give'] + 9 >= 50)
{
  $badges = get_badges_byuserid($user['idnum']);
  if($badges['starhoarder'] == 'no')
  {
    set_badge($user['idnum'], 'starhoarder');
    $extra = '<p><i>(You received the Gold Star Sticker Hoarder Badge for having 50 or more Gold Stars at once.)</i></p>';
  }
}
?>
<p>You add the 9 Gold Stickers to your stash.</p>
