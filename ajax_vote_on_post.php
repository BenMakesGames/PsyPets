<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/plazapostvoting.php';

$postid = (int)$_GET['postid'];
$vote = (int)$_GET['vote'];

$command = 'SELECT voted_on FROM monster_posts WHERE idnum=' . $postid . ' LIMIT 1';
$post = $database->FetchSingle($command, 'fetching post');

if($post === false)
  die('post does not exist!');

$post_vote = get_post_vote($postid, $user['idnum']);

if($vote == -1 || $vote == 1)
{
  if($post_vote === false)
    create_post_vote($postid, $user['idnum'], $vote);
  else
    update_post_vote($postid, $user['idnum'], $vote);

  $post_vote['vote'] = $vote;
}

echo $postid, "\n";

if($post_vote === false)
  echo
    '<a href="#" onmouseover="hoveron(\'thumbup' . $postid . '\')" onmouseout="hoveroff(\'thumbup' . $postid . '\')" onclick="thumbsup(' . $postid . '); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/thumbup.png" class="transparent_image" id="thumbup' . $postid . '" /></a>',
    '<a href="#" onmouseover="hoveron(\'thumbdown' . $postid . '\')" onmouseout="hoveroff(\'thumbdown' . $postid . '\')" onclick="thumbsdown(' . $postid . '); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/thumbdown.png" class="transparent_image" id="thumbdown' . $postid . '" /></a>'
  ;
else if($post_vote['vote'] == -1)
  echo
    '<a href="#" onmouseover="hoveron(\'thumbup' . $postid . '\')" onmouseout="hoveroff(\'thumbup' . $postid . '\')" onclick="thumbsup(' . $postid . '); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/thumbup.png" class="transparent_image" id="thumbup' . $postid . '" /></a>',
    '<a href="#" onclick="return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/thumbdown.png" id="thumbdown' . $postid . '" /></a>'
  ;
else if($post_vote['vote'] == 1)
  echo
    '<a href="#" onclick="return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/thumbup.png" id="thumbup' . $postid . '" /></a>',
    '<a href="#" onmouseover="hoveron(\'thumbdown' . $postid . '\')" onmouseout="hoveroff(\'thumbdown' . $postid . '\')" onclick="thumbsdown(' . $postid . '); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/thumbdown.png" class="transparent_image" id="thumbdown' . $postid . '" /></a>'
  ;
?>