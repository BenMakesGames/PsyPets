<?php
function createImage()
{
  $string_a = array(
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M',
    'N', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
    '2', '3', '4', '5', '6', '7', '8', '9'
  );

  // file name!
  $fileRand = md5(rand(100000,999999));

  $keys = array_rand($string_a, 4);

  foreach($keys as $i)
    $string .= ' ' . $string_a[$v];

  $backgroundimage = 'gfx/secback.gif';

  $im = imagecreatefromgif($backgroundimage);

  $colour = imagecolorallocate($im, rand(0,128), rand(0,128), rand(0,128));
  $white  = imagecolorallocate($im, 255, 255, 255);
  $font   = 'commons/fonts/TELKS___.ttf';
  $angle  = rand(-5, 5) * 2;
  $size   = mt_rand(18, 20);

  // white border...
  imagettftext($im, $size, $angle, 6, 40 + $angle, $white, $font, $string);
  imagettftext($im, $size, $angle, 4, 40 + $angle, $white, $font, $string);
  imagettftext($im, $size, $angle, 5, 39 + $angle, $white, $font, $string);
  imagettftext($im, $size, $angle, 5, 41 + $angle, $white, $font, $string);

  // the text!
  imagettftext($im, $size, $angle, 5, 40 + $angle, $colour, $font, $string);

  $outfile = 'temp/' . $fileRand . '.gif';

  imagegif($im, $outfile);

  return $outfile;
}

echo '<img src="' . createImage() . '" name="secimg">';
?>
