<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

require_once 'libraries/db_messages.php';

$old_messages = get_all_db_messages($user['idnum']);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; Event History</title>
<?php include "commons/head.php"; ?>
  <style type="text/css">
   .flash-message .success { color: #fff; }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4>My Account &gt; Event History</h4>
  <table>
   <tbody>
<?php
foreach($old_messages as $message)
{
?>
    <tr>
     <td class="dim"><?= duration($now - $message['timestamp'], 1) ?> ago</td>
     <td><div class="flash-message <?= $DB_MESSAGE_CATEGORY_NAMES[$message['category']] ?>"><?= $message['message'] ?></div></td>
    </tr>
<?php
}
?>
   </tbody>
  </table>
 <?php include 'commons/footer_2.php'; ?>
 </body>
</html>
