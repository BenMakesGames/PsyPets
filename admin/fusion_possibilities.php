<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/chemistrylib.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /');
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Fusion Possibilities</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Fusion Possibilities</h4>
<?php
echo '<ul>';

foreach($PERIODIC_TABLE as $mass=>$element)
{
  foreach($PERIODIC_TABLE as $mass2=>$element2)
  {
    if($mass2 < $mass) continue;

    if(array_key_exists($mass + $mass2, $PERIODIC_TABLE))
      echo '<li>', $element, ' + ', $element2, ' = ', $PERIODIC_TABLE[$mass + $mass2], '</li>';
    
  }
}

echo '</ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
