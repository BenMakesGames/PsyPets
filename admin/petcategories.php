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
require_once 'commons/petgraphics.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$petgfx = $PET_GRAPHICS;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Pet Graphic Categories</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Pet Graphic Categories</h4>
<?php
if($error_message)
  echo '<p class="failure">', $error_message, '</p>';
?>
<table>
 <tr class="titlerow">
  <th class="centered">Pet</th>
  <th></th>
  <th>Offspring</th>
 </tr>
<?php
$rowclass = begin_row_class();

foreach($petgfx as $i=>$graphic)
{
?>
 <tr class="<?= $rowclass ?>">
  <td class="centered"><img src="/gfx/pets/<?= $graphic ?>" /><br /><span class="size8"><?= $graphic ?></span></td>
  <td><img src="/gfx/lookright.gif" /></td>
  <td><img src="/gfx/pets/<?= implode('" /><img src="/gfx/pets/', matching_graphics($graphic)) ?>" /></td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
