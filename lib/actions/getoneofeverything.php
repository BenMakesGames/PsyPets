<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/adventurelib.php';
require_once 'commons/questlib.php';
	
$AGAIN_WITH_ANOTHER = true;

$challenge_tokens = get_challenge_tokens($user['idnum']);
if($challenge_tokens === false)
{
  create_challenge_tokens($user['idnum']);
  $challenge_tokens = get_challenge_tokens($user['idnum']);
  if($challenge_tokens === false)
    die('error loading and/or creating adventure tokens.  this is bad.');
}

delete_inventory_byid($this_inventory['idnum']);

$challenge_tokens['plastic']++;
  
update_challenge_tokens($challenge_tokens);

$database->FetchNone('
	UPDATE monster_users
	SET
		stickers_to_give=stickers_to_give+1,
		rupees=rupees+1,
		money=money+1,
		greenhouse_points=greenhouse_points+1
	WHERE idnum=' . $user['idnum'] . '
	LIMIT 1
');

$extra = '';

if($user['stickers_to_give'] + 1 >= 50)
{
  $badges = get_badges_byuserid($user['idnum']);
  if($badges['starhoarder'] == 'no')
  {
    set_badge($user['idnum'], 'starhoarder');
    $extra = '<p><i>(You received the Gold Star Sticker Hoarder Badge for having 50 or more Gold Stars at once.)</i></p>';
  }
}
?>
<p>You receive one of each of the following:</p>
<ul>
 <li>A Gold Star</li>
 <li>A Rupee</li>
 <li>A Moneys</li>
 <li>A Greenhouse point</li>
 <li>A Plastic Token</li>
</ul>
<?= $extra ?>
<p>Then, the <?= $this_inventory['itemname'] ?> shatters!  (Typical!)</p>
