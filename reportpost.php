<?php
$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

$postid = (int)$_GET['id'];
$comment = trim($_POST['comment']);

$command = 'SELECT threadid,body FROM monster_posts WHERE idnum=' . $postid . ' LIMIT 1';
$data = $database->FetchSingle($command, 'fetching post data');

if($data === false)
{
  header('Location: ./plaza.php');
  exit();
}

$command = '
  INSERT INTO psypets_abusereports
  (
    timestamp,
    type,
    threadid,
    reporter,
    comment,
    original_text
  )
  VALUES
  (
    ' . $now . ',
    \'post\',
    ' . $postid . ',
    ' . $user['idnum'] . ',
    ' . quote_smart($comment) . ',
    ' . quote_smart($data['body']) . '
  )
';
$database->FetchNone($command, 'submitting report');

mail($SETTINGS['author_email'], 'PsyMail notification: a Plaza post has been reported for abuse!', 'original text: ' . $data['body'], "MIME-Version: 1.0\nContent-type: text/html; charset=utf-8\nFrom: " . $SETTINGS['site_mailer']);

$threadid = $data['threadid'];

$command = 'SELECT COUNT(*) AS c FROM monster_posts WHERE threadid=' . $threadid . ' AND idnum<' . $postid;
$data = $database->FetchSingle($command, 'fetching thread data');

$page = floor($data['c'] / 20) + 1;
if($page < 1)
  $page = 1;

header('Location: ./viewthread.php?threadid=' . $threadid . '&page=' . $page . '&msg=100');
?>
