<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['managedonations'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$command = 'SELECT COUNT(idnum) AS c FROM psypets_favor_history WHERE value<0';
$data = $database->FetchSingle($command, 'fetching favor history');

$favor_count = (int)$data['c'];

$num_pages = ceil($favor_count / 50);

$page = (int)$_GET['page'];
if($page < 1 || $page > $num_pages)
  $page = 1;

$command = 'SELECT * FROM psypets_favor_history WHERE value<0 ORDER BY idnum DESC LIMIT ' . (($page - 1) * 50) . ',50';
$favors = $database->FetchMultiple($command, 'fetching favor history');

require 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Administrative Tools &gt; Favor Management</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Favor Management</h4>
     <ul><li><a href="/admin/newfavor.php">Record new favor</a></li></ul>
<?php
if($error_message)
  echo "<p style=\"color:red;\">" . $error_message . "</p>\n";

$page_list = paginate($num_pages, $page, 'adminfavors.php?page=%s');
?>
     <?= $page_list ?>
     <table>
      <tr class="titlerow">
       <th>When</th>
       <th></th>
       <th>Login Name</th>
       <th>Favor</th>
       <th>Description</th>
       <th></th>
      </tr>
<?php
$bgcolor = begin_row_class();

foreach($favors as $favor)
{
  if($favor["timestamp"] == 0)
    $timestamp = "<i>unknown</i>";
  else
    $timestamp = duration($now - $favor['timestamp'], 2) . ' ago';

  $resident = get_user_byid($favor['userid'], 'display,user');
?>
      <tr class="<?= $bgcolor ?>">
       <td><?= $timestamp ?></td>
       <td><a href="/admin/resident.php?user=<?= $resident['user'] ?>"><img src="/gfx/search.gif" /></a></td>
       <td><?= resident_link($resident['display']) ?></td>
       <td align="right"><?= $favor['value'] ?></td>
       <td><?= $favor['favor'] ?></td>
       <td><?= $favor['itemid'] > 0 ? '#' . $favor['itemid'] : '' ?></td>
      </tr>
<?php

  $bgcolor = alt_row_class($bgcolor);
}
?>
     </table>
     <?= $page_list ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
