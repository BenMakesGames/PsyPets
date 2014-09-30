<?php
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once 'commons/formatting.php';

if($user['user'] != 'telkoth')
{
  header('Location: ./404.php');
  exit();
}

$command = 'SELECT idnum,monster_mail.from,monster_mail.to,subject,message FROM monster_mail WHERE message LIKE \'%{img %\'';
$yay = $database->FetchMultiple($command, 'admin_hack');

?>
<html>
 <head>
  <title>PsyPets &gt; <?= $user['display'] ?>'s House &gt; <?= $sayroom ?> Room</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
 
<table>
<?php
foreach($yay as $message)
{
?>
<tr class="leftbar">
 <td>#<?= $message['idnum'] ?></td>
 <td><?= $message['from'] ?></td>
 <td><?= $message['to'] ?></td>
</tr>
<tr class="leftbar">
 <td colspan="3"><?= $message['subject'] ?></td>
</tr>
<tr>
 <td colspan="3"><?= format_text($message['message']) ?></td>
</tr>
<?php
}
?>
</table>
 </body>
</html>
