<?php
require_once "commons/dbconnect.php";
require_once "commons/petgraphics.php";
require_once 'commons/petlib.php';

$petgfx = $PET_GRAPHICS;

$petid = (int)$_GET['petid'];
$pet = get_pet_byid($petid);

if($pet === false)
  die('what pet?');

$gfx_index = array_search($pet['graphic'], $petgfx);

if($pet['graphic'] == 'unicorn.png' || $pet['graphic'] == 'unicorn_candy.png' || $pet['graphic'] == 'unicorn_citron.png' || $pet['graphic'] == 'unicorn_sopretty.png')
  array_unshift($petgfx, 'unicorn_sopretty.png');

if($gfx_index === false)
{
  $gfx_index = 0;
  array_unshift($petgfx, $pet['graphic']);
}
?>
<html>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pick a Pet</title>
 </head>
 <body style="border: 0; margin: 0; padding: 0;">
  <table>
   <tr>
<?php
$i = 0;
foreach($petgfx as $gfx)
{
  $i++;
?>
    <td align="center"><img src="gfx/pets/<?= $gfx ?>" width="48" height="48" alt="" /><br /><input type="radio" name="petselect" onclick="parent.document.getElementById('picture').value='<?= $gfx ?>'" <?= $_GET['sel'] == $gfx ? 'checked ' : '' ?>/></td>
<?php
  if($i % 4 == 0)
  {
?>
   </tr>
   <tr>
<?php
  }
}
?>
   </tr>
  </table>
 </body>
<?php
