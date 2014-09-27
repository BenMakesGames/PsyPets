<?php
$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/polllib.php';
require_once 'commons/globals.php';

$current_poll = get_global('currentpoll');

$polls = get_polls();

$current = get_poll_byid($current_poll);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Polls</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Polls</h4>
<?php
if($error_message)
  echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
<ul><li><a href="pollstandalone.php?id=<?= $current_poll ?>">Take the current poll, "<?= $current['title'] ?>"</li></ul>
<table>
 <tr class="titlerow">
  <th></th><th>Poll</th>
 </tr>
<?php
$rowclass = begin_row_class();

foreach($polls as $poll)
{
?>
 <tr class="<?= $rowclass ?>">
  <td><a href="/polldetails.php?id=<?= $poll['idnum'] ?>"><img src="/gfx/search.gif" border="0" alt="" /></a></td>
  <td><?= $poll['title'] ?></td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
