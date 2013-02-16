<?php
$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

$threadid = (int)$_GET['threadid'];

$command = 'SELECT * FROM monster_watching ' .
           'WHERE `user`=' . quote_smart($user['user']) . ' ' .
           'AND threadid=' . $threadid . ' LIMIT 1';
$thread_watch = $database->FetchSingle($command, 'fetching thread\'s last view date');

if($thread_watch === false)
{
  header('Location: ./viewthread.php?threadid=' . $threadid);
  exit();
}

$command = 'SELECT idnum FROM monster_posts WHERE threadid=' . $threadid . ' AND creationdate>' . $thread_watch['lastread'] . ' ORDER BY idnum ASC LIMIT 1';
$post = $database->FetchSingle($command, 'fetching post');

$command = 'SELECT COUNT(*) AS c FROM monster_posts WHERE threadid=' . $threadid . ' AND creationdate<=' . $thread_watch['lastread'];
$data = $database->FetchSingle($command, 'fetching thread data');

$page = floor($data['c'] / 20) + 1;
if($page < 1)
  $page = 1;

header('Location: ./viewthread.php?threadid=' . $threadid . '&page=' . $page . '#p' . $post['idnum']);
?>
