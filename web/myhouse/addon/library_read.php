<?php
require_once 'commons/init.php';

/*
  reduces max house space by 100
  allows you to store infinite number of books (duplicates allowed)
  add/remove/read copies at will
    disallow reading of books that may destroy themselves
      will have to keep an array of such books within the addon
  show off library in profile
  other features?
    secret trapdoor
    tutor
*/

$whereat = 'home';
$wiki = 'Library_(add-on)';
$THIS_ROOM = 'Library';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/librarylib.php';

if(!addon_exists($house, 'Library'))
{
  header('Location: /myhouse.php');
  exit();
}

$itemid = (int)$_GET['id'];
$page = (int)$_GET['page'];

$book = get_library_book($user['idnum'], $itemid);

if($book === false || in_array($book['itemname'], $FORBIDDEN_BOOKS) || $book['custom'] == 'yes')
{
  header('Location: /myhouse/addon/library.php');
  exit();
}

$action_info = explode(';', $book['action']);

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Library &gt; <?= $book['itemname'] ?> &gt; <?= $action_info[0] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/library.php?page=<?= $page ?>">Library</a> &gt; <?= $book['itemname'] ?> &gt; <?= $action_info[0] ?></h4>
<?php
room_display($house);

$file = LIB_ROOT . '/actions/' . $action_info[1];

if(file_exists($file))
{
  $okay_to_be_here = true;
  require $file;
}
else
  echo 'Error loading item action.  Please notify <a href="/admincontact.php">an administrator</a>.';
?>
     <hr />
     <ul>
      <li><a href="/myhouse/addon/library.php?page=<?= $page ?>">Back to <?= $user['display'] ?>'s Library</a></li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
