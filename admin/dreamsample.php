<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

require_once 'commons/admincheck.php';

// confirm the session...
require_once 'commons/dbconnect.php';

require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/dreamlib.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /');
  exit();
}

$pet = $database->FetchSingle('
  SELECT *
  FROM monster_pets
  WHERE last_love>=' . (time() - 24 * 60 * 60) . '
  ORDER BY RAND()
  LIMIT 1
');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Dream Samples</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Dream Samples</h4>
  <p>Using <a href="/petprofile.php?petid=<?= $pet['idnum'] ?>"><?= $pet['petname'] ?></a> to generate the dreams...</p>
  <ul>
<?php
for($i = 0; $i <= 20; ++$i)
  echo '<li>' . dream_description($pet) . '</li>';
?>
  </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
?>