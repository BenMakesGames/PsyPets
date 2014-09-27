<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

require_once 'commons/admincheck.php';

// confirm the session...
require_once 'commons/dbconnect.php';

require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['seeserversettings'] != 'yes')
{
  header('Location: /');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Server Settings</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
   #phpinfo table { background-color: #ccc; }
   #phpinfo .h { background-color: #888; }
   #phpinfo .e { font-weight: bold; }
   #phpinfo table td { border-bottom: 1px solid #000; }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Server Settings</h4>
<p><?= $_SERVER['DOCUMENT_ROOT'] ?></p>
<?php
ob_start();
phpinfo();
$phpinfo = ob_get_contents();
ob_end_clean();

$phpinfo = preg_replace('/<style type="text\/css">[^<]*<\/style>/', '', $phpinfo);

echo '<div id="phpinfo">' . $phpinfo . '</div>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
?>