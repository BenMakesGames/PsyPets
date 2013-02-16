<?php
$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/fireworklib.php';

$postid = (int)$_GET['postid'];
$fireworkid = (int)$_GET['firework'];

$command = "SELECT background FROM monster_posts WHERE idnum=$postid LIMIT 1";
$post = $database->FetchSingle($command, 'giveastar.php');

if($post === false)
{
  Header("Location: ./plaza.php");
  exit();
}

if($post['background'] > 0 || $user['fireworks'] == '')
{
  header('Location: ./jumptopost.php?postid=' . $postid);
  exit();
}

$supply = get_firework_supply($user);

if(array_key_exists($fireworkid, $supply))
{
  expend_firework($supply, $fireworkid);

  $command = 'UPDATE monster_users SET fireworks=' . quote_smart(render_firework_data_string($supply)) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'removing firework from player');

  $command = 'UPDATE monster_posts SET background=' . $fireworkid . ' WHERE idnum=' . $postid . ' LIMIT 1';
  $database->FetchNone($command, 'adding firework to post');
}

header('Location: ./jumptopost.php?postid=' . $postid);
?>
