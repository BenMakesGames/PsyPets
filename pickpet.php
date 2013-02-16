<?php
require_once "commons/dbconnect.php";
require_once "commons/globals.php";

$petgfx = get_global('petgfx');
?>
<html>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pick a Pet</title>
 </head>
 <body style="border: 0; margin: 0; padding: 0;">
  <table border="0" cellspacing="0" cellpadding="4">
   <tr>
<?php
$i = 0;
foreach($petgfx as $gfx)
{
  $i++;
?>
    <td align="center"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/pets/<?= $gfx ?>" width="48" height="48" alt="" /><br /><input type="radio" name="petselect" onclick="parent.document.getElementById('picture').value='<?= $gfx ?>'" <?= $_GET['sel'] == $gfx ? 'checked ' : '' ?>/></td>
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
</html>
