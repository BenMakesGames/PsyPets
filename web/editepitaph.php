<?php
$whereat = "graveyard";
$wiki = "The_Graveyard";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/userlib.php';
require_once 'commons/gravelib.php';

$tombid = (int)$_GET['id'];

$tombstone = get_tombstone_byid($tombid);

if($tombstone['ownerid'] != $user['idnum'] || $tombstone['tombstone'] == 0)
{
  header('Location: ./graveyard.php');
  exit();
}

if($_POST["action"] == "update")
{
  $_POST["epitaph"] = trim(stripslashes($_POST["epitaph"]));

  update_epitaph($tombid, $_POST["epitaph"]);
  $tombstone["epitaph"] = $_POST["epitaph"];

  $message = "<p style=\"color:green;\">Epitaph updated.</p>";
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Graveyard &gt; <?= $tombstone['petname'] ?> &gt; Change Epitaph</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?= ($message ? $message : "") ?>
<?php
  if($tombstone['petid'] == 0)
    echo '<h4><a href="graveyard.php">The Graveyard</a> &gt; ' . $tombstone['petname'] . ' &gt; Change Epitaph</h4>';
  else
    echo '<h4><a href="graveyard.php">The Graveyard</a> &gt; <a href="/petprofile.php?petid=' . $tombstone['petid'] . '">' . $tombstone['petname'] . '</a> &gt; Change Epitaph</h4>';
?>
     <form action="editepitaph.php?id=<?= $tombid ?>" method="post">
     <table>
      <tr>
       <td><img src="gfx/pets/dead/tombstone_<?= $tombstone['tombstone'] < 10 ? (0 . $tombstone['tombstone']) : $tombstone['tombstone'] ?>.png" width="48" height="48" /></td>
       <td><?= $tombstone['petname'] ?></td>
      </tr>
      <tr>
       <th>Epitaph:</th>
       <td><input name="epitaph" value="<?= $tombstone["epitaph"] ?>" maxlength="64" style="width:400px;" /></td>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="update" /><input type="submit" value="Update" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
