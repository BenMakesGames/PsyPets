<?php
$require_petload = 'no';

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';

$id = (int)$_GET["id"];

$this_post = $database->FetchSingle("SELECT * FROM monster_posts WHERE idnum=$id LIMIT 1");

if($this_post === false)
{
  header('Location: /viewplaza.php');
  exit();
}

if($this_post['egg'] == 'none' || $this_post['egg'] == 'taken')
{
  header('Location: /jumptopost.php?postid=' . $this_post['idnum']);
  exit();
}
else
{
  $itemname = ucfirst($this_post['egg']) . '-Dyed Egg';
  add_inventory($user['user'], $SETTINGS['site_ingame_mailer'], $itemname, 'You found this egg!', 'storage/incoming');

  $database->FetchNone("UPDATE monster_posts SET egg='taken' WHERE idnum=$id LIMIT 1");

  header('Location: /jumptopost.php?postid=' . $this_post['idnum']);
  exit();
}
?>