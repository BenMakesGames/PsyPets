<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = 'no';
$require_petload = 'no';
$reading_tos = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

$item = (int)$_GET['item'];

$command = 'SELECT * FROM monster_graphics WHERE idnum=' . $item . ' LIMIT 1';
$notice = fetch_single($command, 'fetching item contract');

if($notice === false)
{
  header('Location: /meta/copyright_smallgfx.php');
  exit();
}

/*
if($notice['rights'] != 'unlimited')
{
  header('Location: /meta/copyright_smallgfx.php');
  exit();
}
*/

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Help &gt; Copyright Information</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help</a> &gt; Copyright Information &gt; General Copyright Information</h4>
<ul class="tabbed">
 <li class="activetab"><a href="/meta/copyright.php">General Copyright Information</a></li>
 <li><a href="/meta/copyright_smallgfx.php">Item, Pet and Avatar Graphics</a></li>
 <li><a href="/meta/copyright_mahjong.php">Mahjong Graphics</a></li>
 <li><a href="/meta/copyright_largegfx.php">NPC Graphics</a></li>
 <li><a href="/meta/copyright_code.php">Code Libraries</a></li>
</ul>
     <table>
      <tr>
       <th>Graphic:</th>
       <td><img src="<?= $notice['graphic'] ?>" /></td>
      </tr>
<?php
if($notice['title'] != '')
{
  echo '
      <tr>
       <th>Title:</th>
       <td>' . $notice['title'] . '</td>
      </tr>
  ';
}
?>
      <tr>
       <th>Artist:</th>
       <td><?= str_replace(',', ', ', $notice['names']) ?></td>
      </tr>
     </table>
<?php
if($notice['rights'] == 'unlimited')
{
?>
<h5>Agreement</h5>
<p>The artist agreed, upon uploading the graphic, to the following:</p>
<ul>
 <li>You are the copyright holder for the graphic.</li>
 <li>You are giving PsyPets the right to reproduce the graphic without limitation within the PsyPets game, forever.</li>
 <li>Sharing the graphic in this way does not conflict with any other contract you may have entered.</li>
</ul>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
