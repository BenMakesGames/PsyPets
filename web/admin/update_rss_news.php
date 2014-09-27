<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

require_once 'commons/dbconnect.php';
require_once "commons/sessions.php";
require_once 'commons/rsslib.php';

if($user['admin']['mailpsypets'] == 'no')
{
  header('Location: /403.php');
  exit();
}

echo 'force-rendering XML file... ';

render_xml_latest_news();

echo 'done!  <a href="/rss_news.xml">rss_news.xml</a>' . "\n";
?>
