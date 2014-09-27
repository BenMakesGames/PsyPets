<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/sketchbooklib.php';

$id = (int)$_GET['id'];

if($id > 0)
{
  $sketch = get_sketch_byid($id);

  if($sketch !== false && $sketch['userid'] != $user['idnum'])
  {
    header('Location: ./mysketchbook.php');
    exit();
  }
}
else
  $sketch = false;

if($sketch === false)
  $page_title = 'New Sketch';
else
  $page_title = 'Edit Sketch';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Sketchbook &gt; <?= $page_title ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';
?>
<h4><a href="mysketchbook.php">My Sketchbook</a> &gt; <?= $page_title ?></h4>
<div style="width:350px; padding:0; border: 1px solid #000;">
<applet code="main.PsyPetsSketch" java_codebase="./" archive="<?= $PSYPETS_SKETCH_VERSION ?>.jar" width="350" height="604">
 <param name="code" value="main.PsyPetsSketch">
 <param name="codebase" value="./">
 <param name="archive" value="<?= $PSYPETS_SKETCH_VERSION ?>.jar">
 <param name="type" value="application/x-java-applet;version=1.6">
 <param name="userid" value="<?= $user['idnum'] ?>" />
 <param name="sessionid" value="<?= $user['sessionid'] ?>" />
 <param name="sketchid" value="<?= (int)$sketch['idnum'] ?>" />
<?php
if($sketch !== false && $sketch['use_for_store'] == 'yes')
  echo '<param name="shopkeep" value="1" />';
?>
</applet>
</div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
