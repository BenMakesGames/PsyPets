<?php
function imagetograyscale(&$im)
{
  if(imageistruecolor($im))
    imagetruecolortopalette($im, false, 256);

  for($c = 0; $c < imagecolorstotal($im); $c++)
  {
    $col = imagecolorsforindex($im, $c);
    $gray = round(0.299 * $col['red'] + 0.587 * $col['green'] + 0.114 * $col['blue']);
    imagecolorset($im, $c, $gray, $gray, $gray);
  }
}
/*
$img = imagecreatefrompng('gfx/pets/chinesedragon.png');

imagetograyscale($img);

header('Content-type: image/png');

imagepng($img);
*/

function get_pet_graphics($dir)
{
  $files = array();
  $d = dir($dir);
  while(false !== ($entry = $d->read()))
  {
    if($entry == '.' || $entry == '..')
      continue;

    if(is_dir($dir . $entry))
      $files = array_merge($files, get_pet_graphics($dir . $entry . '/'));
    else
      $files[] = $dir . $entry;
  }

  return $files;
}


$files = get_pet_graphics('gfx/library/');

foreach($files as $file)
{
  list($w, $h, $t, $a) = getimagesize($file);
  
  if($w != 48 || $h != 48)
    continue;

  $name = explode('.', $file);
  $bw_name = $name[0] . '_bw.png';
  
  if($name[1] == 'png')
    $img = imagecreatefrompng($file);
  else if($name[1] == 'gif')
    $img = imagecreatefromgif($file);
  else if($name[1] == 'jpg' || $name[1] == 'jpeg')
    $img = imagecreatefromjpeg($file);
  else if($name[1] == 'bmp')
    $img = imagecreatefromwbmp($file);

  imagetograyscale($img);

  imagepng($img, $bw_name);
}
?>
