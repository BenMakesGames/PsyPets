<?php
$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';

$command = 'SELECT display FROM monster_users WHERE is_admin=\'yes\'';
$admins = $database->FetchMultiple($command, 'fetching admin accounts');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Contact an Administrator</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Contact an Administrator</h4>
     <?php if(strlen($message) > 0) echo '<p>' . $message . '</p>'; ?>
     <ul class="spacedlist">
      <li>To report an error or bug regarding the game as a whole, use the <a href="/viewplaza.php?plaza=4">Error Reporting</a> section of <a href="/plaza.php">The Plaza</a>.  However, <em>errors specific to your account</em> should be reported directly to an administrator.</li>
      <li>If you have a question about playing the game, please refer to the <a href="/viewplaza.php?plaza=2">Question & Answer</a> section <a href="/plaza.php">The Plaza</a>.  Questions of this nature tend to be ignored by administrators.</li>
      <li>To report a violation of the <a href="/meta/termsofservice.php">Terms of Service</a> in the Plaza forums (including copyright-infringing material and pornography), use the link at the bottom of the page of the relevant Plaza thread.</li>
      <li>If you have an idea about how to make the game better, feel free to submit it to the <a href="/viewplaza.php?plaza=3">Game Ideas</a> section of <a href="/plaza.php">The Plaza</a>.</li>
     </ul>
     <h5>Administrators</h5>
     <table>
      <tr class="titlerow"><th></th><th>Name</th></tr>
<?php
$class = begin_row_class();

foreach($admins as $admin)
  echo '<tr class="' . $class . '"><td><a href="/writemail.php?sendto=' . urlencode($admin['display']) . '"><img src="/gfx/sendmail.gif" alt="send mail" border="0" /></a></td><td>' . resident_link($admin['display']) . '</td></tr>';
?>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
