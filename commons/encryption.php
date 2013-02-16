<?php
function random_password()
{
  $characters = 'abcdefghijkmnopqrstuvwxyz123456789ABCDEFGHJKLMNPQRSTUVWXYZ?!.-_+$#';

  $length = mt_rand(20, 30);

  $p = '';

  for($x = 0; $x < $length; ++$x)
    $p .= substr($characters, mt_rand(0, strlen($characters) - 1), 1);

  return $p;
}
 
$DIGITS = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

function RandomKey($length)
{
  global $DIGITS;

  $keyset = substr($DIGITS, 0, 16);

  $key = "";

  for($i = 0; $i < $length; ++$i)
    $key .= $keyset{rand(0, strlen($keyset) - 1)};

  return $key;
}?>
