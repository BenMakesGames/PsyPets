<?php
$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

$postid = (int)$_GET['postid'];

$command = 'SELECT threadid FROM monster_posts WHERE idnum=' . $postid . ' LIMIT 1';
$data = $database->FetchSingle($command, 'fetching post data');

if($data === false)
{
  header('Location: ./plaza.php');
  exit();
}

$threadid = $data['threadid'];

$command = 'SELECT COUNT(*) AS c FROM monster_posts WHERE threadid=' . $threadid . ' AND idnum<' . $postid;
$data = $database->FetchSingle($command, 'fetching thread data');

$page = floor($data['c'] / 20) + 1;
if($page < 1)
  $page = 1;

header('Location: ./viewthread.php?threadid=' . $threadid . '&page=' . $page . '#p' . $postid);
?>
