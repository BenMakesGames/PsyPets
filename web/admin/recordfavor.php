<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/donationlib.php";
require_once "commons/userlib.php";

if($admin["managedonations"] != "yes")
{
  header('Location: /admin/tools.php');
  exit();
}

$recipient = get_user_byuser($_POST["user"]);

if($recipient["idnum"] == 0)
{
  Header("Location: /admin/newfavor.php?error=nosuchuser&user=" . $_POST["user"]);
  exit();
}

add_favor2($_POST["user"], $_POST["favor"], (int)$_POST["amount"]);

Header("Location: /admin/favors.php");
?>