<?php
$require_petload = 'no';

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/inventory.php";
require_once "commons/formatting.php";

$command = "SELECT * " .
           "FROM `monster_inventory` " .
           "WHERE `idnum`=" . (int)$_GET["idnum"] . " AND `user`=" . quote_smart($user["user"]) . " LIMIT 1";
$this_inventory = $database->FetchSingle($command, 'readbook.php');

if($this_inventory === false)
{
  Header("Location: ./myhouse.php");
  exit();
}

$command = "SELECT * " .
           "FROM `monster_items` " .
           "WHERE `itemname`=" . quote_smart($this_inventory["itemname"]) . " AND `book_text`!='' ";
$this_book = $database->FetchSingle($command, 'readbook.php');

if($this_book === false)
{
  header("Location: ./myhouse.php");
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $this_book["itemname"] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h5><?= $this_book["itemname"] ?></h5>
     <p>&gt; <a href="javascript:history.go(-1);">Back</a></p>
<?php
 if(substr($this_book["book_text"], 0, 2) == "::")
 {
?>
     <p style="padding-left:16px;"><?php include substr($this_book["book_text"], 2); ?></p>
<?php
 }
 else
 {
?>
     <p style="padding-left:16px;"><?= format_text($this_book['book_text']) ?></p>
<?php
 }
?>
     <p>&gt; <a href="javascript:history.go(-1);">Back</a></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
