<?php
$require_petload = 'no';

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';

$id = (int)$_GET['id'];
$itemid = (int)$_GET['item'];

$item = get_inventory_byid($itemid);

$image_type = 'image/png';

$file_name = 'gfx/404.png';

if($item['user'] == $user['user'])
{
  if($item['itemname'] == 'The Passage of Time' && $id == 1)
    $file_name = 'actions/recipes/gfx/gate.png';
  else if($item['itemname'] == 'Illustration of the Hollow Earth' && $id == 2)
    $file_name = 'gfx/hollowearth_full.png';
  else if($id == 3)
  {
    if($item['itemname'] == 'Shooting Star Painting')
    {
      $image_type = 'image/jpeg';
      $file_name = 'gfx/art/shooting_star_640x480_jpeg90.jpg';
    }
    else if($item['itemname'] == 'Portrait of Nobility')
    {
      $image_type = 'image/jpeg';
      $file_name = 'gfx/art/louis_xiv.jpg';
    }
    else if($item['itemname'] == 'Quetzalcoatl')
      $file_name = 'gfx/art/quetzalcoatl.png';
    else if($item['itemname'] == 'A Doodle of a Noodle Canoodled By a Poodle')
      $file_name = 'gfx/art/doodle.png';
  }
  else if($id == 4 && $item['itemname'] == 'The Imp')
    $file_name = 'gfx/books/theimp.png';
}
else
  $file_name = 'gfx/403.png';

header('Content-type: ' . $image_type);

echo file_get_contents($file_name);
?>
