<?php
require_once 'commons/init.php';

$require_petload = 'no';

require_once 'commons/admincheck.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/chemistrylib.php';
require_once 'commons/cachelib.php';

if($admin['seeserversettings'] != 'yes')
{
  header('Location: /');
  exit();
}

if($_POST['action'] == 'Flush Memcached')
{
  if(Cache::$MEMCACHE_SERVER->flush())
      $CONTENT['messages'][] = 'Memcached server has been flushed.';
    else
        $CONTENT['messages'][] = 'Could not flush memcached server :(';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Memcached</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Memcached</h4>
<?php
$stats = Cache::$MEMCACHE_SERVER->getStats();

echo '<table>';
$rowclass = begin_row_class();
foreach($stats as $stat=>$value)
{
  echo '<tr class="' . $rowclass . '"><th>' . $stat . '</th><td>' . $value . '</td></tr>';
  $rowclass = alt_row_class($rowclass);
}
echo '</table>';

$lookup = 'item by id:1';

$key = md5($lookup);
?>
<form method="post"><p><input type="submit" class="bigbutton" name="action" value="Flush Memcached" /></p></form>
<?php
$get_result = Cache::$MEMCACHE_SERVER->get($key);
echo '<h4>TEST: memcached[' . $key . '] (' . $lookup . ')</h4>';

echo '<pre>';
print_r($get_result);
echo '</pre>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
