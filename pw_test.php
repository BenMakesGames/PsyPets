<?php
//header('Location: ./tempindex.php');
//exit();

if(date('Y M d') == '2008 Apr 01')
{
  header('Location: ./index-apr1-lol.php');
  exit();
}

$require_login = 'no';
$invisible = 'yes';
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/messages.php';
require_once 'commons/ip.php';

$PAGE['force_ad'] = true;
$user['user'] = 'telkoth';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Home</title>
<?php include 'commons/head.php'; ?>
  <link href="rss_news.xml" rel="alternate" type="application/rss+xml" title="<?= $SETTINGS['site_name'] ?> Latest News" />
  <link rel="signup" href="signup.php" title="Sign Up" />
  <style type="text/css">
   /* hacked-in styling for Project Wonderful testing... */ 
   #topbg { display: none; }
   
   #adbox { overflow: visible; }
   body { background: #69c; }
  </style>
 </head>
 <body>
<?php
include 'commons/header_2.php';
?>
  <p>Testing, testing...</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
